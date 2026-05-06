<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        * { scrollbar-width: thin; scrollbar-color: #256d8f #0b1220; }
    </style>
</head>
<body class="h-screen overflow-hidden bg-[#080d18] text-slate-100">
    @php
        $displayName = fn ($user) => $user->ten_dang_nhap ?: ($user->email ?: 'Nguoi dung');
        $avatarText = fn ($text) => mb_strtoupper(mb_substr($text ?: 'N', 0, 1));
        $groupAvatar = fn ($group) => $group->anh_nhom ? asset($group->anh_nhom) : null;
    @endphp

    <div class="grid h-screen grid-cols-[340px_minmax(0,1fr)]">
        <aside class="flex min-h-0 flex-col border-r border-[#1b3047] bg-[#0d1423]">
            <div class="border-b border-[#1b3047] p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-black text-sky-300">Group Chat</h1>
                        <p class="text-sm font-semibold text-slate-400">{{ $displayName($currentUser) }}</p>
                    </div>
                    <a href="{{ route('chat.demo') }}" class="rounded-2xl border border-sky-400/30 px-4 py-2 text-sm font-bold text-sky-300 hover:bg-sky-400/10">1-1</a>
                </div>

                <form method="POST" action="{{ route('chat.groups.store') }}" enctype="multipart/form-data" class="mt-6 space-y-3 rounded-3xl border border-[#1b3047] bg-[#101827] p-4">
                    @csrf
                    <input name="ten_nhom"
                           class="h-11 w-full rounded-2xl border border-[#24384f] bg-[#0b1220] px-4 font-semibold text-slate-100 outline-none placeholder:text-slate-500 focus:border-sky-400"
                           placeholder="Ten nhom"
                           value="{{ old('ten_nhom') }}">

                    <label class="block rounded-2xl border border-dashed border-[#34536f] bg-[#0b1220] px-4 py-3 text-sm font-semibold text-slate-400">
                        Anh dai dien nhom
                        <input name="anh_nhom" type="file" accept="image/*" class="mt-2 block w-full text-xs text-slate-400 file:mr-3 file:rounded-xl file:border-0 file:bg-sky-300 file:px-3 file:py-2 file:font-bold file:text-[#07111f]">
                    </label>

                    <div class="max-h-40 space-y-2 overflow-y-auto rounded-2xl border border-[#24384f] bg-[#0b1220] p-3">
                        @forelse ($users as $user)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl px-2 py-2 hover:bg-white/[.04]">
                                <input name="member_ids[]" value="{{ $user->id }}" type="checkbox" class="rounded border-slate-500 bg-[#101827] text-sky-400 focus:ring-sky-400">
                                <span class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-sky-300 to-emerald-400 text-sm font-black text-[#07111f]">{{ $avatarText($displayName($user)) }}</span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-bold">{{ $displayName($user) }}</span>
                                    <span class="block truncate text-xs text-slate-500">{{ $user->email }}</span>
                                </span>
                            </label>
                        @empty
                            <div class="text-sm text-slate-500">Chua co user khac de tao nhom.</div>
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
                        Tao nhom
                    </button>
                </form>
            </div>

            <div class="min-h-0 flex-1 space-y-3 overflow-y-auto p-4">
                @forelse ($groups as $group)
                    @php($isActive = optional($activeGroup)->id === $group->id)
                    <a href="{{ route('chat.groups.index', ['group_id' => $group->id]) }}"
                       class="flex items-center gap-4 rounded-3xl border px-4 py-4 transition {{ $isActive ? 'border-sky-500/30 bg-sky-500/12' : 'border-transparent hover:bg-white/[.04]' }}">
                        @if ($groupAvatar($group))
                            <img src="{{ $groupAvatar($group) }}" alt="{{ $group->ten_nhom }}" class="h-14 w-14 rounded-full object-cover">
                        @else
                            <div class="grid h-14 w-14 place-items-center rounded-full bg-gradient-to-br from-sky-300 to-violet-400 text-xl font-black text-[#07111f]">{{ $avatarText($group->ten_nhom) }}</div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="truncate text-lg font-black">{{ $group->ten_nhom }}</div>
                            <div class="truncate text-sm font-semibold text-slate-400">{{ $group->members->count() }} thanh vien</div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-3xl border border-[#1b3047] bg-[#101827] p-5 text-center text-sm font-semibold text-slate-400">
                        Chua co nhom nao. Tao nhom moi de bat dau.
                    </div>
                @endforelse
            </div>
        </aside>

        <main class="flex min-h-0 flex-col bg-[#080d18]">
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
                </header>

                <div id="groupMessages" class="min-h-0 flex-1 space-y-6 overflow-y-auto px-8 py-6">
                    @forelse ($messages as $chatMessage)
                        @php($isMine = $chatMessage->nguoi_gui_id === $currentUser->id)
                        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[62%]">
                                @unless ($isMine)
                                    <div class="mb-1 text-xs font-bold text-sky-300">{{ $displayName($chatMessage->sender) }}</div>
                                @endunless
                                <div class="rounded-[22px] border px-5 py-4 text-base font-semibold leading-relaxed {{ $isMine ? 'border-sky-300/30 bg-sky-400/20' : 'border-[#1d344e] bg-[#101827]' }}">
                                    <div class="whitespace-pre-wrap break-words">{{ $chatMessage->noi_dung }}</div>
                                </div>
                                <div class="mt-1 text-xs font-semibold text-slate-500 {{ $isMine ? 'text-right' : '' }}">{{ optional($chatMessage->ngay_tao)->format('H:i') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="flex h-full items-center justify-center text-lg font-bold text-slate-500">
                            Chua co tin nhan trong nhom.
                        </div>
                    @endforelse
                </div>

                <form id="groupMessageForm"
                      method="POST"
                      action="{{ route('chat.groups.messages.store', $activeGroup) }}"
                      data-fetch-url="{{ route('chat.groups.messages.index', $activeGroup) }}"
                      class="flex items-center gap-4 border-t border-[#1b3047] bg-[#0d1423] p-6">
                    @csrf
                    <button class="grid h-12 w-12 shrink-0 place-items-center rounded-full border border-[#34536f] text-xl text-slate-300 hover:border-sky-300 hover:text-sky-300" type="button" title="Gui tep">📎</button>
                    <button class="grid h-12 w-12 shrink-0 place-items-center rounded-full border border-[#34536f] text-xl text-slate-300 hover:border-sky-300 hover:text-sky-300" type="button" title="Ghi am">🎙</button>
                    <input id="groupMessageInput"
                           name="noi_dung"
                           class="h-14 min-w-0 flex-1 rounded-full border border-[#1b3047] bg-[#101827] px-6 text-base font-semibold text-slate-100 outline-none placeholder:text-slate-500 focus:border-sky-400"
                           placeholder="Nhap tin nhan nhom..."
                           autocomplete="off"
                           required>
                    <button class="grid h-14 w-14 place-items-center rounded-full bg-sky-300 text-2xl font-black text-[#07111f] hover:bg-sky-200" type="submit">➤</button>
                </form>
            @else
                <div class="flex h-full items-center justify-center text-xl font-bold text-slate-500">
                    Tao hoac chon mot nhom de chat.
                </div>
            @endif
        </main>
    </div>

    <script>
        const groupMessages = document.getElementById('groupMessages');
        const groupForm = document.getElementById('groupMessageForm');
        const groupInput = document.getElementById('groupMessageInput');

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderGroupMessages(messages) {
            if (!groupMessages) return;
            const nearBottom = groupMessages.scrollHeight - groupMessages.scrollTop - groupMessages.clientHeight < 140;

            groupMessages.innerHTML = messages.length
                ? messages.map((message) => {
                    const mine = message.is_mine;
                    return `
                        <div class="flex ${mine ? 'justify-end' : 'justify-start'}">
                            <div class="max-w-[62%]">
                                ${mine ? '' : `<div class="mb-1 text-xs font-bold text-sky-300">${escapeHtml(message.sender_name)}</div>`}
                                <div class="rounded-[22px] border px-5 py-4 text-base font-semibold leading-relaxed ${mine ? 'border-sky-300/30 bg-sky-400/20' : 'border-[#1d344e] bg-[#101827]'}">
                                    <div class="whitespace-pre-wrap break-words">${escapeHtml(message.content)}</div>
                                </div>
                                <div class="mt-1 text-xs font-semibold text-slate-500 ${mine ? 'text-right' : ''}">${escapeHtml(message.time)}</div>
                            </div>
                        </div>
                    `;
                }).join('')
                : '<div class="flex h-full items-center justify-center text-lg font-bold text-slate-500">Chua co tin nhan trong nhom.</div>';

            if (nearBottom) groupMessages.scrollTop = groupMessages.scrollHeight;
        }

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

        if (groupForm && groupInput) {
            groupForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                const content = groupInput.value.trim();
                if (!content) return;

                const token = groupForm.querySelector('input[name="_token"]').value;
                groupInput.value = '';

                const response = await fetch(groupForm.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-CSRF-TOKEN': token,
                    },
                    credentials: 'same-origin',
                    body: new URLSearchParams({ noi_dung: content }),
                });

                if (!response.ok) {
                    groupInput.value = content;
                    return;
                }

                await loadGroupMessages();
            });

            groupInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    groupForm.requestSubmit();
                }
            });

            loadGroupMessages();
            setInterval(loadGroupMessages, 2500);
        }
    </script>
</body>
</html>
