@extends('layouts.app')

@section('title', 'Nhóm')

@section('content')
    <style>
        .chat-surface { scrollbar-width: thin; scrollbar-color: #21455f #0b1220; }
        .avatar-ring { box-shadow: 0 0 0 1px rgba(125, 211, 252, .4), 0 12px 28px rgba(0, 0, 0, .28); }
        .chat-bg {
            background:
                radial-gradient(circle at 72% 18%, rgba(36, 88, 119, .18), transparent 26%),
                radial-gradient(circle at 40% 82%, rgba(27, 51, 80, .18), transparent 28%),
                #080d18;
        }
        .mute-glass {
            background: linear-gradient(135deg, rgba(15, 23, 42, .82), rgba(15, 23, 42, .58));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08), 0 18px 48px rgba(2, 8, 23, .22);
            backdrop-filter: blur(16px);
        }
        .typing-dot {
            animation: typing-bounce 1.2s infinite ease-in-out;
        }
        .typing-dot:nth-child(2) { animation-delay: .16s; }
        .typing-dot:nth-child(3) { animation-delay: .32s; }
        @keyframes typing-bounce {
            0%, 80%, 100% { transform: translateY(0); opacity: .38; }
            40% { transform: translateY(-4px); opacity: 1; }
        }
    </style>
    @php
        // Các hàm trợ giúp hiển thị tên người dùng/nhóm và tệp đính kèm
        $displayName = fn ($user) => $user->ten_hien_thi ?: ($user->ten_dang_nhap ?: ($user->email ?: 'Người dùng'));
        $avatarText = fn ($text) => mb_strtoupper(mb_substr($text ?: 'N', 0, 1));
        $groupAvatar = fn ($group) => $group->anh_nhom ? asset($group->anh_nhom) : null;
        $attachmentName = fn ($media) => basename($media->duong_dan);
    @endphp

    <div class="chat-surface grid h-[calc(100vh-64px)] grid-cols-[380px_minmax(0,1fr)] overflow-hidden bg-[#080d18] text-slate-100">
        <section class="flex min-h-0 flex-col border-r border-[#1b3047] bg-[#0b1220]">
            <div class="border-b border-[#1b3047] px-8 py-7">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-100">Nhóm</h1>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Tin nhắn nhóm</p>
                    </div>
                    <a href="{{ route('chat.demo') }}" class="rounded-2xl border border-sky-400/30 px-4 py-2 text-sm font-bold text-sky-300 hover:bg-sky-400/10">1-1</a>
                </div>

                {{-- Form tạo nhóm mới cùng danh sách thành viên --}}
                <form method="POST" action="{{ route('chat.groups.store') }}" enctype="multipart/form-data" class="mt-6 space-y-3 rounded-3xl border border-[#1b3047] bg-[#101827] p-4">
                    @csrf
                    <input name="ten_nhom"
                           class="h-11 w-full rounded-2xl border border-[#24384f] bg-[#0b1220] px-4 font-semibold text-slate-100 outline-none placeholder:text-slate-500 focus:border-sky-400"
                           placeholder="Tên nhóm"
                           value="{{ old('ten_nhom') }}">

                    <label class="block rounded-2xl border border-dashed border-[#34536f] bg-[#0b1220] px-4 py-3 text-sm font-semibold text-slate-400">
                        Ảnh đại diện nhóm
                        <input name="anh_nhom" type="file" accept="image/*" class="mt-2 block w-full text-xs text-slate-400 file:mr-3 file:rounded-xl file:border-0 file:bg-sky-300 file:px-3 file:py-2 file:font-bold file:text-[#07111f]">
                    </label>

                    <div class="max-h-40 space-y-2 overflow-y-auto rounded-2xl border border-[#24384f] bg-[#0b1220] p-3">
                        @forelse ($users as $user)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl px-2 py-2 hover:bg-white/[.04]">
                                <input name="member_ids[]" value="{{ $user->id }}" type="checkbox" class="rounded-full border-slate-500 bg-[#101827] text-sky-400 focus:ring-sky-400">
                                <span class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-sky-300 to-emerald-400 text-sm font-black text-[#07111f]">{{ $avatarText($displayName($user)) }}</span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-bold">{{ $displayName($user) }}</span>
                                    <span class="block truncate text-xs text-slate-500">{{ $user->email }}</span>
                                </span>
                            </label>
                        @empty
                            <div class="text-sm text-slate-500">Chưa có người dùng khác để tạo nhóm.</div>
                        @endforelse
                    </div>

                    @if ($errors->any())
                        <div class="rounded-2xl border border-red-400/20 bg-red-400/10 px-3 py-2 text-xs font-semibold text-red-300">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-3 py-2 text-xs font-semibold text-emerald-300">
                            {{ session('status') }}
                        </div>
                    @endif

                    <button class="h-11 w-full rounded-2xl bg-sky-300 font-black text-[#07111f] hover:bg-sky-200" type="submit">
                        Tạo nhóm
                    </button>
                </form>
            </div>

            {{-- Danh sách các nhóm hiện có của người dùng --}}
            <div class="min-h-0 flex-1 space-y-3 overflow-y-auto p-4">
                @forelse ($groups as $group)
                    @php($isActive = optional($activeGroup)->id === $group->id)
                    @php($isMuted = (bool) optional($group->members->firstWhere('id', $currentUser->id)?->pivot)->tat_thong_bao)
                    <a href="{{ route('chat.groups.index', ['group_id' => $group->id]) }}"
                       class="flex items-center gap-4 rounded-3xl border px-4 py-4 transition {{ $isActive ? 'border-sky-500/30 bg-sky-500/12' : 'border-transparent hover:bg-white/[.04]' }}">
                        @if ($groupAvatar($group))
                            <img src="{{ $groupAvatar($group) }}" alt="{{ $group->ten_nhom }}" class="h-14 w-14 rounded-full object-cover">
                        @else
                            <div class="grid h-14 w-14 place-items-center rounded-full bg-gradient-to-br from-sky-300 to-violet-400 text-xl font-black text-[#07111f]">{{ $avatarText($group->ten_nhom) }}</div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <div class="truncate text-lg font-black">{{ $group->ten_nhom }}</div>
                                @if ($isMuted)
                                    <span class="material-symbols-outlined shrink-0 text-[16px] text-amber-300" title="Đã tắt thông báo">notifications_off</span>
                                @endif
                            </div>
                            <div class="truncate text-sm font-semibold text-slate-400">{{ $group->members->count() }} thanh vien</div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-3xl border border-[#1b3047] bg-[#101827] p-5 text-center text-sm font-semibold text-slate-400">
                        Chưa có nhóm nào. Tạo nhóm mới để bắt đầu.
                    </div>
                @endforelse
            </div>
        </section>

        <main class="chat-bg flex min-h-0 flex-col">
            {{-- Hiển thị chi tiết nhóm và khung chat tin nhắn nhóm --}}
            @if ($activeGroup)
                <header class="flex h-[76px] items-center justify-between border-b border-[#1b3047] bg-[#0d1423] px-8">
                    <div class="flex min-w-0 items-center gap-4">
                        @if ($groupAvatar($activeGroup))
                            <img src="{{ $groupAvatar($activeGroup) }}" alt="{{ $activeGroup->ten_nhom }}" class="h-12 w-12 rounded-full object-cover">
                        @else
                            <div class="grid h-12 w-12 place-items-center rounded-full bg-gradient-to-br from-sky-300 to-violet-400 text-lg font-black text-[#07111f]">{{ $avatarText($activeGroup->ten_nhom) }}</div>
                        @endif
                        <div class="min-w-0">
                            <h2 class="truncate text-xl font-black">{{ $activeGroup->ten_nhom }}</h2>
                            <p class="truncate text-sm font-semibold text-slate-400">
                                {{ $activeGroup->members->map(fn ($user) => $displayName($user))->join(', ') }}
                            </p>
                        </div>
                    </div>
                    <div class="relative w-60">
                        <label class="flex h-10 items-center gap-2 rounded-2xl border border-[#1b3047] bg-[#101827] px-3 text-slate-500">
                            <span class="material-symbols-outlined text-lg">search</span>
                            <input id="groupSearchInput" class="w-full border-0 bg-transparent text-sm outline-none placeholder:text-slate-500 focus:ring-0" placeholder="Tim kiem..." type="search">
                        </label>
                        <div id="groupSearchResults" class="absolute top-full left-0 right-0 mt-2 max-h-64 overflow-y-auto rounded-xl border border-[#1b3047] bg-[#0b1220] hidden shadow-2xl z-10">
                        </div>
                    </div>
                    <form id="muteGroupForm"
                          action="{{ route('chat.groups.mute', $activeGroup) }}"
                          method="POST"
                          data-muted="{{ $activeGroupMuted ? '1' : '0' }}"
                          class="inline-flex">
                        @csrf
                        <button id="muteGroupButton"
                                class="mute-glass group inline-flex h-12 items-center gap-2 rounded-2xl border px-4 text-sm font-black transition {{ $activeGroupMuted ? 'border-amber-300/40 text-amber-200 hover:bg-amber-300/10' : 'border-sky-300/25 text-sky-200 hover:bg-sky-300/10' }}"
                                type="submit"
                                title="{{ $activeGroupMuted ? 'Bật lại thông báo' : 'Tắt thông báo' }}">
                            <span class="material-symbols-outlined text-[22px] transition group-hover:scale-110">{{ $activeGroupMuted ? 'notifications_off' : 'notifications' }}</span>
                            <span>{{ $activeGroupMuted ? 'Đang tắt' : 'Tắt chuông' }}</span>
                        </button>
                    </form>
                </header>
                <div id="muteToast" class="pointer-events-none fixed right-6 top-20 z-50 hidden rounded-2xl border border-white/10 bg-[#101827]/90 px-4 py-3 text-sm font-bold text-slate-100 shadow-2xl backdrop-blur-xl"></div>

                <div id="groupMessages" class="min-h-0 flex-1 space-y-6 overflow-y-auto px-8 py-6">
                    @forelse ($messages as $chatMessage)
                        @php($isMine = $chatMessage->nguoi_gui_id === $currentUser->id)
                        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $chatMessage->id }}">
                            <div class="max-w-[62%]">
                                @unless ($isMine)
                                    <div class="mb-1 text-xs font-bold text-sky-300">{{ $displayName($chatMessage->sender) }}</div>
                                @endunless
                                <div class="rounded-[22px] border px-5 py-4 text-base font-semibold leading-relaxed {{ $isMine ? 'border-sky-300/30 bg-sky-400/20' : 'border-[#1d344e] bg-[#101827]' }}">
                                    @if ($chatMessage->da_thu_hoi)
                                        <div class="italic text-slate-400">Tin nhắn đã bị thu hồi</div>
                                    @else
                                        @if ($chatMessage->noi_dung)
                                            <div class="whitespace-pre-wrap break-words">{{ $chatMessage->noi_dung }}</div>
                                        @endif
                                        @if ($chatMessage->media->isNotEmpty())
                                            <div class="{{ $chatMessage->noi_dung ? 'mt-3' : '' }} space-y-3">
                                                @foreach ($chatMessage->media as $media)
                                                    @if ($media->loai === 'hinh_anh')
                                                        <a href="{{ asset($media->duong_dan) }}" target="_blank" class="block overflow-hidden rounded-2xl border border-white/10">
                                                            <img src="{{ asset($media->duong_dan) }}" alt="{{ $attachmentName($media) }}" class="max-h-80 w-full object-cover">
                                                        </a>
                                                    @elseif ($media->loai === 'video')
                                                        <video controls class="max-h-80 w-full rounded-2xl border border-white/10 bg-black">
                                                            <source src="{{ asset($media->duong_dan) }}">
                                                        </video>
                                                    @elseif ($media->loai === 'am_thanh')
                                                        <audio controls class="w-72 max-w-full">
                                                            <source src="{{ asset($media->duong_dan) }}">
                                                        </audio>
                                                    @else
                                                        <a href="{{ asset($media->duong_dan) }}" target="_blank" class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/[.05] px-4 py-3 text-sm font-bold hover:bg-white/[.08]">
                                                            <span class="grid h-9 w-9 place-items-center rounded-xl bg-sky-300 text-[#07111f]">F</span>
                                                            <span class="min-w-0 truncate">{{ $attachmentName($media) }}</span>
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div class="mt-1 flex items-center gap-2 text-xs font-semibold text-slate-500 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                    <span>{{ optional($chatMessage->ngay_tao)->format('H:i') }}</span>
                                    @if ($isMine && !$chatMessage->da_thu_hoi)
                                        <button type="button" class="delete-group-message text-red-400 hover:text-red-300" data-message-id="{{ $chatMessage->id }}" title="Thu hồi tin nhắn">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6h16ZM10 11v6M14 11v6"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex h-full items-center justify-center text-lg font-bold text-slate-500">
                            Chưa có tin nhắn trong nhóm.
                        </div>
                    @endforelse
                </div>
                <div id="groupTypingIndicator" class="hidden px-8 pb-2">
                    <div class="inline-flex max-w-full items-center gap-3 rounded-2xl border border-white/10 bg-[#101827]/70 px-4 py-2 text-sm font-bold text-slate-300 shadow-2xl backdrop-blur-xl">
                        <div id="groupTypingAvatars" class="flex -space-x-2"></div>
                        <span id="groupTypingText" class="truncate"></span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-white/[.06] px-2.5 py-2">
                            <span class="typing-dot h-1.5 w-1.5 rounded-full bg-sky-200"></span>
                            <span class="typing-dot h-1.5 w-1.5 rounded-full bg-sky-200"></span>
                            <span class="typing-dot h-1.5 w-1.5 rounded-full bg-sky-200"></span>
                        </span>
                    </div>
                </div>

                <form id="groupMessageForm"
                      method="POST"
                      enctype="multipart/form-data"
                      action="{{ route('chat.groups.messages.store', $activeGroup) }}"
                      data-fetch-url="{{ route('chat.groups.messages.index', $activeGroup) }}"
                      data-typing-url="{{ route('chat.groups.typing.start', $activeGroup) }}"
                      data-stop-typing-url="{{ route('chat.groups.typing.stop', $activeGroup) }}"
                      data-typing-users-url="{{ route('chat.groups.typing.index', $activeGroup) }}"
                      data-conversation-id="{{ $activeGroup->id }}"
                      class="flex items-center gap-4 border-t border-[#1b3047] bg-[#0d1423] p-6">
                    @csrf
                    <label class="grid h-12 w-12 shrink-0 cursor-pointer place-items-center rounded-full border border-[#34536f] text-xl text-slate-300 hover:border-sky-300 hover:text-sky-300" title="Gửi ảnh, video, tệp">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.4 11.6 12 21a6 6 0 0 1-8.5-8.5l9.9-9.9a4 4 0 0 1 5.7 5.7l-9.9 9.9a2 2 0 0 1-2.8-2.8l9.2-9.2"/></svg>
                        <input id="groupAttachmentInput" name="attachments[]" type="file" class="hidden" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                    </label>
                    <button id="groupRecordButton" class="grid h-12 w-12 shrink-0 place-items-center rounded-full border border-[#34536f] text-slate-300 hover:border-sky-300 hover:text-sky-300" type="button" title="Ghi âm">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3a3 3 0 0 0-3 3v6a3 3 0 0 0 6 0V6a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3"/></svg>
                    </button>
                    <input id="groupMessageInput"
                           name="noi_dung"
                           class="h-14 min-w-0 flex-1 rounded-full border border-[#1b3047] bg-[#101827] px-6 text-base font-semibold text-slate-100 outline-none placeholder:text-slate-500 focus:border-sky-400"
                           placeholder="Nhập tin nhắn nhóm..."
                           autocomplete="off">
                    <button class="grid h-14 w-14 place-items-center rounded-full bg-sky-300 text-[#07111f] hover:bg-sky-200" type="submit" title="Gửi">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor"><path d="M3.4 20.4 21 12 3.4 3.6 3 10l10 2-10 2 .4 6.4Z"/></svg>
                    </button>
                </form>
                <div id="groupAttachmentPreview" class="border-t border-[#1b3047] bg-[#0d1423] px-8 pb-3 text-sm font-semibold text-sky-300"></div>
            @else
                <div class="flex h-full items-center justify-center text-xl font-bold text-slate-500">
                    Tạo hoặc chọn một nhóm để bắt đầu trò chuyện.
                </div>
            @endif
        </main>
    </div>

    <!-- Delete Group Message Modal -->
    <div id="deleteGroupMessageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="rounded-2xl border border-[#1b3047] bg-[#0d1423] shadow-2xl">
            <div class="border-b border-[#1b3047] px-6 py-4">
                <h3 class="text-lg font-bold text-slate-100">Xóa tin nhắn</h3>
            </div>
            <div class="p-6 space-y-3">
                <button type="button" id="deleteGroupForMeBtn" class="w-full rounded-lg border border-[#1b3047] bg-[#101827] px-4 py-3 text-left font-semibold text-slate-100 hover:bg-[#1a2332] transition">
                    <div class="font-bold text-slate-100">Xóa cho tôi</div>
                    <div class="text-xs text-slate-400">Chỉ bạn mới nhìn thấy tin nhắn bị xóa</div>
                </button>
                <button type="button" id="deleteGroupForAllBtn" class="w-full rounded-lg border border-[#1b3047] bg-[#101827] px-4 py-3 text-left font-semibold text-slate-100 hover:bg-[#1a2332] transition">
                    <div class="font-bold text-slate-100">Thu hồi cho cả nhóm</div>
                    <div class="text-xs text-slate-400">Tất cả sẽ thấy tin nhắn bị thu hồi</div>
                </button>
                <button type="button" id="cancelGroupDeleteBtn" class="w-full rounded-lg border border-[#1b3047] bg-[#101827] px-4 py-3 text-center font-semibold text-slate-300 hover:bg-[#1a2332] transition">
                    Hủy
                </button>
            </div>
        </div>
    </div>

    <script>
        const groupMessages = document.getElementById('groupMessages');
        const groupForm = document.getElementById('groupMessageForm');
        const groupInput = document.getElementById('groupMessageInput');
        const groupAttachmentInput = document.getElementById('groupAttachmentInput');
        const groupAttachmentPreview = document.getElementById('groupAttachmentPreview');
        const groupRecordButton = document.getElementById('groupRecordButton');
        const muteGroupForm = document.getElementById('muteGroupForm');
        const muteGroupButton = document.getElementById('muteGroupButton');
        const muteToast = document.getElementById('muteToast');
        const groupTypingIndicator = document.getElementById('groupTypingIndicator');
        const groupTypingText = document.getElementById('groupTypingText');
        const groupTypingAvatars = document.getElementById('groupTypingAvatars');
        let groupMediaRecorder = null;
        let groupRecordedChunks = [];
        let lastGroupMessagesFingerprint = '';
        let groupTypingStopTimer = null;
        let lastGroupTypingSentAt = 0;
        let isGroupTyping = false;
        let groupTypingUsers = new Map();

        // Đảm bảo nội dung hiển thị an toàn, tránh XSS
        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        // Xây dựng HTML cho các file đính kèm trong tin nhắn nhóm
        function attachmentHtml(attachments) {
            return (attachments || []).map((file) => {
                const url = escapeHtml(file.url);
const name = escapeHtml(file.name || 'Tệp đính kèm');

                if (file.type === 'hinh_anh') {
                    return `<a href="${url}" target="_blank" class="block overflow-hidden rounded-2xl border border-white/10">
                        <img src="${url}" alt="${name}" class="max-h-80 w-full object-cover">
                    </a>`;
                }

                if (file.type === 'video') {
                    return `<video controls class="max-h-80 w-full rounded-2xl border border-white/10 bg-black">
                        <source src="${url}">
                    </video>`;
                }

                if (file.type === 'am_thanh') {
                    return `<audio controls class="w-72 max-w-full">
                        <source src="${url}">
                    </audio>`;
                }

                return `<a href="${url}" target="_blank" class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/[.05] px-4 py-3 text-sm font-bold hover:bg-white/[.08]">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-sky-300 text-[#07111f]">F</span>
                    <span class="min-w-0 truncate">${name}</span>
                </a>`;
            }).join('');
        }

        // Render lại toàn bộ danh sách tin nhắn nhóm mỗi khi có cập nhật mới
        function renderGroupMessages(messages) {
            if (!groupMessages) return;

            const fingerprint = JSON.stringify((messages || []).map((message) => [
                message.id,
                message.content,
                message.time,
                (message.attachments || []).map((file) => [file.type, file.url]).join('|'),
            ]));
            if (fingerprint === lastGroupMessagesFingerprint) return;
            lastGroupMessagesFingerprint = fingerprint;

            const nearBottom = groupMessages.scrollHeight - groupMessages.scrollTop - groupMessages.clientHeight < 140;

            groupMessages.innerHTML = messages.length
                ? messages.map((message) => {
                    const mine = message.is_mine;
                    let content = '';
                    if (message.is_recalled) {
                        content = '<div class="italic text-slate-400">Tin nhắn đã bị thu hồi</div>';
                    } else if (message.is_deleted) {
                        content = '<div class="italic text-slate-400">Tin nhắn đã bị xóa</div>';
                    } else if (message.content) {
                        content = `<div class="whitespace-pre-wrap break-words">${escapeHtml(message.content)}</div>`;
                    }
                    
                    const attachments = (message.is_recalled || message.is_deleted) ? '' : attachmentHtml(message.attachments);
                    const attachmentWrap = attachments
                        ? `<div class="${message.content ? 'mt-3' : ''} space-y-3">${attachments}</div>`
                        : '';
                    const deleteButton = mine && !message.is_recalled && !message.is_deleted
                        ? `<button type="button" class="delete-group-message text-red-400 hover:text-red-300" data-message-id="${message.id}" title="Xóa tin nhắn">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6h16ZM10 11v6M14 11v6"/></svg>
                        </button>`
                        : '';
                    return `
                        <div class="flex ${mine ? 'justify-end' : 'justify-start'}">
                            <div class="max-w-[62%]">
                                ${mine ? '' : `<div class="mb-1 text-xs font-bold text-sky-300">${escapeHtml(message.sender_name)}</div>`}
                                <div class="rounded-[22px] border px-5 py-4 text-base font-semibold leading-relaxed ${mine ? 'border-sky-300/30 bg-sky-400/20' : 'border-[#1d344e] bg-[#101827]'}">
                                    ${content}
                                    ${attachmentWrap}
                                </div>
                                <div class="mt-1 flex items-center gap-2 text-xs font-semibold text-slate-500 ${mine ? 'justify-end' : ''}">
                                    <span>${escapeHtml(message.time)}</span>
                                    ${deleteButton}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('')
                : '<div class="flex h-full items-center justify-center text-lg font-bold text-slate-500">Chưa có tin nhắn trong nhóm.</div>';

            if (nearBottom) groupMessages.scrollTop = groupMessages.scrollHeight;
        }

        function groupTypingLabel(users) {
            if (users.length === 0) return '';
            if (users.length === 1) return `${users[0].name} đang nhập tin nhắn...`;
            return `${users.length} người đang nhập...`;
        }

        function pruneGroupTypingUsers() {
            const now = Math.floor(Date.now() / 1000);
            groupTypingUsers.forEach((user, id) => {
                if ((user.expires_at || 0) <= now) groupTypingUsers.delete(id);
            });
        }

        function renderGroupTypingUsers(users = null) {
            if (!groupTypingIndicator || !groupTypingText || !groupTypingAvatars) return;

            if (Array.isArray(users)) {
                groupTypingUsers = new Map(users.map((user) => [Number(user.id), user]));
            }

            pruneGroupTypingUsers();
            const activeUsers = Array.from(groupTypingUsers.values());
            groupTypingText.textContent = groupTypingLabel(activeUsers);
            groupTypingAvatars.innerHTML = activeUsers.slice(0, 3).map((user) => {
                const initial = escapeHtml(user.initial || (user.name || '?').slice(0, 1));
                if (user.avatar_url) {
                    return `<img src="${escapeHtml(user.avatar_url)}" alt="${escapeHtml(user.name)}" class="h-7 w-7 rounded-full border-2 border-[#101827] object-cover">`;
                }
                return `<span class="grid h-7 w-7 place-items-center rounded-full border-2 border-[#101827] bg-gradient-to-br from-sky-300 to-violet-400 text-xs font-black text-[#07111f]">${initial}</span>`;
            }).join('');
            groupTypingIndicator.classList.toggle('hidden', activeUsers.length === 0);
        }

        async function loadGroupTypingUsers() {
            if (!groupForm || document.hidden) return;

            const response = await fetch(groupForm.dataset.typingUsersUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (response.ok) {
                const data = await response.json();
                renderGroupTypingUsers(data.users || []);
            }
        }

        async function sendGroupTypingState(typing) {
            if (!groupForm) return;

            const token = groupForm.querySelector('input[name="_token"]').value;
            await fetch(typing ? groupForm.dataset.typingUrl : groupForm.dataset.stopTypingUrl, {
                method: typing ? 'POST' : 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                credentials: 'same-origin',
            });
        }

        function subscribeGroupTypingChannel(conversationId) {
            if (!conversationId || !window.Echo || groupForm?.dataset.typingSubscribed === String(conversationId)) return;
            groupForm.dataset.typingSubscribed = String(conversationId);
            window.Echo.private(`chat.conversation.${conversationId}`)
                .listen('.typing', (event) => {
                    const user = event.user || {};
                    if (!user.id) return;
                    if (event.typing) {
                        groupTypingUsers.set(Number(user.id), user);
                    } else {
                        groupTypingUsers.delete(Number(user.id));
                    }
                    renderGroupTypingUsers();
                });
        }

        function markGroupTypingActivity() {
            if (!groupInput || !groupInput.value.trim()) return;

            const now = Date.now();
            if (!isGroupTyping || now - lastGroupTypingSentAt > 2200) {
                isGroupTyping = true;
                lastGroupTypingSentAt = now;
                sendGroupTypingState(true);
            }

            window.clearTimeout(groupTypingStopTimer);
            groupTypingStopTimer = window.setTimeout(() => {
                isGroupTyping = false;
                sendGroupTypingState(false);
            }, 2800);
        }

        function stopLocalGroupTyping() {
            window.clearTimeout(groupTypingStopTimer);
            if (isGroupTyping) {
                isGroupTyping = false;
                sendGroupTypingState(false);
            }
        }

        // Tải tin nhắn nhóm từ server theo kiểu AJAX
        async function loadGroupMessages() {
            if (!groupForm || document.hidden) return;
            const response = await fetch(groupForm.dataset.fetchUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (response.ok) {
                const data = await response.json();
                renderGroupMessages(data.messages || []);
            }
        }

        if (groupMessages) groupMessages.scrollTop = groupMessages.scrollHeight;

        function showMuteToast(message) {
            if (!muteToast) return;
            muteToast.textContent = message;
            muteToast.classList.remove('hidden');
            window.clearTimeout(showMuteToast.timer);
            showMuteToast.timer = window.setTimeout(() => muteToast.classList.add('hidden'), 2200);
        }

        if (muteGroupForm && muteGroupButton) {
            muteGroupForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const token = muteGroupForm.querySelector('input[name="_token"]').value;
                const response = await fetch(muteGroupForm.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) return;

                const data = await response.json();
                const muted = Boolean(data.muted);
                const icon = muteGroupButton.querySelector('.material-symbols-outlined');
                const label = muteGroupButton.querySelector('span:last-child');
                muteGroupForm.dataset.muted = muted ? '1' : '0';
                muteGroupButton.title = muted ? 'Bật lại thông báo' : 'Tắt thông báo';
                muteGroupButton.className = `mute-glass group inline-flex h-12 items-center gap-2 rounded-2xl border px-4 text-sm font-black transition ${muted ? 'border-amber-300/40 text-amber-200 hover:bg-amber-300/10' : 'border-sky-300/25 text-sky-200 hover:bg-sky-300/10'}`;
                if (icon) icon.textContent = muted ? 'notifications_off' : 'notifications';
                if (label) label.textContent = muted ? 'Đang tắt' : 'Tắt chuông';
                showMuteToast(data.message || (muted ? 'Đã tắt thông báo nhóm.' : 'Đã bật lại thông báo nhóm.'));
            });
        }

        if (groupForm && groupInput) {
            // Xử lý gửi tin nhắn nhóm qua AJAX để không reload trang
            groupForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                const content = groupInput.value.trim();
                const hasFiles = groupAttachmentInput && groupAttachmentInput.files.length > 0;
                if (!content && !hasFiles) return;

                const token = groupForm.querySelector('input[name="_token"]').value;
                const body = new FormData(groupForm);
                body.set('noi_dung', content);
                groupInput.value = '';
                if (groupAttachmentPreview) groupAttachmentPreview.textContent = '';
                stopLocalGroupTyping();

                const response = await fetch(groupForm.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    credentials: 'same-origin',
                    body,
                });

                if (!response.ok) {
                    groupInput.value = content;
                    if (groupAttachmentPreview && hasFiles) groupAttachmentPreview.textContent = `${groupAttachmentInput.files.length} tệp đang chờ gửi`;
                    return;
                }

                if (groupAttachmentInput) groupAttachmentInput.value = '';
                await loadGroupMessages();
            });

            groupAttachmentInput?.addEventListener('change', () => {
                if (!groupAttachmentPreview) return;
                const count = groupAttachmentInput.files.length;
                groupAttachmentPreview.textContent = count ? `${count} tệp đã chọn` : '';
            });

            // Gửi bản ghi âm với FormData, giữ nguyên cùng endpoint gửi tin nhắn
            async function sendGroupAudio(blob) {
                const token = groupForm.querySelector('input[name="_token"]').value;
                const body = new FormData(groupForm);
                body.set('noi_dung', groupInput.value.trim());
                body.delete('attachments[]');
                body.append('attachments[]', blob, `ghi-am-${Date.now()}.webm`);

                groupInput.value = '';
                if (groupAttachmentPreview) groupAttachmentPreview.textContent = 'Đang gửi ghi âm...';

                const response = await fetch(groupForm.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    credentials: 'same-origin',
                    body,
                });

                if (groupAttachmentPreview) groupAttachmentPreview.textContent = '';
                if (response.ok) await loadGroupMessages();
            }

            // Bật/tắt ghi âm khi người dùng bấm vào nút micro
            groupRecordButton?.addEventListener('click', async () => {
                if (groupMediaRecorder && groupMediaRecorder.state === 'recording') {
                    groupMediaRecorder.stop();
                    return;
                }

                if (!navigator.mediaDevices?.getUserMedia || !window.MediaRecorder) {
                    if (groupAttachmentPreview) groupAttachmentPreview.textContent = 'Trình duyệt không hỗ trợ ghi âm.';
                    return;
                }

                let stream;
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                } catch (error) {
                    if (groupAttachmentPreview) groupAttachmentPreview.textContent = 'Không thể mở micro. Hãy cấp quyền ghi âm cho trình duyệt.';
                    return;
                }
                groupRecordedChunks = [];
                groupMediaRecorder = new MediaRecorder(stream);
                groupMediaRecorder.addEventListener('dataavailable', (event) => {
                    if (event.data.size > 0) groupRecordedChunks.push(event.data);
                });
                groupMediaRecorder.addEventListener('stop', async () => {
                    stream.getTracks().forEach((track) => track.stop());
                    groupRecordButton.classList.remove('border-red-400', 'bg-red-400/15', 'text-red-300');
                    const blob = new Blob(groupRecordedChunks, { type: 'audio/webm' });
                    if (blob.size > 0) await sendGroupAudio(blob);
                });

                groupRecordButton.classList.add('border-red-400', 'bg-red-400/15', 'text-red-300');
                if (groupAttachmentPreview) groupAttachmentPreview.textContent = 'Đang ghi âm... bấm micro để dừng và gửi';
                groupMediaRecorder.start();
            });

            groupInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    groupForm.requestSubmit();
                }
            });
            groupInput.addEventListener('input', markGroupTypingActivity);
            groupInput.addEventListener('blur', () => {
                window.clearTimeout(groupTypingStopTimer);
                groupTypingStopTimer = window.setTimeout(stopLocalGroupTyping, 600);
            });

            // Handle group message deletion
            const deleteGroupModal = document.getElementById('deleteGroupMessageModal');
            const deleteGroupForMeBtn = document.getElementById('deleteGroupForMeBtn');
            const deleteGroupForAllBtn = document.getElementById('deleteGroupForAllBtn');
            const cancelGroupDeleteBtn = document.getElementById('cancelGroupDeleteBtn');
            let pendingGroupDeleteMessageId = null;

            document.addEventListener('click', async (event) => {
                const deleteButton = event.target.closest('.delete-group-message');
                if (!deleteButton) return;

                pendingGroupDeleteMessageId = deleteButton.dataset.messageId;
                deleteGroupModal?.classList.remove('hidden');
                deleteGroupModal?.classList.add('flex');
            });

            deleteGroupForMeBtn?.addEventListener('click', async () => {
                if (confirm('Bạn chắc chắn muốn xóa tin nhắn này cho bạn?')) {
                    await performGroupDelete('ca_nhan');
                } else {
                    deleteGroupModal?.classList.add('hidden');
                    deleteGroupModal?.classList.remove('flex');
                    pendingGroupDeleteMessageId = null;
                }
            });

            deleteGroupForAllBtn?.addEventListener('click', async () => {
                if (confirm('Bạn chắc chắn muốn thu hồi tin nhắn này cho cả nhóm? Người khác sẽ nhận thấy tin nhắn đã bị thu hồi.')) {
                    await performGroupDelete('ca_hai');
                } else {
                    deleteGroupModal?.classList.add('hidden');
                    deleteGroupModal?.classList.remove('flex');
                    pendingGroupDeleteMessageId = null;
                }
            });

            cancelGroupDeleteBtn?.addEventListener('click', () => {
                deleteGroupModal?.classList.add('hidden');
                deleteGroupModal?.classList.remove('flex');
                pendingGroupDeleteMessageId = null;
            });

            async function performGroupDelete(type) {
                const messageId = pendingGroupDeleteMessageId;
                const token = groupForm.querySelector('input[name="_token"]')?.value;
                if (!token || !messageId) return;

                deleteGroupModal?.classList.add('hidden');
                deleteGroupModal?.classList.remove('flex');

                const response = await fetch(`/chat-groups/messages/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ type }),
                });

                if (response.ok) {
                    await loadGroupMessages();
                } else {
                    alert('Không thể xóa tin nhắn. Vui lòng thử lại.');
                }
                pendingGroupDeleteMessageId = null;
            }

            subscribeGroupTypingChannel(groupForm.dataset.conversationId);
            loadGroupMessages();
            loadGroupTypingUsers();
            setInterval(loadGroupMessages, 2500);
            setInterval(loadGroupTypingUsers, 1500);
            setInterval(() => renderGroupTypingUsers(), 1000);
        }

        // Search functionality for group chat
        const groupSearchInput = document.getElementById('groupSearchInput');
        const groupSearchResults = document.getElementById('groupSearchResults');
        let groupSearchTimeout;

        if (groupSearchInput && groupForm) {
            groupSearchInput.addEventListener('input', async (e) => {
                clearTimeout(groupSearchTimeout);
                const keyword = e.target.value.trim();

                if (!keyword) {
                    groupSearchResults.classList.add('hidden');
                    return;
                }

                groupSearchTimeout = setTimeout(async () => {
                    const conversationId = groupForm.action.match(/chat-groups\/(\d+)/)?.[1];
                    if (!conversationId) return;

                    try {
                        const response = await fetch(
                            `/chat-groups/${conversationId}/search?keyword=${encodeURIComponent(keyword)}`,
                            {
                                headers: { Accept: 'application/json' },
                                credentials: 'same-origin',
                            }
                        );

                        if (response.ok) {
                            const data = await response.json();
                            displayGroupSearchResults(data);
                        }
                    } catch (error) {
                        console.error('Group search error:', error);
                    }
                }, 300);
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('#groupSearchInput') && !e.target.closest('#groupSearchResults')) {
                    groupSearchResults.classList.add('hidden');
                }
            });
        }

        function displayGroupSearchResults(data) {
            if (!data.messages || data.messages.length === 0) {
                groupSearchResults.innerHTML = '<div class="p-3 text-center text-slate-400 text-xs">Không tìm thấy tin nhắn nào</div>';
                groupSearchResults.classList.remove('hidden');
                return;
            }

            const resultsHtml = data.messages.map(msg => `
                <div class="border-b border-[#1b3047] p-2 hover:bg-[#101827] cursor-pointer transition text-xs" onclick="scrollToGroupMessage(${msg.id})">
                    <div class="font-bold text-sky-300">${escapeHtml(msg.sender_name)}</div>
                    <div class="text-slate-400">${msg.time}</div>
                    <div class="text-slate-100 line-clamp-1 mt-0.5">${escapeHtml(msg.content || '[Tệp đính kèm]')}</div>
                </div>
            `).join('');

            groupSearchResults.innerHTML = `
                <div class="p-2 border-b border-[#1b3047] text-xs text-slate-400 font-semibold">
                    Tìm thấy ${data.total} kết quả
                </div>
                ${resultsHtml}
            `;
            groupSearchResults.classList.remove('hidden');
        }

        function scrollToGroupMessage(messageId) {
            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
            if (messageElement) {
                messageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                messageElement.classList.add('animate-pulse');
                setTimeout(() => messageElement.classList.remove('animate-pulse'), 2000);
            }
            groupSearchResults.classList.add('hidden');
        }
    </script>
@endsection
