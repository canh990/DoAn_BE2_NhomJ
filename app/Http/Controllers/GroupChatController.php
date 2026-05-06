<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if ($activeGroup) {
            $messages = $activeGroup->messages()
                ->with('sender')
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
            ->with('sender')
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
            'noi_dung' => ['required', 'string', 'max:5000'],
        ]);

        $message = Message::create([
            'cuoc_tro_chuyen_id' => $conversation->id,
            'nguoi_gui_id' => Auth::id(),
            'noi_dung' => trim($data['noi_dung']),
        ]);

        $conversation->touch();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->formatMessage($message->load('sender'), Auth::id()),
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

    private function formatMessage(Message $message, int $currentUserId): array
    {
        return [
            'id' => $message->id,
            'sender_id' => $message->nguoi_gui_id,
            'sender_name' => $message->sender?->ten_dang_nhap ?: ($message->sender?->email ?: 'Thanh vien'),
            'content' => $message->noi_dung,
            'time' => optional($message->ngay_tao)->format('H:i'),
            'is_mine' => $message->nguoi_gui_id === $currentUserId,
        ];
    }
}
