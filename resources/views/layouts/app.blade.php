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
                    if (!data.success) {
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

                    if (box) {
                        const noComments = box.querySelector('[data-no-comments]');
                        const list = box.querySelector('[data-comment-list]');

                        if (noComments) {
                            noComments.remove();
                        }

                        const parentId = data.comment.parent_id;
                        const newComment = document.createElement('div');
                        newComment.className = 'rounded-2xl border border-white/10 bg-slate-950 p-3';
                        newComment.dataset.commentId = data.comment.id;
                        newComment.innerHTML = `
                            <div class="flex gap-3 items-start">
                                <img class="w-8 h-8 rounded-full object-cover border border-slate-700" src="${data.comment.user_avatar}" alt="${data.comment.user_name}">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between gap-2 text-sm text-slate-200">
                                        <span class="font-semibold">${data.comment.user_name}</span>
                                        <span class="text-xs text-slate-500">${data.comment.created_at}</span>
                                    </div>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-300">${data.comment.content}</p>
                                </div>
                            </div>
                        `;

                        const replyButton = document.createElement('button');
                        replyButton.type = 'button';
                        replyButton.dataset.commentReplyButton = '';
                        replyButton.dataset.commentId = data.comment.id;
                        replyButton.dataset.commentUser = data.comment.user_name;
                        replyButton.className = 'hover:text-sky-300 text-xs text-slate-400 mt-3';
                        replyButton.textContent = 'Trả lời';

                        const replyWrapper = document.createElement('div');
                        replyWrapper.className = 'mt-3 flex items-center gap-3';
                        replyWrapper.appendChild(replyButton);

                        const replyContainer = document.createElement('div');
                        replyContainer.className = 'mt-3 space-y-3 pl-10';
                        replyContainer.dataset.commentReplies = '';

                        if (parentId) {
                            const parentReplies = list.querySelector('[data-comment-id="' + parentId + '"] [data-comment-replies]');
                            if (parentReplies) {
                                const replyBlock = document.createElement('div');
                                replyBlock.className = newComment.className;
                                replyBlock.dataset.commentId = data.comment.id;
                                replyBlock.innerHTML = newComment.innerHTML;
                                parentReplies.appendChild(replyBlock);
                            } else {
                                list.appendChild(newComment);
                                newComment.appendChild(replyWrapper);
                                newComment.appendChild(replyContainer);
                            }
                        } else {
                            newComment.appendChild(replyWrapper);
                            newComment.appendChild(replyContainer);
                            list.appendChild(newComment);
                        }

                        const parentInput = commentForm.querySelector('input[name="binh_luan_cha_id"]');
                        const actionLabel = commentForm.querySelector('[data-comment-action]');
                        const cancelBtn = commentForm.querySelector('[data-comment-cancel]');
                        if (parentInput) {
                            parentInput.value = '';
                        }
                        if (actionLabel) {
                            actionLabel.textContent = 'Viết bình luận mới';
                        }
                        if (cancelBtn) {
                            cancelBtn.classList.add('hidden');
                        }
                    }
                })
                .catch(function (error) {
                    console.error('Lỗi khi gửi bình luận:', error);
                });
        });
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
    </script>
    <script src="/js/theme-toggle.js"></script>
    <script src="/js/language-toggle.js"></script>
</body>
</html>