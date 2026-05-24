<?php

namespace App\Http\Controllers;

use App\Events\ChatTyping;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $users = User::query()
            ->whereKeyNot($currentUser->id)
            ->orderBy('ten_dang_nhap')
            ->get();

        $selectedUser = null;
        $conversation = null;
        $messages = collect();
        $muteStates = $this->privateMuteStates($currentUser->id);
        $activeUserMuted = false;

        if ($request->filled('user_id') || $users->isNotEmpty()) {
            $selectedUser = $request->filled('user_id')
                ? $users->firstWhere('id', (int) $request->input('user_id'))
                : $users->first();

            if ($selectedUser) {
                $conversation = $this->findPrivateConversation($currentUser->id, $selectedUser->id);

                if ($conversation) {
                    $messages = $conversation->messages()
                        ->with(['sender', 'media'])
                        ->orderBy('ngay_tao')
                        ->get();
                }

                $activeUserMuted = (bool) ($muteStates[$selectedUser->id] ?? false);
            }
        }

        return view('message.chat1-1', compact('currentUser', 'users', 'selectedUser', 'conversation', 'messages', 'muteStates', 'activeUserMuted'));
    }

    public function storeConversation(Request $request)
    {
        $data = $this->validateMessageInput($request, [
            'user_id' => ['required', 'integer', 'exists:nguoi_dung,id'],
        ]);

        $currentUser = Auth::user();
        abort_if((int) $data['user_id'] === $currentUser->id, 422);

        $conversation = $this->findPrivateConversation($currentUser->id, (int) $data['user_id'])
            ?? $this->createPrivateConversation($currentUser->id, (int) $data['user_id']);

        $message = $this->createMessage($conversation, $currentUser->id, $data['noi_dung'] ?? null, $request);

        if ($request->expectsJson()) {
            return response()->json([
                'conversation_id' => $conversation->id,
                'message' => $this->formatMessage($message, $currentUser->id),
            ]);
        }

        return redirect()->route('chat.demo', ['user_id' => $data['user_id']]);
    }

    public function storeMessage(Request $request, Conversation $conversation)
    {
        $data = $this->validateMessageInput($request);

        $currentUser = Auth::user();
        abort_unless($conversation->loai === 'ca_nhan' && $conversation->members()->whereKey($currentUser->id)->exists(), 403);

        $message = $this->createMessage($conversation, $currentUser->id, $data['noi_dung'] ?? null, $request);

        $otherUserId = $conversation->members()
            ->whereKeyNot($currentUser->id)
            ->value('nguoi_dung.id');

        if ($request->expectsJson()) {
            return response()->json([
                'conversation_id' => $conversation->id,
                'message' => $this->formatMessage($message, $currentUser->id),
            ]);
        }

        return redirect()->route('chat.demo', ['user_id' => $otherUserId]);
    }

    public function messagesForUser(User $user)
    {
        $currentUser = Auth::user();
        abort_if($user->id === $currentUser->id, 422);

        $conversation = $this->findPrivateConversation($currentUser->id, $user->id);
        $messages = $conversation
            ? $conversation->messages()->with('media')->orderBy('ngay_tao')->get()
            : collect();

        return response()->json([
            'conversation_id' => $conversation?->id,
            'messages' => $messages->map(fn (Message $message) => $this->formatMessage($message, $currentUser->id))->values(),
        ]);
    }

    public function storeUserMessage(Request $request, User $user)
    {
        $data = $this->validateMessageInput($request);

        $currentUser = Auth::user();
        abort_if($user->id === $currentUser->id, 422);

        $conversation = $this->findPrivateConversation($currentUser->id, $user->id)
            ?? $this->createPrivateConversation($currentUser->id, $user->id);

        $message = $this->createMessage($conversation, $currentUser->id, $data['noi_dung'] ?? null, $request);

        return response()->json([
            'conversation_id' => $conversation->id,
            'message' => $this->formatMessage($message, $currentUser->id),
        ]);
    }

    public function toggleUserMute(Request $request, User $user)
    {
        $currentUser = Auth::user();
        abort_if($user->id === $currentUser->id, 422);

        $conversation = $this->findPrivateConversation($currentUser->id, $user->id)
            ?? $this->createPrivateConversation($currentUser->id, $user->id);

        $muted = ! (bool) $conversation->members()
            ->whereKey($currentUser->id)
            ->firstOrFail()
            ->pivot
            ->tat_thong_bao;

        $conversation->members()->updateExistingPivot($currentUser->id, [
            'tat_thong_bao' => $muted,
        ]);

        $message = $muted
            ? 'Da tat thong bao tu '.$this->displayName($user).'.'
            : 'Da bat lai thong bao tu '.$this->displayName($user).'.';

        if ($request->expectsJson()) {
            return response()->json([
                'muted' => $muted,
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }

    public function typingUsersForUser(User $user)
    {
        $currentUser = Auth::user();
        abort_if($user->id === $currentUser->id, 422);

        $conversation = $this->findPrivateConversation($currentUser->id, $user->id);

        return response()->json([
            'conversation_id' => $conversation?->id,
            'users' => $conversation ? $this->typingUsers($conversation, $currentUser->id) : [],
        ]);
    }

    public function startTypingForUser(User $user)
    {
        $currentUser = Auth::user();
        abort_if($user->id === $currentUser->id, 422);

        $conversation = $this->findPrivateConversation($currentUser->id, $user->id)
            ?? $this->createPrivateConversation($currentUser->id, $user->id);

        $typingUser = $this->typingUserPayload($currentUser);
        $this->rememberTypingUser($conversation, $typingUser);
        $this->broadcastTyping($conversation, $typingUser, true);

        return response()->json([
            'conversation_id' => $conversation->id,
            'typing' => true,
        ]);
    }

    public function stopTypingForUser(User $user)
    {
        $currentUser = Auth::user();
        abort_if($user->id === $currentUser->id, 422);

        $conversation = $this->findPrivateConversation($currentUser->id, $user->id);

        if ($conversation) {
            $typingUser = $this->typingUserPayload($currentUser);
            $this->forgetTypingUser($conversation, $currentUser->id);
            $this->broadcastTyping($conversation, $typingUser, false);
        }

        return response()->json(['typing' => false]);
    }

    public function storeFriend(Request $request)
    {
        $data = $request->validate([
            'account' => ['required', 'string', 'max:255'],
        ], [
            'account.required' => 'Nhap email, so dien thoai hoac ten dang nhap can ket ban.',
        ]);

        $currentUser = Auth::user();
        $account = trim($data['account']);

        $friend = User::query()
            ->whereKeyNot($currentUser->id)
            ->where(function ($query) use ($account) {
                $query->where('email', $account)
                    ->orWhere('so_dien_thoai', $account)
                    ->orWhere('ten_dang_nhap', $account);
            })
            ->first();

        if (! $friend) {
            return back()
                ->withInput()
                ->withErrors(['account' => 'Khong tim thay tai khoan nay.']);
        }

        DB::table('theo_doi')->updateOrInsert(
            [
                'nguoi_theo_doi_id' => $currentUser->id,
                'nguoi_duoc_theo_doi_id' => $friend->id,
            ],
            [
                'trang_thai' => 'da_chap_nhan',
                'ngay_tao' => now(),
            ]
        );

        DB::table('theo_doi')->updateOrInsert(
            [
                'nguoi_theo_doi_id' => $friend->id,
                'nguoi_duoc_theo_doi_id' => $currentUser->id,
            ],
            [
                'trang_thai' => 'da_chap_nhan',
                'ngay_tao' => now(),
            ]
        );

        // Tạo thông báo kết bạn cho đối phương
        \App\Models\ThongBao::updateOrCreate(
            [
                'nguoi_dung_id' => $friend->id,
                'nguoi_thuc_hien_id' => $currentUser->id,
                'loai' => 'ket_ban',
            ],
            [
                'da_doc' => false,
                'ngay_tao' => now(),
            ]
        );

        $conversation = $this->findPrivateConversation($currentUser->id, $friend->id)
            ?? $this->createPrivateConversation($currentUser->id, $friend->id);

        return redirect()
            ->route('chat.demo', ['user_id' => $friend->id])
            ->with('status', 'Da ket ban voi '.$friend->ten_dang_nhap.' va mo chat 1-1.');
    }

    public function deleteMessage(Request $request, Message $message)
    {
        $currentUser = Auth::user();

        abort_if($message->nguoi_gui_id !== $currentUser->id, 403);
        abort_unless(
            $message->conversation->loai === 'ca_nhan'
            && $message->conversation->members()->whereKey($currentUser->id)->exists(),
            403
        );

        $data = $request->validate([
            'type' => ['required', 'in:ca_nhan,ca_hai'],
        ]);

        $message->update(['kieu_xoa' => $data['type']]);

        return response()->json([
            'message' => $this->formatMessage($message->load('media'), $currentUser->id),
        ]);
    }

    public function searchMessages(Request $request, Conversation $conversation)
    {
        $currentUser = Auth::user();
        abort_unless($conversation->loai === 'ca_nhan' && $conversation->members()->whereKey($currentUser->id)->exists(), 403);

        $data = $request->validate([
            'keyword' => ['required', 'string', 'min:1', 'max:255'],
        ], [
            'keyword.required' => 'Vui long nhap tu khoa tim kiem.',
            'keyword.min' => 'Tu khoa phai co it nhat 1 ky tu.',
        ]);

        $keyword = trim($data['keyword']);
        $messages = $conversation->messages()
            ->with(['sender', 'media'])
            ->whereNotNull('noi_dung')
            ->where('noi_dung', 'like', '%'.$keyword.'%')
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

    private function findPrivateConversation(int $firstUserId, int $secondUserId): ?Conversation
    {
        return Conversation::query()
            ->where('loai', 'ca_nhan')
            ->whereHas('members', fn ($query) => $query->whereKey($firstUserId))
            ->whereHas('members', fn ($query) => $query->whereKey($secondUserId))
            ->withCount('members')
            ->get()
            ->firstWhere('members_count', 2);
    }

    private function createPrivateConversation(int $firstUserId, int $secondUserId): Conversation
    {
        $conversation = Conversation::create(['loai' => 'ca_nhan']);
        $conversation->members()->attach([
            $firstUserId => ['vai_tro' => 'thanh_vien'],
            $secondUserId => ['vai_tro' => 'thanh_vien'],
        ]);

        return $conversation;
    }

    private function privateMuteStates(int $currentUserId)
    {
        return DB::table('thanh_vien_nhom as mine')
            ->join('cuoc_tro_chuyen as conversations', 'conversations.id', '=', 'mine.cuoc_tro_chuyen_id')
            ->join('thanh_vien_nhom as others', 'others.cuoc_tro_chuyen_id', '=', 'mine.cuoc_tro_chuyen_id')
            ->where('conversations.loai', 'ca_nhan')
            ->where('mine.nguoi_dung_id', $currentUserId)
            ->where('others.nguoi_dung_id', '!=', $currentUserId)
            ->pluck('mine.tat_thong_bao', 'others.nguoi_dung_id');
    }

    private function createMessage(Conversation $conversation, int $senderId, ?string $content, Request $request): Message
    {
        $message = Message::create([
            'cuoc_tro_chuyen_id' => $conversation->id,
            'nguoi_gui_id' => $senderId,
            'noi_dung' => filled($content) ? trim($content) : null,
        ]);

        $this->storeAttachments($message, $request);
        $conversation->touch();
        $this->forgetTypingUser($conversation, $senderId);

        $sender = Auth::user();
        if ($sender) {
            $this->broadcastTyping($conversation, $this->typingUserPayload($sender), false);
        }

        $otherMembers = $conversation->members()
            ->where('nguoi_dung.id', '!=', $senderId)
            ->wherePivot('tat_thong_bao', false)
            ->get();

        foreach ($otherMembers as $member) {
            \App\Models\ThongBao::create([
                'nguoi_dung_id' => $member->id,
                'nguoi_thuc_hien_id' => $senderId,
                'loai' => 'tin_nhan',
                'ngay_tao' => now(),
            ]);
        }

        return $message->load('media');
    }

    private function validateMessageInput(Request $request, array $rules = []): array
    {
        $defaultRules = [
            'noi_dung' => ['nullable', 'string', 'max:5000', 'required_without:attachments'],
            'attachments' => ['nullable', 'array', 'max:6'],
            'attachments.*' => ['file', 'max:20480', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mp3,wav,ogg,m4a,weba,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar'],
        ];

        return $request->validate(array_merge($defaultRules, $rules), [
            'noi_dung.required_without' => 'Nhap tin nhan hoac chon tep de gui.',
            'attachments.*.max' => 'Moi tep toi da 20MB.',
            'attachments.*.mimes' => 'Chi ho tro anh, video, am thanh va cac tep pho bien.',
        ]);
    }

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

    private function displayName(User $user): string
    {
        return $user->ten_dang_nhap ?: ($user->email ?: 'nguoi dung nay');
    }

    private function typingUsers(Conversation $conversation, int $currentUserId): array
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
        return [
            'id' => $user->id,
            'name' => $this->displayName($user),
            'initial' => mb_strtoupper(mb_substr($this->displayName($user), 0, 1)),
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

    private function formatMessage(Message $message, int $currentUserId): array
    {
        $isRecalledForBoth = $message->kieu_xoa === 'ca_hai';
        $isDeletedForMe = $message->kieu_xoa === 'ca_nhan' && $message->nguoi_gui_id === $currentUserId;

        return [
            'id' => $message->id,
            'sender_id' => $message->nguoi_gui_id,
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
}
