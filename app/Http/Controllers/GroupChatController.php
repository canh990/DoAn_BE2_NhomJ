<?php

namespace App\Http\Controllers;

use App\Events\ChatTyping;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * GroupChatController xử lý hành vi chat nhóm.
 *
 * - index(): hiển thị màn hình chat nhóm
 * - store(): tạo cuộc trò chuyện nhóm mới
 * - messages(): trả về tin nhắn nhóm qua AJAX
 * - storeMessage(): lưu tin nhắn nhóm mới
 * - toggleMute(): bật/tắt thông báo nhóm
 */
class GroupChatController extends Controller
{
    /**
     * Hiển thị bảng điều khiển chat nhóm với danh sách nhóm và tin nhắn nhóm đang hoạt động.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $users = User::query()
            ->whereKeyNot($currentUser->id)
            ->orderBy('ten_dang_nhap')
            ->get();

        $groups = Conversation::query()
            ->where('loai', 'nhom')
            ->whereHas('members', fn ($query) => $query->whereKey($currentUser->id))
            ->with(['members', 'messages' => fn ($query) => $query->latest('ngay_tao')->limit(1)])
            ->orderByDesc('ngay_cap_nhat')
            ->get();

        $activeGroup = null;
        $messages = collect();

        if ($request->filled('group_id')) {
            $activeGroup = $groups->firstWhere('id', (int) $request->input('group_id'));
        } elseif ($groups->isNotEmpty()) {
            $activeGroup = $groups->first();
        }

        if ($activeGroup instanceof Conversation) {
            $messages = $activeGroup->messages()
                ->with(['sender', 'media'])
                ->orderBy('ngay_tao')
                ->get();
        }

        $activeGroupMuted = $activeGroup instanceof Conversation
            ? (bool) optional($activeGroup->members->firstWhere('id', $currentUser->id)?->pivot)->tat_thong_bao
            : false;

        return view('message.group', compact('currentUser', 'users', 'groups', 'activeGroup', 'messages', 'activeGroupMuted'));
    }

    /**
     * Tạo cuộc trò chuyện nhóm mới và thêm các thành viên được chọn.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'ten_nhom' => ['required', 'string', 'max:100'],
            'anh_nhom' => ['nullable', 'image', 'max:2048'],
            'member_ids' => ['required', 'array', 'min:1'],
            'member_ids.*' => ['integer', 'exists:nguoi_dung,id'],
        ], [
            'ten_nhom.required' => 'Nhap ten nhom.',
            'member_ids.required' => 'Chon it nhat 1 thanh vien.',
            'member_ids.min' => 'Chon it nhat 1 thanh vien.',
        ]);

        $currentUser = Auth::user();
        $memberIds = collect($data['member_ids'])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id !== $currentUser->id)
            ->unique()
            ->values();

        abort_if($memberIds->isEmpty(), 422);

        $avatarPath = null;
        if ($request->hasFile('anh_nhom')) {
            $avatar = $request->file('anh_nhom');
            $filename = Str::uuid().'.'.$avatar->getClientOriginalExtension();
            $avatar->move(public_path('uploads/group-avatars'), $filename);
            $avatarPath = 'uploads/group-avatars/'.$filename;
        }

        $group = Conversation::create([
            'loai' => 'nhom',
            'ten_nhom' => $data['ten_nhom'],
            'anh_nhom' => $avatarPath,
        ]);

        $group->members()->attach([
            $currentUser->id => ['vai_tro' => 'quan_tri'],
        ]);

        $group->members()->attach(
            $memberIds->mapWithKeys(fn ($id) => [$id => ['vai_tro' => 'thanh_vien']])->all()
        );

        return redirect()
            ->route('chat.groups.index', ['group_id' => $group->id])
            ->with('status', 'Da tao nhom '.$group->ten_nhom.'.');
    }

    /**
     * Trả về JSON cho tin nhắn nhóm. Chỉ thành viên của nhóm được truy cập.
     */
    public function messages(Conversation $conversation)
    {
        $this->authorizeGroupMember($conversation);

        $currentUser = Auth::user();
        $messages = $conversation->messages()
            ->with(['sender', 'media'])
            ->orderBy('ngay_tao')
            ->get();

        return response()->json([
            'messages' => $messages->map(fn (Message $message) => $this->formatMessage($message, $currentUser->id))->values(),
        ]);
    }

