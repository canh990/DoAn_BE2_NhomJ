<!DOCTYPE html>
@php
    // apply the user's preferred locale from session for each render
    app()->setLocale(session('personal_locale', config('app.locale', 'vi')));
    $theme = session('personal_theme', null);
@endphp
<html class="{{ $theme === 'light' ? 'light' : 'dark' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NHOMJ')</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/css/theme-light.css">
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "tertiary-fixed-dim": "#c8a0f0",
                        "on-surface": "#e0e8f0",
                        "error": "#ff6b6b",
                        "on-tertiary": "#1a002e",
                        "on-primary": "#001f2e",
                        "secondary": "#88b4cc",
                        "primary-container": "#0e4d6e",
                        "on-primary-fixed": "#001f2e",
                        "on-tertiary-container": "#e8d0ff",
                        "on-background": "#e0e8f0",
                        "on-tertiary-fixed-variant": "#4d2a73",
                        "on-secondary-fixed-variant": "#2a4a5e",
                        "primary-fixed-dim": "#7dd3fc",
                        "surface-tint": "#7dd3fc",
                        "surface": "#0f1524",
                        "surface-container-lowest": "#0a0e1a",
                        "on-error-container": "#ffb3b3",
                        "secondary-container": "#1a3a4e",
                        "surface-container-highest": "#202c42",
                        "surface-dim": "#0f1524",
                        "inverse-on-surface": "#0a0e1a",
                        "inverse-primary": "#0a4c6e",
                        "tertiary-container": "#3d2060",
                        "outline-variant": "#2a3a48",
                        "inverse-surface": "#e0e8f0",
                        "surface-container": "#141c2e",
                        "on-error": "#1a0000",
                        "tertiary-fixed": "#e8d0ff",
                        "primary": "#7dd3fc",
                        "primary-fixed": "#c8eaff",
                        "error-container": "#3d1414",
                        "tertiary": "#c8a0f0",
                        "on-tertiary-fixed": "#1a002e",
                        "on-secondary-fixed": "#0d1f2b",
                        "surface-variant": "#1a2438",
                        "surface-container-low": "#111828",
                        "surface-container-high": "#1a2438",
                        "on-primary-fixed-variant": "#004d73",
                        "outline": "#4a6070",
                        "on-secondary-container": "#c0d8e8",
                        "secondary-fixed": "#c0d8e8",
                        "on-secondary": "#001f2e",
                        "background": "#0a0e1a",
                        "on-surface-variant": "#a0b4c4",
                        "surface-bright": "#1a2438",
                        "on-primary-container": "#c8eaff",
                        "secondary-fixed-dim": "#88b4cc"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.5rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Inter"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0a0e1a;
            color: #e0e8f0;
            font-family: 'Inter', sans-serif;
        }
        .glass-panel {
            background: rgba(15, 21, 36, 0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(125, 211, 252, 0.1);
        }
        .glass-panel-elevated {
            background: rgba(15, 21, 36, 0.75);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(125, 211, 252, 0.15);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="antialiased selection:bg-primary/30 selection:text-primary">

    <header class="fixed top-0 w-full z-50 bg-[#0a0e1a]/60 backdrop-blur-xl border-b border-sky-400/10 shadow-[0_0_30px_rgba(125,211,252,0.05)] font-inter tracking-tight flex justify-between items-center px-6 h-16">
        <div class="flex items-center gap-8">
            <span class="text-2xl font-bold bg-gradient-to-r from-sky-400 to-purple-400 bg-clip-text text-transparent">NHOMJ</span>
            <div class="hidden md:flex items-center bg-white/5 border border-sky-400/10 rounded-full px-4 py-1.5 focus-within:border-sky-400/30 transition-all">
                <span class="material-symbols-outlined text-slate-400 text-sm mr-2" data-icon="search">search</span>
                <input class="bg-transparent border-none focus:ring-0 text-sm text-on-surface placeholder:text-slate-500 w-64" placeholder="Tìm kiếm trên NHOMJ" type="text"/>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button class="p-2 text-slate-400 hover:bg-sky-400/10 rounded-xl transition-all active:scale-95 duration-200">
                <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
            </button>
            <button class="p-2 text-slate-400 hover:bg-sky-400/10 rounded-xl transition-all active:scale-95 duration-200">
                <span class="material-symbols-outlined" data-icon="mail">mail</span>
            </button>
            <button class="p-2 text-sky-300 hover:bg-sky-400/10 rounded-xl transition-all active:scale-95 duration-200">
                <span class="material-symbols-outlined" data-icon="account_circle">account_circle</span>
            </button>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-400/10 rounded-xl transition-all active:scale-95 duration-200" title="Đăng xuất">
                    <span class="material-symbols-outlined" data-icon="logout">logout</span>
                </button>
            </form>
        </div>
    </header>

    <aside class="fixed left-0 top-16 h-[calc(100vh-64px)] w-64 p-4 border-r border-sky-400/10 flex flex-col gap-2 z-40 hidden md:flex">
     @auth
    @php $user = Auth::user(); @endphp

  <div class="mb-4 px-4 py-2">
    <div class="flex items-center gap-3 mb-1">
        <div class="w-10 h-10 overflow-hidden rounded-full border border-sky-400/30">
            <img 
             
                class="w-full h-full object-cover" 
                alt="{{ $user->name }}" 
                src="{{ $user->anh_dai_dien ? asset('storage/' . $user->anh_dai_dien) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}" 
            />
        </div>
        
        <div>
            <p class="text-sm font-bold text-sky-300 font-inter leading-tight">
                {{ $user->name }}
            </p>
            {{-- Bạn có thể thêm @username ở đây nếu muốn --}}
            <p class="text-[10px] text-slate-500 font-medium">
                {{ '@' . ($user->ten_dang_nhap ?? 'nguoidung') }}
            </p>
        </div>
    </div>
</div>
@endauth
        <nav class="flex flex-col gap-1 flex-1">
            <a class="flex items-center gap-3 {{ request()->routeIs('home') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="{{ route('home') }}">
                <span class="material-symbols-outlined" data-icon="home">home</span>
                Bảng tin
            </a>
            <a class="flex items-center gap-3 {{ request()->routeIs('explore') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="{{ route('explore') }}">
                <span class="material-symbols-outlined" data-icon="explore">explore</span>
                Khám phá
            </a>
            <a class="flex items-center gap-3 {{ request()->routeIs('notifications') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="{{ route('notifications') }}">
                <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                Thông báo
            </a>

            <a class="flex items-center gap-3 {{ request()->routeIs('chat.demo') || request()->routeIs('chat.user.*') || request()->routeIs('chat.messages.*') || request()->routeIs('chat.conversations.*') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="{{ route('chat.demo') }}">
                <span class="material-symbols-outlined" data-icon="chat">chat</span>
                Tin nhắn
            </a>
            <a class="flex items-center gap-3 {{ request()->routeIs('profile') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="{{ route('profile') }}">
                <span class="material-symbols-outlined" data-icon="person">person</span>
                Hồ sơ
            </a>
            <a href="{{ route('settings.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all hover:bg-white/10 group">
    <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform" data-icon="settings">
        settings
    </span>
    <span class="text-lg font-medium">{{ __('messages.settings_title') }}</span>
</a>
        </nav>
        <button class="mt-4 w-full py-3 bg-sky-400/20 border border-sky-400/30 text-sky-300 font-bold rounded-xl hover:bg-sky-400/30 transition-all active:scale-95">
            Đăng bài mới
        </button>
    </aside>

    <main class="md:ml-64 pt-16 min-h-screen">
        @yield('content')
    </main>

    <nav class="md:hidden fixed bottom-0 w-full glass-panel-elevated flex justify-around items-center h-16 z-50 border-t border-sky-400/10">
        <button class="p-2 text-slate-400">
            <span class="material-symbols-outlined" data-icon="home">home</span>
        </button>
        <button class="p-2 text-slate-400">
            <span class="material-symbols-outlined" data-icon="explore">explore</span>
        </button>
        <button class="p-2 text-sky-300 bg-sky-400/20 rounded-xl">
            <span class="material-symbols-outlined" data-icon="person">person</span>
        </button>
        <button class="p-2 text-slate-400">
            <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
        </button>
        <button class="p-2 text-slate-400">
            <span class="material-symbols-outlined" data-icon="mail">mail</span>
        </button>
    </nav>

    <script>
        document.addEventListener('click', function (event) {
            const reactionTrigger = event.target.closest('[data-reaction-trigger]');
            const reactionOption = event.target.closest('[data-reaction-option]');
            const commentToggle = event.target.closest('[data-comment-toggle]');
            const commentReplyButton = event.target.closest('[data-comment-reply-button]');
            const commentCancel = event.target.closest('[data-comment-cancel]');
            const shareButton = event.target.closest('[data-share-button]');
            const reactionAreas = document.querySelectorAll('[data-reaction-area]');

            if (commentCancel) {
                event.preventDefault();
                const area = commentCancel.closest('[data-reaction-area]');
                const form = area?.querySelector('.comment-submit-form');
                if (form) {
                    form.querySelector('input[name="binh_luan_cha_id"]').value = '';
                    form.querySelector('[data-comment-action]').textContent = 'Viết bình luận mới';
                    commentCancel.classList.add('hidden');
                }
                return;
            }

            if (commentReplyButton) {
                event.preventDefault();
                const area = commentReplyButton.closest('[data-reaction-area]');
                const form = area?.querySelector('.comment-submit-form');
                if (form) {
                    form.querySelector('input[name="binh_luan_cha_id"]').value = commentReplyButton.dataset.commentId;
                    form.querySelector('textarea[name="noi_dung"]').focus();
                    form.querySelector('[data-comment-action]').textContent = `Trả lời ${commentReplyButton.dataset.commentUser}`;
                    const cancelButton = form.querySelector('[data-comment-cancel]');
                    if (cancelButton) {
                        cancelButton.classList.remove('hidden');
                    }
                }
                return;
            }

            if (commentToggle) {
                event.stopPropagation();
                const area = commentToggle.closest('[data-reaction-area]');
                const box = area?.querySelector('[data-comment-box]');
                if (box) {
                    box.classList.toggle('hidden');
                }
                return;
            }

            if (reactionOption) {
                event.stopPropagation();
                const reaction = reactionOption.dataset.reaction;
                const label = reactionOption.dataset.reactionLabel;
                const color = reactionOption.dataset.reactionColor;
                const iconName = reactionOption.dataset.reactionIcon;
                const area = reactionOption.closest('[data-reaction-area]');
                const form = area.querySelector('.reaction-submit-form');
                const action = form.action;
                const token = form.querySelector('input[name="_token"]').value;
                const body = new URLSearchParams();

                body.append('_token', token);
                body.append('loai_cam_xuc', reaction);

                fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body,
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        if (!data.success) {
                            return;
                        }

                        const triggerIcon = area.querySelector('[data-reaction-trigger-icon]');
                        const triggerLabel = area.querySelector('[data-reaction-trigger-label]');
                        const countNode = area.querySelector('[data-reaction-count]');
                        const picker = area.querySelector('[data-reaction-picker]');
                        const isRemoved = data.removed;
                        const newIconName = isRemoved ? 'thumb_up' : iconName;
                        const newLabel = isRemoved ? 'Thích' : label;
                        const newColor = isRemoved ? 'text-sky-400' : color;

                        if (triggerIcon) {
                            triggerIcon.textContent = newIconName;
                            triggerIcon.className = 'material-symbols-outlined ' + newColor;
                        }

                        if (triggerLabel) {
                            triggerLabel.textContent = newLabel;
                        }

                        if (countNode) {
                            countNode.textContent = data.reactions_count + ' cảm xúc';
                        }

                        if (picker) {
                            picker.classList.add('hidden');
                        }
                    });

                return;
            }

            if (shareButton) {
                event.stopPropagation();
                window.openShareModal(shareButton.dataset.shareUrl, shareButton);
                return;
            }

            reactionAreas.forEach(function (area) {
                const picker = area.querySelector('[data-reaction-picker]');
                if (!picker) {
                    return;
                }

                if (reactionTrigger && area.contains(reactionTrigger)) {
                    picker.classList.toggle('hidden');
                } else if (!area.contains(event.target)) {
                    picker.classList.add('hidden');
                }
            });
        });

        document.addEventListener('submit', function (event) {
            const commentForm = event.target.closest('.comment-submit-form');
            if (!commentForm) {
                return;
            }

            event.preventDefault();
            const action = commentForm.action;
            const body = new FormData(commentForm);

            fetch(action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body,
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    // Nếu Laravel trả về lỗi validation (422)
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0][0];
                        if (typeof window.showToast === 'function') {
                            window.showToast(firstError, 'error');
                        } else {
                            alert(firstError);
                        }
                        return;
                    }
                    
                    if (data.success === false) {
                        if (typeof window.showToast === 'function') {
                            window.showToast(data.message || 'Có lỗi xảy ra', 'error');
                        } else {
                            alert(data.message || 'Có lỗi xảy ra');
                        }
                        return;
                    }

                    const area = commentForm.closest('[data-reaction-area]');
                    const countNode = area?.querySelector('[data-comment-count]');
                    const box = commentForm.closest('[data-comment-box]');
                    const textarea = commentForm.querySelector('textarea[name="noi_dung"]');

                    if (countNode) {
                        countNode.textContent = '(' + data.comments_count + ')';
                    }

                    if (textarea) {
                        textarea.value = '';
                    }

                    const mediaInput = commentForm.querySelector('.comment-media-input');
                    const mediaPreview = commentForm.querySelector('.comment-media-preview');
                    if (mediaInput) {
                        mediaInput.value = '';
                        mediaInput._selectedFiles = [];
                    }
                    if (mediaPreview) {
                        mediaPreview.innerHTML = '';
                        mediaPreview.classList.add('hidden');
                    }

                    if (box) {
                        const noComments = box.querySelector('[data-no-comments]');
                        const list = box.querySelector('[data-comment-list]');

                        if (noComments) {
                            noComments.remove();
                        }

                        const parentId = data.comment.parent_id;
                        
                        const newThread = document.createElement('div');
                        newThread.className = 'comment-thread w-full';
                        newThread.dataset.commentId = data.comment.id;

                        const newComment = document.createElement('div');
                        newComment.className = 'rounded-2xl border border-white/10 bg-slate-950 p-3';
                        newComment.innerHTML = `
                            <div class="flex gap-3 items-start">
                                <img class="w-8 h-8 rounded-full object-cover border border-slate-700" src="${data.comment.user_avatar}" alt="${data.comment.user_name}">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between gap-2 text-sm text-slate-200">
                                        <span class="font-semibold">${data.comment.user_name}</span>
                                        <span class="text-xs text-slate-500">${data.comment.created_at}</span>
                                    </div>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-300">${data.comment.content}</p>
                                    ${data.comment.media && data.comment.media.length > 0 ? `
                                    <div class="mt-2 grid gap-2 ${data.comment.media.length == 1 ? 'grid-cols-1' : (data.comment.media.length == 2 ? 'grid-cols-2' : 'grid-cols-2 sm:grid-cols-3')} max-w-sm">
                                        ${data.comment.media.map(m => `
                                            <div class="overflow-hidden rounded-xl border border-white/10 bg-slate-900/50 ${data.comment.media.length > 1 ? 'aspect-square' : ''}">
                                                ${m.loai === 'video' 
                                                    ? `<video src="${m.url}" controls controlsList="nodownload" muted playsinline loop class="w-full h-full ${data.comment.media.length == 1 ? 'max-h-[300px] object-contain block' : 'object-cover'}"></video>`
                                                    : `<img src="${m.url}" alt="Comment media" data-post-id="comment-${data.comment.id}" class="post-image-item cursor-pointer hover:opacity-90 transition-opacity w-full h-full ${data.comment.media.length == 1 ? 'max-h-[300px] object-contain block' : 'object-cover'}">`
                                                }
                                            </div>
                                        `).join('')}
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;

                        const replyButton = document.createElement('button');
                        replyButton.type = 'button';
                        replyButton.dataset.commentReplyButton = '';
                        replyButton.dataset.commentId = data.comment.id;
                        replyButton.dataset.commentUser = data.comment.user_name;
                        replyButton.className = 'hover:text-sky-300 transition-colors';
                        replyButton.textContent = 'Trả lời';

                        const replyWrapper = document.createElement('div');
                        replyWrapper.className = 'mt-3 flex items-center gap-3 text-xs text-slate-400';
                        replyWrapper.appendChild(replyButton);
                        
                        // Thêm nút Xóa cho bình luận vừa tạo (vì user vừa tạo chắc chắn có quyền xoá)
                        const deleteButton = document.createElement('button');
                        deleteButton.type = 'button';
                        deleteButton.className = 'hover:text-red-400 transition-colors';
                        deleteButton.textContent = 'Xóa';
                        deleteButton.onclick = function() {
                            window.openConfirmModal('Xóa bình luận?', 'Thao tác này sẽ xoá luôn các ảnh/video đính kèm.', () => {
                                fetch('/comments/' + data.comment.id, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(resData => {
                                    if(resData.success) {
                                        const thread = document.querySelector('.comment-thread[data-comment-id="' + data.comment.id + '"]');
                                        if(thread) thread.remove();
                                        if(typeof window.showToast === 'function') {
                                            window.showToast('Bình luận đã được xoá', 'success');
                                        }
                                    } else {
                                        if(typeof window.showToast === 'function') {
                                            window.showToast(resData.message || 'Có lỗi xảy ra', 'error');
                                        }
                                    }
                                })
                                .catch(err => {
                                    if(typeof window.showToast === 'function') window.showToast('Lỗi kết nối.', 'error');
                                });
                            });
                        };
                        replyWrapper.appendChild(deleteButton);
                        
                        newComment.querySelector('.flex-1').appendChild(replyWrapper);
                        newThread.appendChild(newComment);

                        const replyContainer = document.createElement('div');
                        replyContainer.className = 'mt-2 space-y-2 pl-6 sm:pl-12 relative';
                        replyContainer.dataset.commentReplies = '';
                        newThread.appendChild(replyContainer);

                        if (parentId) {
                            // Find the parent's reply container which is a direct sibling of the parent's comment block
                            // actually it's list.querySelector('[data-comment-id="..."] > [data-comment-replies]')
                            // but since [data-comment-replies] is unique inside the thread wrapper, we can just use descendant selector:
                            const parentReplies = list.querySelector('[data-comment-id="' + parentId + '"] [data-comment-replies]');
                            if (parentReplies) {
                                let verticalLine = parentReplies.querySelector('.absolute.bg-white\\/10');
                                if (!verticalLine) {
                                    verticalLine = document.createElement('div');
                                    verticalLine.className = 'absolute left-[15px] sm:left-[27px] top-0 bottom-0 w-px bg-white/10';
                                    parentReplies.insertBefore(verticalLine, parentReplies.firstChild);
                                }
                                if (verticalLine && verticalLine.nextSibling) {
                                    parentReplies.insertBefore(newThread, verticalLine.nextSibling);
                                } else {
                                    parentReplies.appendChild(newThread);
                                }
                            } else {
                                if (list.firstChild) list.insertBefore(newThread, list.firstChild);
                                else list.appendChild(newThread);
                            }
                        } else {
                            if (list.firstChild && list.firstChild.dataset && list.firstChild.dataset.noComments !== undefined) {
                                list.innerHTML = '';
                                list.appendChild(newThread);
                            } else {
                                if (list.firstChild) list.insertBefore(newThread, list.firstChild);
                                else list.appendChild(newThread);
                            }
                        }
                        
                        // "phải trả lời bình luận liên tiếp nhau": do not reset parentInput, actionLabel, cancelBtn
                        // so the user can continue replying to the same comment if they want.
                    }
                })
                .catch(function (error) {
                    console.error('Lỗi khi gửi bình luận:', error);
                    if (typeof window.showToast === 'function') {
                        window.showToast('Lỗi kết nối. Không thể đăng bình luận.', 'error');
                    } else {
                        alert('Lỗi kết nối. Không thể đăng bình luận.');
                    }
                });
        });
    </script>
    
    <script>
        window.handleCommentMediaSelect = function(input, isUpdate = false) {
            const previewContainer = input.parentElement.querySelector('.comment-media-preview');
            if (!previewContainer) return;
            
            // Khởi tạo mảng lưu trữ file nếu chưa có
            if (!input._selectedFiles) {
                input._selectedFiles = [];
            }
            
            // Nếu người dùng vừa chọn file mới (không phải gọi từ hàm xoá)
            if (!isUpdate && input.files && input.files.length > 0) {
                Array.from(input.files).forEach(file => {
                    // Tránh thêm trùng file (kiểm tra theo tên và dung lượng)
                    const exists = input._selectedFiles.some(f => f.name === file.name && f.size === file.size);
                    if (!exists) {
                        input._selectedFiles.push(file);
                    }
                });
            }
            
            previewContainer.innerHTML = '';
            
            if (input._selectedFiles.length > 0) {
                previewContainer.classList.remove('hidden');
                
                const dt = new DataTransfer();
                input._selectedFiles.forEach((file, index) => {
                    dt.items.add(file);
                    
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden border border-white/20 shadow-sm group shrink-0 bg-slate-900/50';
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'absolute top-1 right-1 bg-slate-900/80 hover:bg-rose-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-all duration-200 flex items-center justify-center z-10';
                    removeBtn.innerHTML = '<span class="material-symbols-outlined text-[14px]">close</span>';
                    removeBtn.onclick = function(e) {
                        e.preventDefault();
                        wrapper.remove();
                        input._selectedFiles.splice(index, 1);
                        window.handleCommentMediaSelect(input, true);
                    };
                    
                    if (file.type.startsWith('video/')) {
                        const video = document.createElement('video');
                        video.src = URL.createObjectURL(file);
                        video.className = 'w-full h-full object-cover';
                        wrapper.appendChild(video);
                        
                        const playIcon = document.createElement('div');
                        playIcon.className = 'absolute inset-0 flex items-center justify-center pointer-events-none bg-black/20';
                        playIcon.innerHTML = '<span class="material-symbols-outlined text-white text-2xl drop-shadow-md" style="font-variation-settings: \'FILL\' 1;">play_circle</span>';
                        wrapper.appendChild(playIcon);
                    } else {
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.className = 'w-full h-full object-cover';
                        wrapper.appendChild(img);
                    }
                    wrapper.appendChild(removeBtn);
                    previewContainer.appendChild(wrapper);
                });
                
                input.files = dt.files;
            } else {
                previewContainer.classList.add('hidden');
                input.files = new DataTransfer().files; // Clear files
            }
        };
    </script>

    <!-- Global Image Lightbox -->
    <div id="image-lightbox" class="fixed inset-0 z-[100] hidden bg-black/95 backdrop-blur-sm flex-col justify-center items-center opacity-0 transition-opacity duration-300">
        <!-- Close button -->
        <button id="lightbox-close" class="absolute top-4 right-4 text-white/70 hover:text-white p-2 bg-white/10 hover:bg-white/20 rounded-full transition-all z-50">
            <span class="material-symbols-outlined text-3xl">close</span>
        </button>

        <!-- Navigation Buttons -->
        <button id="lightbox-prev" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white p-3 bg-white/5 hover:bg-white/20 rounded-full transition-all hidden z-50">
            <span class="material-symbols-outlined text-4xl">chevron_left</span>
        </button>
        
        <button id="lightbox-next" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white p-3 bg-white/5 hover:bg-white/20 rounded-full transition-all hidden z-50">
            <span class="material-symbols-outlined text-4xl">chevron_right</span>
        </button>

        <!-- Image Container -->
        <div class="relative w-full h-full max-w-7xl max-h-screen p-4 sm:p-12 flex items-center justify-center">
            <img id="lightbox-img" class="max-w-full max-h-full object-contain transition-transform duration-300 scale-95" src="" alt="Full view">
        </div>

        <!-- Image Counter -->
        <div id="lightbox-counter" class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/70 text-sm font-medium bg-black/50 px-4 py-1.5 rounded-full hidden">
            1 / 3
        </div>
    </div>

    <script>
        // Lightbox logic
        document.addEventListener('DOMContentLoaded', function() {
            const lightbox = document.getElementById('image-lightbox');
            const lightboxImg = document.getElementById('lightbox-img');
            const lightboxClose = document.getElementById('lightbox-close');
            const lightboxPrev = document.getElementById('lightbox-prev');
            const lightboxNext = document.getElementById('lightbox-next');
            const lightboxCounter = document.getElementById('lightbox-counter');
            
            let currentGallery = [];
            let currentIndex = 0;

            function openLightbox(gallery, index) {
                currentGallery = gallery;
                currentIndex = index;
                updateLightbox();
                
                lightbox.classList.remove('hidden');
                lightbox.classList.add('flex');
                
                // Allow display flex to apply before adding opacity
                setTimeout(() => {
                    lightbox.classList.remove('opacity-0');
                    lightboxImg.classList.remove('scale-95');
                    lightboxImg.classList.add('scale-100');
                }, 10);
                document.body.style.overflow = 'hidden';
            }

            function closeLightbox() {
                lightbox.classList.add('opacity-0');
                lightboxImg.classList.remove('scale-100');
                lightboxImg.classList.add('scale-95');
                setTimeout(() => {
                    lightbox.classList.add('hidden');
                    lightbox.classList.remove('flex');
                    document.body.style.overflow = '';
                }, 300);
            }

            function updateLightbox() {
                if (currentGallery.length === 0) return;
                
                lightboxImg.src = currentGallery[currentIndex];
                
                if (currentGallery.length > 1) {
                    lightboxPrev.classList.remove('hidden');
                    lightboxNext.classList.remove('hidden');
                    lightboxCounter.classList.remove('hidden');
                    lightboxCounter.textContent = `${currentIndex + 1} / ${currentGallery.length}`;
                } else {
                    lightboxPrev.classList.add('hidden');
                    lightboxNext.classList.add('hidden');
                    lightboxCounter.classList.add('hidden');
                }
            }

            function showNextImage(e) {
                if (e) e.stopPropagation();
                if (currentGallery.length <= 1) return;
                currentIndex = (currentIndex + 1) % currentGallery.length;
                updateLightbox();
            }

            function showPrevImage(e) {
                if (e) e.stopPropagation();
                if (currentGallery.length <= 1) return;
                currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
                updateLightbox();
            }

            if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
            if (lightboxNext) lightboxNext.addEventListener('click', showNextImage);
            if (lightboxPrev) lightboxPrev.addEventListener('click', showPrevImage);
            
            if (lightbox) {
                lightbox.addEventListener('click', function(e) {
                    if (e.target === lightbox || e.target.closest('.relative.w-full.h-full') && e.target.id !== 'lightbox-img' && e.target.id !== 'lightbox-next' && e.target.id !== 'lightbox-prev') {
                        closeLightbox();
                    }
                });
            }

            document.addEventListener('keydown', function(e) {
                if (!lightbox || lightbox.classList.contains('hidden')) return;
                
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowRight') showNextImage();
                if (e.key === 'ArrowLeft') showPrevImage();
            });

            // Bind clicks to post images
            document.addEventListener('click', function(e) {
                const img = e.target.closest('.post-image-item');
                if (img) {
                    const postId = img.getAttribute('data-post-id');
                    if (!postId) {
                        openLightbox([img.src], 0);
                        return;
                    }
                    const galleryImgs = document.querySelectorAll(`.post-image-item[data-post-id="${postId}"]`);
                    const galleryUrls = Array.from(galleryImgs).map(el => el.src);
                    const index = Array.from(galleryImgs).indexOf(img);
                    openLightbox(galleryUrls, index);
                }
            });

            // Video Autoplay on Scroll
            const videoObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const video = entry.target;
                    if (entry.isIntersecting) {
                        video.play().catch(e => console.log('Autoplay prevented:', e));
                    } else {
                        video.pause();
                    }
                });
            }, {
                threshold: 0.6 // Play when 60% visible
            });

            // Observe existing videos
            document.querySelectorAll('video').forEach(video => {
                videoObserver.observe(video);
            });

            // If we add new posts dynamically, we need a MutationObserver to observe new videos
            const observer = new MutationObserver((mutations) => {
                mutations.forEach(mutation => {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === 1) {
                            if (node.tagName === 'VIDEO') {
                                videoObserver.observe(node);
                            }
                            node.querySelectorAll('video').forEach(video => {
                                videoObserver.observe(video);
                            });
                        }
                    });
                });
            });

            observer.observe(document.body, { childList: true, subtree: true });

            // Global Dropdown Logic for Post Options
            document.addEventListener('click', function(e) {
                const trigger = e.target.closest('.post-dropdown-trigger');
                if (trigger) {
                    const menu = trigger.nextElementSibling;
                    const isHidden = menu.classList.contains('hidden');
                    
                    document.querySelectorAll('.post-dropdown-menu').forEach(m => m.classList.add('hidden'));
                    
                    if (isHidden) {
                        menu.classList.remove('hidden');
                    }
                } else {
                    if (!e.target.closest('.post-dropdown-menu')) {
                        document.querySelectorAll('.post-dropdown-menu').forEach(m => m.classList.add('hidden'));
                    }
                }
            });
        });

        // --- Logic Chỉnh sửa bài viết Toàn cầu ---
    
        // --- Global Confirm Modal ---
        let confirmActionCallback = null;
        window.openConfirmModal = function(title, message, callback) {
            const modal = document.getElementById('global-confirm-modal');
            if (!modal) return;
            
            document.getElementById('confirm-modal-title').textContent = title;
            document.getElementById('confirm-modal-message').textContent = message;
            confirmActionCallback = callback;
            
            const content = document.getElementById('confirm-modal-content');
            modal.classList.remove('hidden');
            
            // Trigger animation
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
            }, 10);
        };
        
        window.closeConfirmModal = function() {
            const modal = document.getElementById('global-confirm-modal');
            if (!modal) return;
            
            const content = document.getElementById('confirm-modal-content');
            content.classList.add('scale-95');
            modal.classList.add('opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                confirmActionCallback = null;
            }, 300);
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            const submitBtn = document.getElementById('confirm-modal-submit');
            if(submitBtn) {
                submitBtn.addEventListener('click', function() {
                    if (confirmActionCallback) {
                        confirmActionCallback();
                    }
                    window.closeConfirmModal();
                });
            }
        });
    
        // --- Logic Chỉnh sửa bài viết Toàn cầu ---
        window.openEditModal = function(postId, content) {
            const editModal = document.getElementById('edit-post-modal');
            const editForm = document.getElementById('edit-post-form');
            const editContent = document.getElementById('edit-post-content');
            const editModalContent = editModal ? editModal.querySelector('.glass-panel') : null;

            if (!editModal) {
                console.error("Edit modal not found!");
                return;
            }
            
            editForm.action = `/posts/${postId}`;
            
            // Unescape HTML entities
            const textarea = document.createElement('textarea');
            textarea.innerHTML = content;
            editContent.value = textarea.value;

            editModal.classList.remove('hidden');
            // Trigger reflow
            void editModal.offsetWidth;
            editModal.classList.remove('opacity-0');
            if (editModalContent) {
                editModalContent.classList.remove('scale-95');
            }
            editContent.focus();
            
            // Hide all dropdown menus globally just in case
            document.querySelectorAll('.post-dropdown-menu').forEach(m => m.classList.add('hidden'));
        };

        window.closeEditModal = function() {
            const editModal = document.getElementById('edit-post-modal');
            const editContent = document.getElementById('edit-post-content');
            const editModalContent = editModal ? editModal.querySelector('.glass-panel') : null;

            if (!editModal) return;
            editModal.classList.add('opacity-0');
            if (editModalContent) {
                editModalContent.classList.add('scale-95');
            }
            setTimeout(() => {
                editModal.classList.add('hidden');
                if (editContent) editContent.value = '';
            }, 300);
        };

        // ===== TOAST NOTIFICATION =====
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            const isSuccess = type === 'success';
            
            toast.className = `flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border backdrop-blur-md transform transition-all duration-300 translate-y-10 opacity-0 ${
                isSuccess 
                ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' 
                : 'bg-rose-500/10 border-rose-500/20 text-rose-400'
            }`;
            
            const icon = isSuccess ? 'check_circle' : 'error';
            
            toast.innerHTML = `
                <span class="material-symbols-outlined">${icon}</span>
                <span class="font-medium text-sm">${message}</span>
            `;

            container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            });

            // Remove after 3s
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        };

        // ===== SHARE MODAL LOGIC =====
        let currentShareUrl = null;
        let currentShareButton = null;

        window.openShareModal = function(shareUrl, buttonEl) {
            const modal = document.getElementById('share-post-modal');
            const contentInput = document.getElementById('share-post-content');
            if (!modal) return;
            
            currentShareUrl = shareUrl;
            currentShareButton = buttonEl;
            if (contentInput) contentInput.value = '';

            modal.classList.remove('hidden');
            // Allow display block to apply before adding opacity
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                const modalContent = modal.querySelector('.glass-panel');
                if (modalContent) modalContent.classList.remove('scale-95');
            });
        };

        window.closeShareModal = function() {
            const modal = document.getElementById('share-post-modal');
            const modalContent = modal ? modal.querySelector('.glass-panel') : null;

            if (!modal) return;
            modal.classList.add('opacity-0');
            if (modalContent) modalContent.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                currentShareUrl = null;
                currentShareButton = null;
            }, 300);
        };

        window.submitShare = function() {
            if (!currentShareUrl) return;
            
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) return;

            const contentInput = document.getElementById('share-post-content');
            const noiDung = contentInput ? contentInput.value : '';
            
            const body = new URLSearchParams();
            body.append('_token', token);
            if (noiDung.trim() !== '') {
                body.append('noi_dung', noiDung);
            }

            const btnSubmit = document.getElementById('confirm-share-btn');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">refresh</span> Đang chia sẻ...';
            }

            fetch(currentShareUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showToast('Đã chia sẻ bài viết thành công!', 'success');
                    if (currentShareButton) {
                        const shareCountSpan = currentShareButton.querySelector('[data-share-count]');
                        if (shareCountSpan && data.shares_count > 0) {
                            shareCountSpan.textContent = `(${data.shares_count})`;
                        }
                    }
                    window.closeShareModal();
                } else {
                    window.showToast(data.message || 'Có lỗi xảy ra khi chia sẻ.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showToast('Lỗi kết nối đến máy chủ.', 'error');
            })
            .finally(() => {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<span class="material-symbols-outlined text-[18px]">share</span> Chia sẻ ngay';
                }
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            const btnCloseEditModal = document.getElementById('close-edit-modal');
            const btnCancelEdit = document.getElementById('cancel-edit-btn');
            const editModal = document.getElementById('edit-post-modal');

            const btnCloseShareModal = document.getElementById('close-share-modal');
            const btnCancelShare = document.getElementById('cancel-share-btn');
            const btnConfirmShare = document.getElementById('confirm-share-btn');
            const shareModal = document.getElementById('share-post-modal');

            if (btnCloseEditModal) btnCloseEditModal.addEventListener('click', window.closeEditModal);
            if (btnCancelEdit) btnCancelEdit.addEventListener('click', window.closeEditModal);
            
            if (btnCloseShareModal) btnCloseShareModal.addEventListener('click', window.closeShareModal);
            if (btnCancelShare) btnCancelShare.addEventListener('click', window.closeShareModal);
            if (btnConfirmShare) btnConfirmShare.addEventListener('click', window.submitShare);

            if (editModal) {
                editModal.addEventListener('click', function(e) {
                    if (e.target === editModal) window.closeEditModal();
                });
            }

            if (shareModal) {
                shareModal.addEventListener('click', function(e) {
                    if (e.target === shareModal) window.closeShareModal();
                });
            }
        });
    </script>
    <script src="/js/theme-toggle.js"></script>
    <script src="/js/language-toggle.js"></script>
    <!-- ===== MODAL CHỈNH SỬA BÀI VIẾT ===== -->
    <div id="edit-post-modal" class="hidden fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="glass-panel rounded-2xl w-full max-w-lg shadow-2xl scale-95 transition-transform duration-300">
            <div class="flex items-center justify-between p-4 border-b border-sky-400/10">
                <h3 class="text-lg font-bold text-on-surface">Chỉnh sửa bài viết</h3>
                <button type="button" id="close-edit-modal" class="p-2 text-slate-400 hover:text-white hover:bg-white/10 rounded-full transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="edit-post-form" method="POST" class="p-4 flex flex-col gap-4">
                @csrf
                @method('PUT')
                <textarea id="edit-post-content" name="noi_dung" rows="5" class="w-full bg-slate-900/50 border border-sky-400/20 rounded-xl focus:ring-1 focus:ring-sky-400 text-slate-100 placeholder-slate-500 resize-none p-3" required></textarea>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cancel-edit-btn" class="px-5 py-2 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Hủy</button>
                    <button type="submit" class="px-5 py-2 text-sm font-semibold bg-sky-500 hover:bg-sky-400 text-white rounded-xl shadow-lg shadow-sky-500/20 transition-all active:scale-95">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== MODAL CHIA SẺ BÀI VIẾT ===== -->
    <div id="share-post-modal" class="hidden fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="glass-panel rounded-2xl w-full max-w-lg shadow-2xl scale-95 transition-transform duration-300">
            <div class="flex items-center justify-between p-4 border-b border-sky-400/10">
                <h3 class="text-lg font-bold text-on-surface">Chia sẻ bài viết</h3>
                <button type="button" id="close-share-modal" class="p-2 text-slate-400 hover:text-white hover:bg-white/10 rounded-full transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-4 flex flex-col gap-4">
                <textarea id="share-post-content" rows="4" placeholder="Viết cảm nghĩ của bạn về bài viết này..." class="w-full bg-slate-900/50 border border-sky-400/20 rounded-xl focus:ring-1 focus:ring-sky-400 text-slate-100 placeholder-slate-500 resize-none p-3"></textarea>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cancel-share-btn" class="px-5 py-2 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Hủy</button>
                    <button type="button" id="confirm-share-btn" class="px-5 py-2 text-sm font-semibold bg-sky-500 hover:bg-sky-400 text-white rounded-xl shadow-lg shadow-sky-500/20 transition-all active:scale-95 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">share</span> Chia sẻ ngay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== GLOBAL CONFIRM MODAL ===== -->
    <div id="global-confirm-modal" class="hidden fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div id="confirm-modal-content" class="glass-panel rounded-2xl w-full max-w-sm shadow-2xl scale-95 transition-transform duration-300 relative overflow-hidden">
            <div class="p-6 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-500/10 mb-4 border border-rose-500/20">
                    <span class="material-symbols-outlined text-rose-500 text-3xl">warning</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-2" id="confirm-modal-title">Xác nhận xóa</h3>
                <p class="text-sm text-slate-400 mb-6" id="confirm-modal-message">Bạn có chắc chắn muốn thực hiện hành động này không?</p>
                
                <div class="flex gap-3">
                    <button type="button" onclick="window.closeConfirmModal()" class="flex-1 px-5 py-2.5 text-sm font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 rounded-xl transition-colors">Hủy</button>
                    <button type="button" id="confirm-modal-submit" class="flex-1 px-5 py-2.5 text-sm font-semibold bg-rose-500 hover:bg-rose-600 text-white rounded-xl shadow-lg shadow-rose-500/20 transition-all active:scale-95">Xóa ngay</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== TOAST NOTIFICATION CONTAINER ===== -->
    <div id="toast-container" class="fixed bottom-20 left-1/2 -translate-x-1/2 sm:bottom-6 sm:left-auto sm:right-6 sm:translate-x-0 z-[110] flex flex-col gap-2 pointer-events-none"></div>

</body>

</html>