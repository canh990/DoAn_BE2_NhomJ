@extends('layouts.app')

@section('title', 'Nhom')

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
        $displayName = fn ($user) => $user->ten_dang_nhap ?: ($user->email ?: 'Nguoi dung');
        $avatarText = fn ($text) => mb_strtoupper(mb_substr($text ?: 'N', 0, 1));
        $groupAvatar = fn ($group) => $group->anh_nhom ? asset($group->anh_nhom) : null;
        $attachmentName = fn ($media) => basename($media->duong_dan);
    @endphp

    <div class="chat-surface grid h-[calc(100vh-64px)] grid-cols-[380px_minmax(0,1fr)] overflow-hidden bg-[#080d18] text-slate-100">
        <section class="flex min-h-0 flex-col border-r border-[#1b3047] bg-[#0b1220]">
            <div class="border-b border-[#1b3047] px-8 py-7">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-100">Nhom</h1>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Tin nhan nhom</p>
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
        </section>

        <main class="chat-bg flex min-h-0 flex-col">
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
                      enctype="multipart/form-data"
                      action="{{ route('chat.groups.messages.store', $activeGroup) }}"
                      data-fetch-url="{{ route('chat.groups.messages.index', $activeGroup) }}"
                      class="flex items-center gap-4 border-t border-[#1b3047] bg-[#0d1423] p-6">
                    @csrf
                    <label class="grid h-12 w-12 shrink-0 cursor-pointer place-items-center rounded-full border border-[#34536f] text-xl text-slate-300 hover:border-sky-300 hover:text-sky-300" title="Gui anh, video, tep">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.4 11.6 12 21a6 6 0 0 1-8.5-8.5l9.9-9.9a4 4 0 0 1 5.7 5.7l-9.9 9.9a2 2 0 0 1-2.8-2.8l9.2-9.2"/></svg>
                        <input id="groupAttachmentInput" name="attachments[]" type="file" class="hidden" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                    </label>
                    <button id="groupRecordButton" class="grid h-12 w-12 shrink-0 place-items-center rounded-full border border-[#34536f] text-slate-300 hover:border-sky-300 hover:text-sky-300" type="button" title="Ghi am">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3a3 3 0 0 0-3 3v6a3 3 0 0 0 6 0V6a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3"/></svg>
                    </button>
                    <input id="groupMessageInput"
                           name="noi_dung"
                           class="h-14 min-w-0 flex-1 rounded-full border border-[#1b3047] bg-[#101827] px-6 text-base font-semibold text-slate-100 outline-none placeholder:text-slate-500 focus:border-sky-400"
                           placeholder="Nhap tin nhan nhom..."
                           autocomplete="off">
                    <button class="grid h-14 w-14 place-items-center rounded-full bg-sky-300 text-[#07111f] hover:bg-sky-200" type="submit" title="Gui">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor"><path d="M3.4 20.4 21 12 3.4 3.6 3 10l10 2-10 2 .4 6.4Z"/></svg>
                    </button>
                </form>
                <div id="groupAttachmentPreview" class="border-t border-[#1b3047] bg-[#0d1423] px-8 pb-3 text-sm font-semibold text-sky-300"></div>
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
        const groupAttachmentInput = document.getElementById('groupAttachmentInput');
        const groupAttachmentPreview = document.getElementById('groupAttachmentPreview');
        const groupRecordButton = document.getElementById('groupRecordButton');
        let groupMediaRecorder = null;
        let groupRecordedChunks = [];
        let lastGroupMessagesFingerprint = '';

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
                    const content = message.content
                        ? `<div class="whitespace-pre-wrap break-words">${escapeHtml(message.content)}</div>`
                        : '';
                    const attachments = attachmentHtml(message.attachments);
                    const attachmentWrap = attachments
                        ? `<div class="${message.content ? 'mt-3' : ''} space-y-3">${attachments}</div>`
                        : '';
                    return `
                        <div class="flex ${mine ? 'justify-end' : 'justify-start'}">
                            <div class="max-w-[62%]">
                                ${mine ? '' : `<div class="mb-1 text-xs font-bold text-sky-300">${escapeHtml(message.sender_name)}</div>`}
                                <div class="rounded-[22px] border px-5 py-4 text-base font-semibold leading-relaxed ${mine ? 'border-sky-300/30 bg-sky-400/20' : 'border-[#1d344e] bg-[#101827]'}">
                                    ${content}
                                    ${attachmentWrap}
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
                const hasFiles = groupAttachmentInput && groupAttachmentInput.files.length > 0;
                if (!content && !hasFiles) return;

                const token = groupForm.querySelector('input[name="_token"]').value;
                const body = new FormData(groupForm);
                body.set('noi_dung', content);
                groupInput.value = '';
                if (groupAttachmentPreview) groupAttachmentPreview.textContent = '';

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
                    if (groupAttachmentPreview && hasFiles) groupAttachmentPreview.textContent = `${groupAttachmentInput.files.length} tep dang cho gui`;
                    return;
                }

                if (groupAttachmentInput) groupAttachmentInput.value = '';
                await loadGroupMessages();
            });

            groupAttachmentInput?.addEventListener('change', () => {
                if (!groupAttachmentPreview) return;
                const count = groupAttachmentInput.files.length;
                groupAttachmentPreview.textContent = count ? `${count} tep da chon` : '';
            });

            async function sendGroupAudio(blob) {
                const token = groupForm.querySelector('input[name="_token"]').value;
                const body = new FormData(groupForm);
                body.set('noi_dung', groupInput.value.trim());
                body.delete('attachments[]');
                body.append('attachments[]', blob, `ghi-am-${Date.now()}.webm`);

                groupInput.value = '';
                if (groupAttachmentPreview) groupAttachmentPreview.textContent = 'Dang gui ghi am...';

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

            groupRecordButton?.addEventListener('click', async () => {
                if (groupMediaRecorder && groupMediaRecorder.state === 'recording') {
                    groupMediaRecorder.stop();
                    return;
                }

                if (!navigator.mediaDevices?.getUserMedia || !window.MediaRecorder) {
                    if (groupAttachmentPreview) groupAttachmentPreview.textContent = 'Trinh duyet khong ho tro ghi am.';
                    return;
                }

                let stream;
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                } catch (error) {
                    if (groupAttachmentPreview) groupAttachmentPreview.textContent = 'Khong the mo micro. Hay cap quyen ghi am cho trinh duyet.';
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
                if (groupAttachmentPreview) groupAttachmentPreview.textContent = 'Dang ghi am... bam micro de dung va gui';
                groupMediaRecorder.start();
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
@endsection