    /**
     * Lưu tin nhắn mới vào cuộc trò chuyện nhóm.
     */
    public function storeMessage(Request $request, Conversation $conversation)
    {
        $this->authorizeGroupMember($conversation);

        $data = $request->validate([
            'noi_dung' => ['nullable', 'string', 'max:5000', 'required_without:attachments'],
            'attachments' => ['nullable', 'array', 'max:6'],
            'attachments.*' => ['file', 'max:20480', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mp3,wav,ogg,m4a,weba,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar'],
        ], [
            'noi_dung.required_without' => 'Nhap tin nhan hoac chon tep de gui.',
            'attachments.*.max' => 'Moi tep toi da 20MB.',
            'attachments.*.mimes' => 'Chi ho tro anh, video, am thanh va cac tep pho bien.',
        ]);

        $message = Message::create([
            'cuoc_tro_chuyen_id' => $conversation->id,
            'nguoi_gui_id' => Auth::id(),
            'noi_dung' => filled($data['noi_dung'] ?? null) ? trim($data['noi_dung']) : null,
        ]);

        $this->storeAttachments($message, $request);

        $conversation->touch();
        $this->forgetTypingUser($conversation, Auth::id());
        $this->broadcastTyping($conversation, $this->typingUserPayload(Auth::user()), false);
        $this->notifyUnmutedMembers($conversation, Auth::id());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->formatMessage($message->load(['sender', 'media']), Auth::id()),
            ]);
        }

        return redirect()->route('chat.groups.index', ['group_id' => $conversation->id]);
    }

    /**
     * Chuyển trạng thái tắt/bật thông báo cho nhóm.
     */
    public function toggleMute(Request $request, Conversation $conversation)
    {
        $this->authorizeGroupMember($conversation);

        $currentUserId = Auth::id();
        $muted = ! (bool) $conversation->members()
            ->whereKey($currentUserId)
            ->firstOrFail()
            ->pivot
            ->tat_thong_bao;

        $conversation->members()->updateExistingPivot($currentUserId, [
            'tat_thong_bao' => $muted,
        ]);

        $message = $muted
            ? 'Da tat thong bao nhom '.$conversation->ten_nhom.'.'
            : 'Da bat lai thong bao nhom '.$conversation->ten_nhom.'.';

        if ($request->expectsJson()) {
            return response()->json([
                'muted' => $muted,
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }

    public function typingUsers(Conversation $conversation)
    {
        $this->authorizeGroupMember($conversation);

        return response()->json([
            'conversation_id' => $conversation->id,
            'users' => $this->activeTypingUsers($conversation, Auth::id()),
        ]);
    }

    public function startTyping(Conversation $conversation)
    {
        $this->authorizeGroupMember($conversation);

        $typingUser = $this->typingUserPayload(Auth::user());
        $this->rememberTypingUser($conversation, $typingUser);
        $this->broadcastTyping($conversation, $typingUser, true);

        return response()->json([
            'conversation_id' => $conversation->id,
            'typing' => true,
        ]);
    }

    public function stopTyping(Conversation $conversation)
    {
        $this->authorizeGroupMember($conversation);

        $typingUser = $this->typingUserPayload(Auth::user());
        $this->forgetTypingUser($conversation, Auth::id());
        $this->broadcastTyping($conversation, $typingUser, false);

        return response()->json(['typing' => false]);
    }

    /**
     * Dừng nếu người dùng hiện tại không phải thành viên của nhóm yêu cầu.
     */
    private function authorizeGroupMember(Conversation $conversation): void
    {
        abort_unless(
            $conversation->loai === 'nhom' && $conversation->members()->whereKey(Auth::id())->exists(),
            403
        );
    }

    /**
     * Thông báo cho các thành viên nhóm chưa tắt thông báo.
     */
    private function notifyUnmutedMembers(Conversation $conversation, int $senderId): void
    {
        $members = $conversation->members()
            ->where('nguoi_dung.id', '!=', $senderId)
            ->wherePivot('tat_thong_bao', false)
            ->get();

        foreach ($members as $member) {
            \App\Models\ThongBao::create([
                'nguoi_dung_id' => $member->id,
                'nguoi_thuc_hien_id' => $senderId,
                'loai' => 'tin_nhan_nhom',
                'cuoc_tro_chuyen_id' => $conversation->id,
                'ngay_tao' => now(),
            ]);
        }
    }

    private function activeTypingUsers(Conversation $conversation, int $currentUserId): array
    {
        $users = $this->currentTypingMap($conversation);
        unset($users[$currentUserId]);

        return array_values($users);
    }

    private function rememberTypingUser(Conversation $conversation, array $user): void
    {
        $users = $this->currentTypingMap($conversation);
        $users[$user['id']] = array_merge($user, [
            'expires_at' => now()->addSeconds(4)->timestamp,
        ]);

        Cache::put($this->typingCacheKey($conversation), $users, now()->addMinutes(5));
    }

    private function forgetTypingUser(Conversation $conversation, int $userId): void
    {
        $users = $this->currentTypingMap($conversation);
        unset($users[$userId]);

        Cache::put($this->typingCacheKey($conversation), $users, now()->addMinutes(5));
    }

    private function currentTypingMap(Conversation $conversation): array
    {
        $now = now()->timestamp;
        $users = Cache::get($this->typingCacheKey($conversation), []);

        return collect($users)
            ->filter(fn ($user) => ($user['expires_at'] ?? 0) > $now)
            ->all();
    }

    private function typingCacheKey(Conversation $conversation): string
    {
        return 'chat_typing:'.$conversation->id;
    }

    private function typingUserPayload(User $user): array
    {
        $name = $user->ten_dang_nhap ?: ($user->email ?: 'Thanh vien');

        return [
            'id' => $user->id,
            'name' => $name,
            'initial' => mb_strtoupper(mb_substr($name, 0, 1)),
            'avatar_url' => $user->anh_dai_dien ? asset('storage/'.$user->anh_dai_dien) : null,
        ];
    }

    private function broadcastTyping(Conversation $conversation, array $user, bool $typing): void
    {
        try {
            broadcast(new ChatTyping($conversation->id, $user, $typing))->toOthers();
        } catch (\Throwable) {
            // HTTP polling keeps typing indicators working when broadcasting is not configured locally.
        }
    }

    /**
     * Lưu tệp đính kèm được tải lên cho tin nhắn nhóm.
     */
    private function storeAttachments(Message $message, Request $request): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        $directory = public_path('uploads/message-media');
        File::ensureDirectoryExists($directory);

        foreach ($request->file('attachments') as $file) {
            if (! $file->isValid()) {
                continue;
            }

            $mediaType = $this->mediaType($file->getMimeType());
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid().($extension ? '.'.$extension : '');
            $file->move($directory, $filename);

            $message->media()->create([
                'loai' => $mediaType,
                'duong_dan' => 'uploads/message-media/'.$filename,
            ]);
        }
    }

    /**
     * Xác định loại tệp đính kèm từ MIME type.
     */
    private function mediaType(?string $mimeType): string
    {
        if (Str::startsWith((string) $mimeType, 'image/')) {
            return 'hinh_anh';
        }

        if (Str::startsWith((string) $mimeType, 'video/')) {
            return 'video';
        }

        if (Str::startsWith((string) $mimeType, 'audio/')) {
            return 'am_thanh';
        }

        return 'tap_tin';
    }

    public function deleteMessage(Request $request, Message $message)
    {
        $currentUser = Auth::user();
        
        // Check if current user is the sender
        abort_if($message->nguoi_gui_id !== $currentUser->id, 403);
        
        // Check if user is part of the group
        abort_unless(
            $message->conversation->loai === 'nhom' && 
            $message->conversation->members()->whereKey($currentUser->id)->exists(),
            403
        );

        $data = $request->validate([
            'type' => ['required', 'in:ca_nhan,ca_hai'],
        ]);

        $message->update(['kieu_xoa' => $data['type']]);

        return response()->json([
            'message' => $this->formatMessage($message, $currentUser->id),
        ]);
    }

    /**
     * Định dạng tin nhắn nhóm để trả về JSON.
     */
    private function formatMessage(Message $message, int $currentUserId): array
    {
        $isRecalledForBoth = $message->kieu_xoa === 'ca_hai';
        $isDeletedForMe = $message->kieu_xoa === 'ca_nhan' && $message->nguoi_gui_id === $currentUserId;
        
        return [
            'id' => $message->id,
            'sender_id' => $message->nguoi_gui_id,
            'sender_name' => $message->sender?->ten_dang_nhap ?: ($message->sender?->email ?: 'Thanh vien'),
            'content' => ($isRecalledForBoth || $isDeletedForMe) ? null : $message->noi_dung,
            'attachments' => ($isRecalledForBoth || $isDeletedForMe) ? [] : $message->media->map(fn ($media) => [
                'type' => $media->loai,
                'url' => asset($media->duong_dan),
                'name' => basename($media->duong_dan),
            ])->values(),
            'time' => optional($message->ngay_tao)->format('H:i'),
            'is_mine' => $message->nguoi_gui_id === $currentUserId,
            'is_recalled' => $isRecalledForBoth,
            'is_deleted' => $isDeletedForMe,
        ];
    }

    public function searchMessages(Request $request, Conversation $conversation)
    {
        $this->authorizeGroupMember($conversation);

        $currentUser = Auth::user();
        $data = $request->validate([
            'keyword' => ['required', 'string', 'min:1', 'max:255'],
        ], [
            'keyword.required' => 'Vui lòng nhập từ khóa tìm kiếm.',
            'keyword.min' => 'Từ khóa phải có ít nhất 1 ký tự.',
        ]);

        $keyword = trim($data['keyword']);
        $messages = $conversation->messages()
            ->with(['sender', 'media'])
            ->whereNotNull('noi_dung')
            ->where('noi_dung', 'like', '%' . $keyword . '%')
            ->where(function ($query) {
                $query->whereNull('kieu_xoa')
                      ->orWhere('kieu_xoa', '!=', 'ca_hai');
            })
            ->orderBy('ngay_tao', 'desc')
            ->paginate(20);

        return response()->json([
            'keyword' => $keyword,
            'total' => $messages->total(),
            'messages' => $messages->map(fn (Message $message) => $this->formatMessage($message, $currentUser->id))->values(),
            'current_page' => $messages->currentPage(),
            'last_page' => $messages->lastPage(),
        ]);
    }
}
