<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GroupChatController extends Controller
{
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

        return view('message.group', compact('currentUser', 'users', 'groups', 'activeGroup', 'messages'));
    }

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

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->formatMessage($message->load(['sender', 'media']), Auth::id()),
            ]);
        }

        return redirect()->route('chat.groups.index', ['group_id' => $conversation->id]);
    }

    private function authorizeGroupMember(Conversation $conversation): void
    {
        abort_unless(
            $conversation->loai === 'nhom' && $conversation->members()->whereKey(Auth::id())->exists(),
            403
        );
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
}
