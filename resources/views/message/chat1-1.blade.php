<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glacier Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { scrollbar-width: thin; scrollbar-color: #21455f #0b1220; }
        body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .avatar-ring { box-shadow: 0 0 0 1px rgba(125, 211, 252, .4), 0 12px 28px rgba(0, 0, 0, .28); }
        .chat-bg {
            background:
                radial-gradient(circle at 72% 18%, rgba(36, 88, 119, .18), transparent 26%),
                radial-gradient(circle at 40% 82%, rgba(27, 51, 80, .18), transparent 28%),
                #080d18;
        }
    </style>
</head>
<body class="h-screen overflow-hidden bg-[#080d18] text-slate-100">
    @php
        $activeUser = $selectedUser;
        $displayName = fn ($user) => $user->ten_dang_nhap ?: ($user->email ?: 'Người dùng');
        $avatarText = fn ($user) => mb_strtoupper(mb_substr($displayName($user), 0, 1));
    @endphp

    <div class="grid h-screen grid-cols-[318px_400px_minmax(0,1fr)] bg-[#080d18]">
        <aside class="flex min-h-0 flex-col border-r border-[#1b3047] bg-[#0d1423]">
            <div class="flex items-center gap-4 px-7 py-6">
                <div class="grid h-12 w-12 place-items-center rounded-full border border-sky-400/50 bg-sky-400/15 text-3xl font-bold text-sky-300 avatar-ring">*</div>
                <div>
                    <div class="text-xl font-bold text-sky-300">Glacier Chat</div>
                    <div class="text-xs font-bold uppercase tracking-[.28em] text-sky-400">Trực tuyến</div>
                </div>
            </div>

            <nav class="mt-5 space-y-4 px-3 text-lg font-semibold text-slate-400">
                <a class="flex items-center gap-5 border-r-2 border-sky-400 bg-sky-500/14 px-5 py-4 text-sky-300" href="{{ route('chat.demo') }}">
                    <span class="grid h-8 w-8 place-items-center rounded bg-sky-300 text-[#102136]">≡</span>
                    <span>Tin nhắn</span>
                </a>
                <div class="flex items-center gap-5 px-5 py-3">
                    <span class="text-3xl text-slate-500">☎</span>
                    <span>Cuộc gọi</span>
                </div>
                <div class="flex items-center gap-5 px-5 py-3">
                    <span class="text-3xl text-slate-500">▣</span>
                    <span>Danh bạ</span>
                </div>
                <a href="{{ route('chat.groups.index') }}" class="flex items-center gap-5 px-5 py-3 hover:bg-white/[.04] hover:text-sky-300">
                    <span class="text-3xl text-slate-500">☷</span>
                    <span>Nhóm</span>
                </a>
                <div class="flex items-center gap-5 px-5 py-3">
                    <span class="text-3xl text-slate-500">▤</span>
                    <span>Lưu trữ</span>
                </div>
            </nav>

            <div class="mt-auto border-t border-[#1b3047] p-3">
                <div class="mb-6 flex items-center gap-5 px-5 py-4 text-lg font-semibold text-slate-400">
                    <span class="text-3xl">⚙</span>
                    <span>Cài đặt</span>
                </div>

                <div class="flex items-center justify-between rounded-[28px] border border-[#1b3047] bg-[#101a2b] px-5 py-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-sky-300 to-teal-500 font-bold text-[#07111f]">
                            {{ $avatarText($currentUser) }}
                        </div>
                        <div class="min-w-0">
                            <div class="truncate font-bold">{{ $displayName($currentUser) }}</div>
                            <div class="truncate text-sm text-slate-400">Thiết lập trạng thái</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm font-semibold text-sky-300 hover:text-sky-200" type="submit">Thoát</button>
                    </form>
                </div>
            </div>
        </aside>

        <section class="flex min-h-0 flex-col border-r border-[#1b3047] bg-[#0b1220]">
            <div class="flex items-center justify-between px-8 py-7">
                <h1 class="text-3xl font-extrabold">Tin nhắn</h1>
                <button class="grid h-11 w-11 place-items-center rounded-full bg-sky-400/10 text-xl text-sky-300" type="button">✎</button>
            </div>

            <div class="px-8">
                <label class="flex h-12 items-center gap-3 rounded-3xl border border-[#1b3047] bg-[#101827] px-4 text-slate-500">
                    <span class="text-xl">⌕</span>
                    <input class="w-full border-0 bg-transparent text-base outline-none placeholder:text-slate-500 focus:ring-0" placeholder="Tìm kiếm cuộc trò chuyện..." type="search">
                </label>
            </div>

            <form method="POST" action="{{ route('chat.friends.store') }}" class="mt-4 px-8">
                @csrf
                <div class="flex gap-2">
                    <input name="account"
                           class="h-11 min-w-0 flex-1 rounded-2xl border border-[#1b3047] bg-[#101827] px-4 text-sm font-semibold text-slate-100 outline-none placeholder:text-slate-500 focus:border-sky-400"
                           placeholder="Email / SDT / ten dang nhap"
                           value="{{ old('account') }}">
                    <button class="shrink-0 rounded-2xl bg-sky-300 px-4 text-sm font-black text-[#07111f] hover:bg-sky-200" type="submit">
                        Ket ban
                    </button>
                </div>

                @if (session('status'))
                    <div class="mt-2 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-3 py-2 text-xs font-semibold text-emerald-300">
                        {{ session('status') }}
                    </div>
                @endif

                @error('account')
                    <div class="mt-2 rounded-2xl border border-red-400/20 bg-red-400/10 px-3 py-2 text-xs font-semibold text-red-300">
                        {{ $message }}
                    </div>
                @enderror
            </form>

            <div class="mt-5 min-h-0 flex-1 space-y-3 overflow-y-auto px-5 pb-6">
                @forelse ($users as $user)
                    @php
                        $isActive = optional($activeUser)->id === $user->id;
                        $name = $displayName($user);
                    @endphp
                    <a href="{{ route('chat.demo', ['user_id' => $user->id]) }}"
                       class="flex items-center gap-4 rounded-[28px] border px-4 py-4 transition {{ $isActive ? 'border-sky-500/25 bg-sky-500/12' : 'border-transparent hover:bg-white/[.03]' }}">
                        <div class="relative shrink-0">
                            <div class="grid h-14 w-14 place-items-center rounded-full bg-gradient-to-br from-sky-300 via-cyan-500 to-emerald-400 text-xl font-black text-[#07111f] avatar-ring">
                                {{ $avatarText($user) }}
                            </div>
                            <span class="absolute bottom-1 right-0 h-3.5 w-3.5 rounded-full border-2 border-[#0b1220] {{ $isActive ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-3">
                                <div class="truncate text-lg font-bold">{{ $name }}</div>
                                <div class="shrink-0 text-sm font-semibold {{ $isActive ? 'text-sky-300' : 'text-slate-400' }}">
                                    {{ $isActive ? 'Vừa xong' : 'Online' }}
                                </div>
                            </div>
                            <div class="mt-1 truncate font-medium {{ $isActive ? 'text-sky-300' : 'text-slate-400' }}">
                                {{ $isActive && $messages->last() ? $messages->last()->noi_dung : ($user->email ?: 'Bắt đầu trò chuyện') }}
                            </div>
                        </div>

                        @if ($isActive)
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-sky-300 text-sm font-black text-[#07111f]">1</span>
                        @endif
                    </a>
                @empty
                    <div class="rounded-3xl border border-[#1b3047] bg-white/[.03] p-5 text-center text-slate-400">
                        Chưa có người dùng khác để chat.
                    </div>
                @endforelse
            </div>
        </section>

        <main class="chat-bg flex min-h-0 flex-col">
            @if ($activeUser)
                <header class="flex h-[70px] items-center justify-between border-b border-[#1b3047] bg-[#0d1423]/85 px-8">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="grid h-12 w-12 place-items-center rounded-full bg-gradient-to-br from-cyan-300 to-emerald-400 text-xl font-black text-[#07111f] avatar-ring">
                                {{ $avatarText($activeUser) }}
                            </div>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-[#0d1423] bg-emerald-400"></span>
                        </div>
                        <div>
                            <div class="text-xl font-extrabold">{{ $displayName($activeUser) }}</div>
                            <div class="text-sm font-bold text-emerald-400">Online</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-7 text-3xl text-slate-400">
                        <button class="hover:text-sky-300" type="button">☎</button>
                        <button class="hover:text-sky-300" type="button">▭</button>
                        <span class="h-8 w-px bg-[#1b3047]"></span>
                        <button class="hover:text-sky-300" type="button">⚙</button>
                    </div>
                </header>

                <div id="chatMessages" class="min-h-0 flex-1 space-y-8 overflow-y-auto px-8 py-7">
                    <div class="flex justify-center">
                        <span class="rounded-full bg-white/[.06] px-5 py-2 text-xs font-black uppercase tracking-[.22em] text-slate-400">Hôm nay</span>
                    </div>

                    @forelse ($messages as $chatMessage)
                        @php($isMine = $chatMessage->nguoi_gui_id === $currentUser->id)
                        <div class="flex items-end gap-4 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                            @unless ($isMine)
                                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-cyan-300 to-emerald-400 font-black text-[#07111f]">
                                    {{ $avatarText($activeUser) }}
                                </div>
                            @endunless

                            <div class="max-w-[58%]">
                                <div class="rounded-[20px] border px-5 py-4 text-lg font-semibold leading-relaxed shadow-2xl {{ $isMine ? 'border-sky-300/35 bg-sky-400/20 text-slate-100' : 'border-[#1d344e] bg-[#101827] text-slate-100' }}">
                                    <div class="whitespace-pre-wrap break-words">{{ $chatMessage->noi_dung }}</div>
                                </div>
                                <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-slate-400 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                    <span>{{ optional($chatMessage->ngay_tao)->format('H:i') }}</span>
                                    @if ($isMine)
                                        <span class="grid h-3.5 w-3.5 place-items-center rounded-full bg-sky-300 text-[10px] text-[#07111f]">✓</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex h-full items-center justify-center text-lg font-semibold text-slate-500">
                            Chưa có tin nhắn. Hãy gửi tin đầu tiên.
                        </div>
                    @endforelse
                </div>

                <div class="p-7">
                    <form id="messageForm"
                          method="POST"
                          data-fetch-url="{{ route('chat.user.messages.index', $activeUser) }}"
                          data-send-url="{{ route('chat.user.messages.store', $activeUser) }}"
                          action="{{ $conversation ? route('chat.messages.store', $conversation) : route('chat.conversations.store') }}"
                          class="flex items-center gap-5 rounded-[22px] border border-sky-500/25 bg-[#101827]/95 px-6 py-3 shadow-[0_0_45px_rgba(56,189,248,.08)]">
                        @csrf
                        @unless ($conversation)
                            <input type="hidden" name="user_id" value="{{ $activeUser->id }}">
                        @endunless

                        <button class="grid h-9 w-9 shrink-0 place-items-center rounded-full border-2 border-slate-400 text-2xl font-bold text-slate-400 hover:border-sky-300 hover:text-sky-300" type="button">+</button>
                        <button class="grid h-9 w-9 shrink-0 place-items-center rounded-full border-2 border-slate-400 text-xl text-slate-400 hover:border-sky-300 hover:text-sky-300" type="button">☺</button>
                        <input id="messageInput"
                               name="noi_dung"
                               class="h-14 min-w-0 flex-1 rounded-full border border-[#1b3047] bg-[#111a2a] px-6 text-lg font-semibold text-slate-100 outline-none placeholder:text-slate-500 focus:border-sky-400"
                               placeholder="Nhập tin nhắn của bạn..."
                               autocomplete="off"
                               required
                               value="{{ old('noi_dung') }}">
                        <button class="text-3xl text-slate-400 hover:text-sky-300" type="button">♬</button>
                        <button class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-sky-300 text-3xl font-black text-[#07111f] transition hover:bg-sky-200" type="submit">➤</button>
                    </form>

                    @error('noi_dung')
                        <div class="mt-3 px-3 text-sm font-semibold text-red-300">{{ $message }}</div>
                    @enderror
                </div>
            @else
                <div class="flex h-full flex-col items-center justify-center gap-4 text-slate-400">
                    <div class="grid h-20 w-20 place-items-center rounded-full bg-sky-400/10 text-4xl text-sky-300">≡</div>
                    <div class="text-xl font-bold">Chọn một người dùng để bắt đầu chat.</div>
                </div>
            @endif
        </main>
    </div>

    <script>
        const chatMessages = document.getElementById('chatMessages');
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const otherAvatar = @json($activeUser ? $avatarText($activeUser) : '');

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function messageHtml(message) {
            const justify = message.is_mine ? 'justify-end' : 'justify-start';
            const bubble = message.is_mine
                ? 'border-sky-300/35 bg-sky-400/20 text-slate-100'
                : 'border-[#1d344e] bg-[#101827] text-slate-100';
            const metaJustify = message.is_mine ? 'justify-end' : 'justify-start';
            const avatar = message.is_mine ? '' : `
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-cyan-300 to-emerald-400 font-black text-[#07111f]">
                    ${escapeHtml(otherAvatar)}
                </div>
            `;
            const checked = message.is_mine
                ? '<span class="grid h-3.5 w-3.5 place-items-center rounded-full bg-sky-300 text-[10px] text-[#07111f]">✓</span>'
                : '';

            return `
                <div class="flex items-end gap-4 ${justify}">
                    ${avatar}
                    <div class="max-w-[58%]">
                        <div class="rounded-[20px] border px-5 py-4 text-lg font-semibold leading-relaxed shadow-2xl ${bubble}">
                            <div class="whitespace-pre-wrap break-words">${escapeHtml(message.content)}</div>
                        </div>
                        <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-slate-400 ${metaJustify}">
                            <span>${escapeHtml(message.time)}</span>
                            ${checked}
                        </div>
                    </div>
                </div>
            `;
        }

        function renderMessages(messages) {
            if (!chatMessages) return;

            const nearBottom = chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight < 120;
            const body = messages.length
                ? messages.map(messageHtml).join('')
                : '<div class="flex h-full items-center justify-center text-lg font-semibold text-slate-500">Chua co tin nhan. Hay gui tin dau tien.</div>';

            chatMessages.innerHTML = `
                <div class="flex justify-center">
                    <span class="rounded-full bg-white/[.06] px-5 py-2 text-xs font-black uppercase tracking-[.22em] text-slate-400">Hôm nay</span>
                </div>
                ${body}
            `;

            if (nearBottom) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        async function loadMessages() {
            if (!messageForm || document.hidden) return;

            const response = await fetch(messageForm.dataset.fetchUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (response.ok) {
                const data = await response.json();
                renderMessages(data.messages || []);
            }
        }

        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        if (messageForm && messageInput) {
            messageForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const content = messageInput.value.trim();
                if (!content) return;

                const token = messageForm.querySelector('input[name="_token"]').value;
                const body = new URLSearchParams({ noi_dung: content });
                messageInput.value = '';

                const response = await fetch(messageForm.dataset.sendUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-CSRF-TOKEN': token,
                    },
                    credentials: 'same-origin',
                    body,
                });

                if (!response.ok) {
                    messageInput.value = content;
                    return;
                }

                await loadMessages();
            });

            messageInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    messageForm.requestSubmit();
                }
            });

            loadMessages();
            setInterval(loadMessages, 2500);
        }
    </script>
</body>
</html>
