<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            }
        }

        return view('message.chat1-1', compact('currentUser', 'users', 'selectedUser', 'conversation', 'messages'));
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

        $conversation = $this->findPrivateConversation($currentUser->id, $friend->id)
            ?? $this->createPrivateConversation($currentUser->id, $friend->id);

        return redirect()
            ->route('chat.demo', ['user_id' => $friend->id])
            ->with('status', 'Da ket ban voi '.$friend->ten_dang_nhap.' va mo chat 1-1.');
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

    private function createMessage(Conversation $conversation, int $senderId, ?string $content, Request $request): Message
    {
        $message = Message::create([
            'cuoc_tro_chuyen_id' => $conversation->id,
            'nguoi_gui_id' => $senderId,
            'noi_dung' => filled($content) ? trim($content) : null,
        ]);

        $this->storeAttachments($message, $request);

        $conversation->touch();

        return $message->load('media');
    }

    private function validateMessageInput(Request $request, array $extraRules = []): array
    {
        return $request->validate($extraRules + [
            'noi_dung' => ['nullable', 'string', 'max:5000', 'required_without:attachments'],
            'attachments' => ['nullable', 'array', 'max:6'],
            'attachments.*' => ['file', 'max:20480', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mp3,wav,ogg,m4a,weba,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar'],
        ], [
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

    private function formatMessage(Message $message, int $currentUserId): array
    {
        return [
            'id' => $message->id,
            'sender_id' => $message->nguoi_gui_id,
            'content' => $message->noi_dung,
            'attachments' => $message->media->map(fn ($media) => [
                'type' => $media->loai,
                'url' => asset($media->duong_dan),
                'name' => basename($media->duong_dan),
            ])->values(),
            'time' => optional($message->ngay_tao)->format('H:i'),
            'is_mine' => $message->nguoi_gui_id === $currentUserId,
        ];
    }
}
