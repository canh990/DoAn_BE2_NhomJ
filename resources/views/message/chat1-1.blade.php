@extends('layouts.app')

@section('title', 'Tin nhan')

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
    </style>

    @php
        $activeUser = $selectedUser;
        $displayName = fn ($user) => $user->ten_dang_nhap ?: ($user->email ?: 'Nguoi dung');
        $avatarText = fn ($user) => mb_strtoupper(mb_substr($displayName($user), 0, 1));
        $attachmentName = fn ($media) => basename($media->duong_dan);
    @endphp

    <div class="chat-surface grid h-[calc(100vh-64px)] grid-cols-[380px_minmax(0,1fr)] overflow-hidden bg-[#080d18] text-slate-100">
        <section class="flex min-h-0 flex-col border-r border-[#1b3047] bg-[#0b1220]">
            <div class="flex items-center justify-between px-8 py-7">
                <div>
                    <h1 class="text-3xl font-extrabold">Tin nhan</h1>
                    <p class="mt-1 text-sm font-semibold text-slate-500">Chat 1-1</p>
                </div>
                <a href="{{ route('chat.groups.index') }}" class="rounded-2xl border border-sky-400/30 px-4 py-2 text-sm font-bold text-sky-300 hover:bg-sky-400/10">
                    Nhom
                </a>
            </div>

            <div class="px-8">
                <label class="flex h-12 items-center gap-3 rounded-3xl border border-[#1b3047] bg-[#101827] px-4 text-slate-500">
                    <span class="material-symbols-outlined text-xl">search</span>
                    <input class="w-full border-0 bg-transparent text-base outline-none placeholder:text-slate-500 focus:ring-0" placeholder="Tim kiem cuoc tro chuyen..." type="search">
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
                                    {{ $isActive ? 'Vua xong' : 'Online' }}
                                </div>
                            </div>
                            <div class="mt-1 truncate font-medium {{ $isActive ? 'text-sky-300' : 'text-slate-400' }}">
                                {{ $isActive && $messages->last() ? ($messages->last()->noi_dung ?: '[Tep dinh kem]') : ($user->email ?: 'Bat dau tro chuyen') }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-3xl border border-[#1b3047] bg-white/[.03] p-5 text-center text-slate-400">
                        Chua co nguoi dung khac de chat.
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

                    <div class="flex items-center gap-6 text-2xl text-slate-400">
                        <button class="material-symbols-outlined hover:text-sky-300" type="button">call</button>
                        <button class="material-symbols-outlined hover:text-sky-300" type="button">videocam</button>
                        <span class="h-8 w-px bg-[#1b3047]"></span>
                        <button class="material-symbols-outlined hover:text-sky-300" type="button">settings</button>
                    </div>
                </header>

                <div id="chatMessages" class="min-h-0 flex-1 space-y-8 overflow-y-auto px-8 py-7">
                    <div class="flex justify-center">
                        <span class="rounded-full bg-white/[.06] px-5 py-2 text-xs font-black uppercase tracking-[.22em] text-slate-400">Hom nay</span>
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
                            Chua co tin nhan. Hay gui tin dau tien.
                        </div>
                    @endforelse
                </div>

                <div class="p-7">
                    <form id="messageForm"
                          method="POST"
                          enctype="multipart/form-data"
                          data-fetch-url="{{ route('chat.user.messages.index', $activeUser) }}"
                          data-send-url="{{ route('chat.user.messages.store', $activeUser) }}"
                          action="{{ $conversation ? route('chat.messages.store', $conversation) : route('chat.conversations.store') }}"
                          class="flex items-center gap-5 rounded-[22px] border border-sky-500/25 bg-[#101827]/95 px-6 py-3 shadow-[0_0_45px_rgba(56,189,248,.08)]">
                        @csrf
                        @unless ($conversation)
                            <input type="hidden" name="user_id" value="{{ $activeUser->id }}">
                        @endunless

                        <label class="grid h-9 w-9 shrink-0 cursor-pointer place-items-center rounded-full border-2 border-slate-400 text-slate-400 hover:border-sky-300 hover:text-sky-300" title="Gui anh, video, tep">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.4 11.6 12 21a6 6 0 0 1-8.5-8.5l9.9-9.9a4 4 0 0 1 5.7 5.7l-9.9 9.9a2 2 0 0 1-2.8-2.8l9.2-9.2"/></svg>
                            <input id="attachmentInput" name="attachments[]" type="file" class="hidden" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                        </label>
                        <button id="emojiButton" class="grid h-9 w-9 shrink-0 place-items-center rounded-full border-2 border-slate-400 text-slate-400 hover:border-sky-300 hover:text-sky-300" type="button" title="Cam xuc">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M8 10h.01M16 10h.01M8 15c1.2 1 2.5 1.5 4 1.5s2.8-.5 4-1.5"/></svg>
                        </button>
                        <input id="messageInput"
                               name="noi_dung"
                               class="h-14 min-w-0 flex-1 rounded-full border border-[#1b3047] bg-[#111a2a] px-6 text-lg font-semibold text-slate-100 outline-none placeholder:text-slate-500 focus:border-sky-400"
                               placeholder="Nhap tin nhan cua ban..."
                               autocomplete="off"
                               value="{{ old('noi_dung') }}">
                        <button id="recordButton" class="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-transparent text-slate-400 hover:bg-sky-400/10 hover:text-sky-300" type="button" title="Ghi am">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3a3 3 0 0 0-3 3v6a3 3 0 0 0 6 0V6a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3"/></svg>
                        </button>
                        <button class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-sky-300 text-[#07111f] transition hover:bg-sky-200" type="submit" title="Gui">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor"><path d="M3.4 20.4 21 12 3.4 3.6 3 10l10 2-10 2 .4 6.4Z"/></svg>
                        </button>
                    </form>

                    @error('noi_dung')
                        <div class="mt-3 px-3 text-sm font-semibold text-red-300">{{ $message }}</div>
                    @enderror
                    @error('attachments.*')
                        <div class="mt-3 px-3 text-sm font-semibold text-red-300">{{ $message }}</div>
                    @enderror
                    <div id="emojiPicker" class="mt-3 hidden max-w-md rounded-2xl border border-[#1b3047] bg-[#101827] p-3 shadow-2xl">
                        <div class="grid grid-cols-10 gap-1 text-xl">
                            @foreach (['😀','😁','😂','🤣','😊','😍','😘','😎','😢','😭','😡','👍','👎','👏','🙏','🔥','❤️','💯','🎉','😴'] as $emoji)
                                <button type="button" class="emoji-option grid h-9 w-9 place-items-center rounded-xl hover:bg-white/[.08]" data-emoji="{{ $emoji }}">{{ $emoji }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div id="attachmentPreview" class="mt-2 px-3 text-sm font-semibold text-sky-300"></div>
                </div>
            @else
                <div class="flex h-full flex-col items-center justify-center gap-4 text-slate-400">
                    <div class="grid h-20 w-20 place-items-center rounded-full bg-sky-400/10 text-4xl text-sky-300">≡</div>
                    <div class="text-xl font-bold">Chon mot nguoi dung de bat dau chat.</div>
                </div>
            @endif
        </main>
    </div>

    <script>
        const chatMessages = document.getElementById('chatMessages');
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const attachmentInput = document.getElementById('attachmentInput');
        const attachmentPreview = document.getElementById('attachmentPreview');
        const recordButton = document.getElementById('recordButton');
        const emojiButton = document.getElementById('emojiButton');
        const emojiPicker = document.getElementById('emojiPicker');
        const otherAvatar = @json($activeUser ? $avatarText($activeUser) : '');
        let mediaRecorder = null;
        let recordedChunks = [];
        let lastMessagesFingerprint = '';

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function attachmentHtml(attachments) {
            return (attachments || []).map((file) => {
                const url = escapeHtml(file.url);
                const name = escapeHtml(file.name || 'Tep dinh kem');

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
            const content = message.content
                ? `<div class="whitespace-pre-wrap break-words">${escapeHtml(message.content)}</div>`
                : '';
            const attachments = attachmentHtml(message.attachments);
            const attachmentWrap = attachments
                ? `<div class="${message.content ? 'mt-3' : ''} space-y-3">${attachments}</div>`
                : '';

            return `
                <div class="flex items-end gap-4 ${justify}">
                    ${avatar}
                    <div class="max-w-[58%]">
                        <div class="rounded-[20px] border px-5 py-4 text-lg font-semibold leading-relaxed shadow-2xl ${bubble}">
                            ${content}
                            ${attachmentWrap}
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

            const fingerprint = JSON.stringify((messages || []).map((message) => [
                message.id,
                message.content,
                message.time,
                (message.attachments || []).map((file) => [file.type, file.url]).join('|'),
            ]));
            if (fingerprint === lastMessagesFingerprint) return;
            lastMessagesFingerprint = fingerprint;

            const nearBottom = chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight < 120;
            const body = messages.length
                ? messages.map(messageHtml).join('')
                : '<div class="flex h-full items-center justify-center text-lg font-semibold text-slate-500">Chua co tin nhan. Hay gui tin dau tien.</div>';

            chatMessages.innerHTML = `
                <div class="flex justify-center">
                    <span class="rounded-full bg-white/[.06] px-5 py-2 text-xs font-black uppercase tracking-[.22em] text-slate-400">Hom nay</span>
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
                const hasFiles = attachmentInput && attachmentInput.files.length > 0;
                if (!content && !hasFiles) return;

                const token = messageForm.querySelector('input[name="_token"]').value;
                const body = new FormData(messageForm);
                body.set('noi_dung', content);
                messageInput.value = '';
                if (attachmentPreview) attachmentPreview.textContent = '';

                const response = await fetch(messageForm.dataset.sendUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    credentials: 'same-origin',
                    body,
                });

                if (!response.ok) {
                    messageInput.value = content;
                    if (attachmentPreview && hasFiles) attachmentPreview.textContent = `${attachmentInput.files.length} tep dang cho gui`;
                    return;
                }

                if (attachmentInput) attachmentInput.value = '';
                await loadMessages();
            });

            attachmentInput?.addEventListener('change', () => {
                if (!attachmentPreview) return;
                const count = attachmentInput.files.length;
                attachmentPreview.textContent = count ? `${count} tep da chon` : '';
            });

            emojiButton?.addEventListener('click', () => {
                emojiPicker?.classList.toggle('hidden');
            });

            emojiPicker?.addEventListener('click', (event) => {
                const option = event.target.closest('.emoji-option');
                if (!option) return;

                const emoji = option.dataset.emoji || '';
                const start = messageInput.selectionStart ?? messageInput.value.length;
                const end = messageInput.selectionEnd ?? messageInput.value.length;
                messageInput.value = `${messageInput.value.slice(0, start)}${emoji}${messageInput.value.slice(end)}`;
                const cursor = start + emoji.length;
                messageInput.focus();
                messageInput.setSelectionRange(cursor, cursor);
            });

            document.addEventListener('click', (event) => {
                if (!emojiPicker || emojiPicker.classList.contains('hidden')) return;
                if (emojiPicker.contains(event.target) || emojiButton?.contains(event.target)) return;
                emojiPicker.classList.add('hidden');
            });

            async function sendAudio(blob) {
                const token = messageForm.querySelector('input[name="_token"]').value;
                const body = new FormData(messageForm);
                body.set('noi_dung', messageInput.value.trim());
                body.delete('attachments[]');
                body.append('attachments[]', blob, `ghi-am-${Date.now()}.webm`);

                messageInput.value = '';
                if (attachmentPreview) attachmentPreview.textContent = 'Dang gui ghi am...';

                const response = await fetch(messageForm.dataset.sendUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    credentials: 'same-origin',
                    body,
                });

                if (attachmentPreview) attachmentPreview.textContent = '';
                if (response.ok) await loadMessages();
            }

            recordButton?.addEventListener('click', async () => {
                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    mediaRecorder.stop();
                    return;
                }

                if (!navigator.mediaDevices?.getUserMedia || !window.MediaRecorder) {
                    if (attachmentPreview) attachmentPreview.textContent = 'Trinh duyet khong ho tro ghi am.';
                    return;
                }

                let stream;
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                } catch (error) {
                    if (attachmentPreview) attachmentPreview.textContent = 'Khong the mo micro. Hay cap quyen ghi am cho trinh duyet.';
                    return;
                }
                recordedChunks = [];
                mediaRecorder = new MediaRecorder(stream);
                mediaRecorder.addEventListener('dataavailable', (event) => {
                    if (event.data.size > 0) recordedChunks.push(event.data);
                });
                mediaRecorder.addEventListener('stop', async () => {
                    stream.getTracks().forEach((track) => track.stop());
                    recordButton.classList.remove('border-red-400', 'bg-red-400/15', 'text-red-300');
                    const blob = new Blob(recordedChunks, { type: 'audio/webm' });
                    if (blob.size > 0) await sendAudio(blob);
                });

                recordButton.classList.add('border-red-400', 'bg-red-400/15', 'text-red-300');
                if (attachmentPreview) attachmentPreview.textContent = 'Dang ghi am... bam micro de dung va gui';
                mediaRecorder.start();
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
@endsection
