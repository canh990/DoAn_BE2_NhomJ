<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                        ->with('sender')
                        ->orderBy('ngay_tao')
                        ->get();
                }
            }
        }

        return view('message.chat1-1', compact('currentUser', 'users', 'selectedUser', 'conversation', 'messages'));
    }

    public function storeConversation(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:nguoi_dung,id'],
            'noi_dung' => ['required', 'string', 'max:5000'],
        ]);

        $currentUser = Auth::user();
        abort_if((int) $data['user_id'] === $currentUser->id, 422);

        $conversation = $this->findPrivateConversation($currentUser->id, (int) $data['user_id'])
            ?? $this->createPrivateConversation($currentUser->id, (int) $data['user_id']);

        $message = $this->createMessage($conversation, $currentUser->id, $data['noi_dung']);

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
        $data = $request->validate([
            'noi_dung' => ['required', 'string', 'max:5000'],
        ]);

        $currentUser = Auth::user();
        abort_unless($conversation->loai === 'ca_nhan' && $conversation->members()->whereKey($currentUser->id)->exists(), 403);

        $message = $this->createMessage($conversation, $currentUser->id, $data['noi_dung']);

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
            ? $conversation->messages()->orderBy('ngay_tao')->get()
            : collect();

        return response()->json([
            'conversation_id' => $conversation?->id,
            'messages' => $messages->map(fn (Message $message) => $this->formatMessage($message, $currentUser->id))->values(),
        ]);
    }

    public function storeUserMessage(Request $request, User $user)
    {
        $data = $request->validate([
            'noi_dung' => ['required', 'string', 'max:5000'],
        ]);

        $currentUser = Auth::user();
        abort_if($user->id === $currentUser->id, 422);

        $conversation = $this->findPrivateConversation($currentUser->id, $user->id)
            ?? $this->createPrivateConversation($currentUser->id, $user->id);

        $message = $this->createMessage($conversation, $currentUser->id, $data['noi_dung']);

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

    private function createMessage(Conversation $conversation, int $senderId, string $content): Message
    {
        $message = Message::create([
            'cuoc_tro_chuyen_id' => $conversation->id,
            'nguoi_gui_id' => $senderId,
            'noi_dung' => trim($content),
        ]);

        $conversation->touch();

        return $message;
    }

    private function formatMessage(Message $message, int $currentUserId): array
    {
        return [
            'id' => $message->id,
            'sender_id' => $message->nguoi_gui_id,
            'content' => $message->noi_dung,
            'time' => optional($message->ngay_tao)->format('H:i'),
            'is_mine' => $message->nguoi_gui_id === $currentUserId,
        ];
    }
}
