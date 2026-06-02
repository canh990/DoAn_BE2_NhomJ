<!DOCTYPE html>
@php
    // apply the user's preferred locale from session for each render
    app()->setLocale(session('personal_locale', config('app.locale', 'vi')));
    $theme = session('personal_theme', null);
    $unreadNotificationsCount = Auth::check() ? Auth::user()->unreadThongBaos()->count() : 0;
@endphp
<html class="{{ $theme === 'light' ? 'light' : 'dark' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NHOMJ')</title>
    
    {{-- Language initialization script - must run before other scripts --}}
    <script src="/js/language-init.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/css/theme-light.css">

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

        /* Notification Animations */
        @keyframes notificationIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .notification-item {
            animation: notificationIn 0.3s ease-out forwards;
        }

        /* Modal Animations */
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-modal-in {
            animation: modalIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .notification-item.removing {
            opacity: 0;
            transform: translateX(20px);
            transition: all 0.3s ease-in;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(125, 211, 252, 0.2);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(125, 211, 252, 0.4);
        }

        /* Textarea Mention Highlighter Styling */
        .textarea-highlighter-wrapper {
            position: relative;
            display: block;
            width: 100%;
        }
        .textarea-highlighter-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            color: #e2e8f0; /* slate-200 / default text color */
            overflow: hidden;
            white-space: pre-wrap;
            word-wrap: break-word;
            z-index: 1;
        }
        .textarea-highlighter-wrapper textarea {
            position: relative;
            background: transparent !important;
            color: transparent !important;
            -webkit-text-fill-color: transparent !important;
            caret-color: #e2e8f0 !important; /* caret must be white/slate-200 */
            z-index: 2;
        }
        .textarea-highlighter-wrapper textarea::placeholder {
            color: #64748b !important;
            -webkit-text-fill-color: #64748b !important;
            opacity: 1 !important;
        }
        .textarea-highlighter-backdrop span {
            color: #38bdf8 !important; /* sky-400 blue */
        }
    </style>
    <!-- Thư viện chuyển đổi HEIC sang JPEG -->
    <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>
</head>
<body class="antialiased selection:bg-primary/30 selection:text-primary">

    <header class="fixed top-0 w-full z-50 bg-[#0a0e1a]/60 backdrop-blur-xl border-b border-sky-400/10 shadow-[0_0_30px_rgba(125,211,252,0.05)] font-inter tracking-tight flex justify-between items-center px-6 h-16">
        <div class="flex items-center gap-8">
            <!-- logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-purple-500 flex items-center justify-center"><span class="material-symbols-outlined text-white">hub</span></div>
                <span class="text-2xl font-bold bg-gradient-to-r from-sky-400 to-purple-400 bg-clip-text text-transparent">NHOMJ</span>
            </a>
            <!-- search -->
            <div class="relative hidden md:block">
                <div class="flex items-center bg-white/5 border border-sky-400/10 rounded-full px-4 py-1.5 focus-within:border-sky-400/30 transition-all">
                    <span class="material-symbols-outlined text-slate-400 text-sm mr-2">search</span>
                    <input id="search-user" type="text" placeholder="{{ __('messages.chat_header_search') }}" autocomplete="off" class="bg-transparent border-none focus:ring-0 text-sm text-on-surface placeholder:text-slate-500 w-64"/>
                </div>

                <!-- dropdown -->
                <div id="search-results" class="hidden absolute top-14 left-0 w-80 bg-[#111827] border border-white/10 rounded-2xl shadow-2xl overflow-hidden z-[9999]"></div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('notifications') }}" class="p-2 text-slate-400 hover:bg-sky-400/10 rounded-xl transition-all active:scale-95 duration-200 relative">
                <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                <span class="notification-badge {{ $unreadNotificationsCount > 0 ? '' : 'hidden' }} absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-[#0a0e1a]">
                    {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                </span>
            </a>
            <a href="{{ route('chat.demo') }}" class="p-2 text-slate-400 hover:bg-sky-400/10 rounded-xl transition-all active:scale-95 duration-200" title="Tin nhắn">
                <span class="material-symbols-outlined" data-icon="mail">mail</span>
            </a>
            @auth
            <a href="{{ route('profile') }}" class="p-1 hover:bg-sky-400/10 rounded-xl transition-all active:scale-95 duration-200 flex items-center gap-2 group" title="Hồ sơ cá nhân">
                <div class="w-8 h-8 overflow-hidden rounded-full border border-sky-400/20 group-hover:border-sky-400/50 transition-colors">
                    <img 
                        class="w-full h-full object-cover" 
                        alt="{{ Auth::user()->name }}" 
                        src="{{ Auth::user()->avatar_url }}" 
                    />
                </div>
            </a>
            @else
            <a href="{{ route('profile') }}" class="p-2 text-sky-300 hover:bg-sky-400/10 rounded-xl transition-all active:scale-95 duration-200" title="Hồ sơ cá nhân">
                <span class="material-symbols-outlined" data-icon="account_circle">account_circle</span>
            </a>
            @endauth
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="button" onclick="window.openConfirmModal('Đăng xuất?', 'Bạn có chắc chắn muốn đăng xuất khỏi tài khoản không?', () => document.getElementById('logout-form').submit(), 'Đăng xuất')" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-400/10 rounded-xl transition-all active:scale-95 duration-200" title="Đăng xuất">
                    <span class="material-symbols-outlined" data-icon="logout">logout</span>
                </button>
            </form>
        </div>
    </header>

    <aside class="fixed left-0 top-16 h-[calc(100vh-64px)] w-64 p-4 border-r border-sky-400/10 flex flex-col gap-2 z-40 hidden md:flex">
     @auth
    @php $user = Auth::user(); @endphp

  <a href="{{ route('profile') }}" class="mb-4 px-4 py-2 block hover:bg-white/5 rounded-2xl transition-all group">
    <div class="flex items-center gap-3 mb-1">
        <div class="w-10 h-10 shrink-0 overflow-hidden rounded-full border border-sky-400/30 group-hover:border-sky-400/60 transition-colors">
            <img 
                class="w-full h-full object-cover" 
                alt="{{ $user->name }}" 
                src="{{ $user->avatar_url }}" 
            />
        </div>
        
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-1">
                <p class="text-sm font-bold text-sky-300 font-inter leading-tight group-hover:text-sky-200 transition-colors truncate" title="{{ $user->name }}">
                    {{ $user->name }}
                </p>
                @if($user->da_xac_thuc)
                <span class="material-symbols-outlined text-[14px] text-sky-400 shrink-0" data-icon="verified" style="font-variation-settings: 'FILL' 1;" title="Đã xác thực">
                    verified
                </span>
                @endif
            </div>
            <p class="text-[11px] text-slate-500 font-medium group-hover:text-slate-400 transition-colors truncate" title="{{ '@' . ($user->ten_dang_nhap ?? 'nguoidung') }}">
                {{ '@' . ($user->ten_dang_nhap ?? 'nguoidung') }}
            </p>
        </div>
    </div>
</a>
@endauth
        <nav class="flex flex-col gap-1 flex-1">
            <a class="flex items-center gap-3 {{ request()->routeIs('home') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="{{ route('home') }}">
                <span class="material-symbols-outlined" data-icon="home">home</span>
                <span class="text-lg font-medium">{{ __('messages.home_title') }}</span>
            </a>
            <a class="flex items-center gap-3 {{ request()->routeIs('explore') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="{{ route('explore') }}">
                <span class="material-symbols-outlined" data-icon="explore">explore</span>
                <span class="text-lg font-medium">{{ __('messages.explore_title') }}</span>
            </a>
            <a class="flex items-center gap-3 {{ request()->routeIs('notifications') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium relative group" href="{{ route('notifications') }}">
                <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                <span class="text-lg font-medium">{{ __('messages.notifications_title') }}</span>
                <span class="notification-badge {{ $unreadNotificationsCount > 0 ? '' : 'hidden' }} absolute top-3 right-4 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-[#0a0e1a] group-hover:scale-110 transition-transform">
                    {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                </span>
            </a>

            <a class="flex items-center gap-3 {{ request()->routeIs('chat.demo') || request()->routeIs('chat.user.*') || request()->routeIs('chat.messages.*') || request()->routeIs('chat.conversations.*') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="{{ route('chat.demo') }}">
                <span class="material-symbols-outlined" data-icon="chat">chat</span>
                <span class="text-lg font-medium">{{ __('messages.chat_title') }}</span>
            </a>
            <a class="flex items-center gap-3 {{ request()->routeIs('profile') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="{{ route('profile') }}">
                <span class="material-symbols-outlined" data-icon="person">person</span>
                <span class="text-lg font-medium">{{ __('messages.person_title') }}</span>
            </a>
            <a class="flex items-center gap-3 {{ request()->routeIs('bookmarks.index') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="{{ route('bookmarks.index') }}">
                <span class="material-symbols-outlined" data-icon="bookmark">bookmark</span>
                <span class="text-lg font-medium">{{ __('messages.bookmarks') }}</span>
            </a>
            <a href="{{ route('settings.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-full transition-all hover:bg-white/10 group">
    <span class="material-symbols-outlined" data-icon="settings">settings</span>
    <span class="text-lg font-medium">{{ __('messages.settings_title') }}</span>
</a>
        </nav>
        <button class="mt-4 w-full py-3 bg-sky-400/20 border border-sky-400/30 text-sky-300 font-bold rounded-xl hover:bg-sky-400/30 transition-all active:scale-95">
            {{ __('messages.new_post') }}
        </button>
    </aside>

    <main class="md:ml-64 pt-16 min-h-screen">
        @yield('content')
    </main>

    <nav class="md:hidden fixed bottom-0 w-full glass-panel-elevated flex justify-around items-center h-16 z-50 border-t border-sky-400/10">
        <a href="{{ route('home') }}" class="p-2 {{ request()->routeIs('home') ? 'text-sky-300 bg-sky-400/20 rounded-xl' : 'text-slate-400' }}">
            <span class="material-symbols-outlined" data-icon="home">home</span>
        </a>
        <a href="{{ route('explore') }}" class="p-2 {{ request()->routeIs('explore') ? 'text-sky-300 bg-sky-400/20 rounded-xl' : 'text-slate-400' }}">
            <span class="material-symbols-outlined" data-icon="explore">explore</span>
        </a>
        <a href="{{ route('profile') }}" class="p-2 {{ request()->routeIs('profile') ? 'text-sky-300 bg-sky-400/20 rounded-xl' : 'text-slate-400' }}">
            @auth
            <div class="w-7 h-7 overflow-hidden rounded-full border {{ request()->routeIs('profile') ? 'border-sky-400' : 'border-slate-500' }}">
                <img 
                    class="w-full h-full object-cover" 
                    alt="{{ Auth::user()->name }}" 
                    src="{{ Auth::user()->avatar_url }}" 
                />
            </div>
            @else
            <span class="material-symbols-outlined" data-icon="person">person</span>
            @endauth
        </a>
        <a href="{{ route('notifications') }}" class="p-2 {{ request()->routeIs('notifications') ? 'text-sky-300 bg-sky-400/20 rounded-xl' : 'text-slate-400' }} relative">
            <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
            <span class="notification-badge {{ $unreadNotificationsCount > 0 ? '' : 'hidden' }} absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-[#0a0e1a]">
                {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
            </span>
        </a>
        <a href="{{ route('chat.demo') }}" class="p-2 {{ request()->routeIs('chat.demo') ? 'text-sky-300 bg-sky-400/20 rounded-xl' : 'text-slate-400' }}">
            <span class="material-symbols-outlined" data-icon="mail">mail</span>
        </a>
    </nav>

    <script>
        document.addEventListener('click', function (event) {
            const reactionTrigger = event.target.closest('[data-reaction-trigger]');
            const reactionOption = event.target.closest('[data-reaction-option]');
            const commentToggle = event.target.closest('[data-comment-toggle]');
            const commentReplyButton = event.target.closest('[data-comment-reply-button]');
            const commentCancel = event.target.closest('[data-comment-cancel]');
            const shareButton = event.target.closest('[data-share-button]');
            const bookmarkButton = event.target.closest('[data-bookmark-button]');
            const pinButton = event.target.closest('.btn-toggle-pin');
            const pollOptionBtn = event.target.closest('.poll-option-btn');
            const readMoreToggleBtn = event.target.closest('[data-read-more-toggle]');
            const reactionAreas = document.querySelectorAll('[data-reaction-area]');

            if (readMoreToggleBtn) {
                event.preventDefault();
                const previewId = readMoreToggleBtn.dataset.previewId;
                const fullId = readMoreToggleBtn.dataset.fullId;

                const isExpanded = readMoreToggleBtn.dataset.expanded === '1';

                if (previewId && fullId) {
                    const previewContent = document.getElementById(previewId);
                    const fullContent = document.getElementById(fullId);
                    if (!previewContent || !fullContent) return;

                    if (isExpanded) {
                        fullContent.classList.add('hidden');
                        previewContent.classList.remove('hidden');
                        readMoreToggleBtn.dataset.expanded = '0';
                        readMoreToggleBtn.textContent = 'Xem thêm';
                    } else {
                        previewContent.classList.add('hidden');
                        fullContent.classList.remove('hidden');
                        readMoreToggleBtn.dataset.expanded = '1';
                        readMoreToggleBtn.textContent = 'Thu gọn';
                    }
                } else {
                    const targetId = readMoreToggleBtn.dataset.targetId;
                    const targetContent = targetId ? document.getElementById(targetId) : null;
                    if (!targetContent) return;
                    const collapsedMaxHeight = targetContent.dataset.collapsedMaxHeight || '7.5rem';

                    if (isExpanded) {
                        targetContent.style.maxHeight = collapsedMaxHeight;
                        targetContent.style.overflow = 'hidden';
                        readMoreToggleBtn.dataset.expanded = '0';
                        readMoreToggleBtn.textContent = 'Xem thêm';
                    } else {
                        targetContent.style.maxHeight = 'none';
                        targetContent.style.overflow = 'visible';
                        readMoreToggleBtn.dataset.expanded = '1';
                        readMoreToggleBtn.textContent = 'Thu gọn';
                    }
                }

                return;
            }

            if (pollOptionBtn) {
                event.preventDefault();
                const container = pollOptionBtn.closest('.poll-container');
                if (!container) return;

                const pollId = pollOptionBtn.dataset.pollId;
                const optionId = pollOptionBtn.dataset.optionId;

                fetch(`/polls/${pollId}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ lua_chon_id: optionId }),
                })
                    .then(async (response) => {
                        const data = await response.json().catch(() => null);
                        if (!response.ok) {
                            throw new Error(data?.message || 'Có lỗi xảy ra khi bình chọn.');
                        }
                        return data;
                    })
                    .then((data) => {
                        if (!data.success) {
                            if (typeof window.showToast === 'function') window.showToast(data.message || 'Không thể bình chọn.', 'error');
                            return;
                        }

                        container.dataset.hasVoted = '1';
                        const totalVotes = data.total_votes || 0;
                        const selectedId = String(data.user_voted_option_id || '');
                        const buttons = container.querySelectorAll('.poll-option-btn');

                        buttons.forEach((btn) => {
                            btn.classList.remove('bg-slate-800/60', 'hover:bg-slate-700');
                            btn.classList.add('bg-slate-800/30');

                            const optId = String(btn.dataset.optionId);
                            const progress = btn.querySelector('.poll-progress');
                            const optionStat = Array.isArray(data.options) ? data.options.find(o => String(o.id) === optId) : null;
                            const votes = optionStat ? Number(optionStat.votes_count || 0) : 0;
                            const percentage = totalVotes > 0 ? Math.round((votes / totalVotes) * 100) : 0;
                            const isSelected = optId === selectedId;

                            if (progress) {
                                progress.style.width = `${percentage}%`;
                                progress.classList.toggle('bg-sky-500/30', isSelected);
                                progress.classList.toggle('bg-slate-600/40', !isSelected);
                            }

                            const textSpan = btn.querySelector('span');
                            if (textSpan) {
                                textSpan.classList.toggle('text-sky-300', isSelected);
                                textSpan.classList.toggle('text-slate-200', !isSelected);
                            }

                            let pct = btn.querySelector('.poll-percentage');
                            if (!pct) {
                                pct = document.createElement('span');
                                pct.className = 'relative z-10 text-sm poll-percentage';
                                btn.appendChild(pct);
                            }
                            pct.textContent = `${percentage}%`;
                            pct.classList.toggle('text-sky-300', isSelected);
                            pct.classList.toggle('font-bold', isSelected);
                            pct.classList.toggle('text-slate-400', !isSelected);
                        });

                        let totalNode = container.querySelector('.poll-total');
                        if (!totalNode) {
                            totalNode = document.createElement('div');
                            totalNode.className = 'text-sm text-slate-400 mt-2 poll-total';
                            container.appendChild(totalNode);
                        }
                        totalNode.textContent = `${totalVotes} lượt bình chọn`;

                        const hintNode = Array.from(container.querySelectorAll('div')).find(el => el.textContent && el.textContent.includes('Bình chọn để xem kết quả'));
                        if (hintNode) hintNode.remove();
                    })
                    .catch((error) => {
                        if (typeof window.showToast === 'function') {
                            window.showToast(error.message || 'Có lỗi xảy ra khi bình chọn.', 'error');
                        } else {
                            alert(error.message || 'Có lỗi xảy ra khi bình chọn.');
                        }
                    });

                return;
            }

            // ==========================================
            // CHỨC NĂNG BOOKMARK: Lưu/Bỏ lưu bài viết
            // ==========================================
            if (bookmarkButton) {
                event.preventDefault();
                const postId = bookmarkButton.dataset.postId;
                const icon = bookmarkButton.querySelector('[data-bookmark-icon]');
                const text = bookmarkButton.querySelector('[data-bookmark-text]');
                
                // Đóng reaction picker của bài viết này nếu đang mở để tránh giao diện bị đè chồng
                const area = bookmarkButton.closest('[data-reaction-area]');
                const picker = area?.querySelector('[data-reaction-picker]');
                if (picker) {
                    picker.classList.add('hidden');
                }
                
                // Gửi request toggle bookmark bài viết qua AJAX
                fetch(`/posts/${postId}/bookmark`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    // Check lỗi phân quyền (chưa đăng nhập)
                    if (response.status === 401) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Vui lòng đăng nhập để thực hiện chức năng này.', 'error');
                        } else {
                            alert('Vui lòng đăng nhập để thực hiện chức năng này.');
                        }
                        throw new Error('Unauthorized');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Cập nhật trạng thái icon và text trên giao diện realtime
                        const isBookmarked = data.is_bookmarked;
                        if (isBookmarked) {
                            icon.classList.add('text-yellow-400');
                            icon.style.fontVariationSettings = "'FILL' 1";
                            if (text) text.textContent = 'Đã lưu';
                        } else {
                            icon.classList.remove('text-yellow-400');
                            icon.style.fontVariationSettings = "'FILL' 0";
                            if (text) text.textContent = 'Lưu';
                        }
                        
                        // Nếu đang ở trang danh sách Bookmark mà bấm bỏ lưu, ẩn bài viết đó đi bằng hiệu ứng mượt
                        if (window.location.pathname === '/bookmarks' && !isBookmarked) {
                            const postCard = bookmarkButton.closest('article');
                            if (postCard) {
                                postCard.style.transition = 'all 0.5s ease';
                                postCard.style.opacity = '0';
                                postCard.style.transform = 'scale(0.95)';
                                setTimeout(() => {
                                    postCard.remove();
                                    const remainingCards = document.querySelectorAll('main article');
                                    if (remainingCards.length === 0) {
                                        window.location.reload();
                                    }
                                }, 500);
                            }
                        }
                        
                        if (typeof window.showToast === 'function') {
                            window.showToast(data.message, 'success');
                        }
                    } else {
                        if (typeof window.showToast === 'function') {
                            window.showToast(data.message || 'Có lỗi xảy ra', 'error');
                        }
                    }
                })
                .catch(error => {
                    if (error.message !== 'Unauthorized') {
                        console.error('Lỗi khi lưu bài viết:', error);
                        if (typeof window.showToast === 'function') {
                            window.showToast('Không thể kết nối máy chủ.', 'error');
                        }
                    }
                });
                return;
            }

            // CHỨC NĂNG PIN BÀI VIẾT: Ghim/Bỏ ghim bài đăng
            if (pinButton) {
                event.preventDefault();
                const postId = pinButton.dataset.postId;
                const isPinned = pinButton.dataset.pinned === '1';

                // Đóng dropdown menu tùy chọn bài viết
                const dropdownMenu = pinButton.closest('.post-dropdown-menu');
                if (dropdownMenu) {
                    dropdownMenu.classList.add('hidden');
                }

                // Dùng localization fallback dạng inline để tránh sửa file messages.php
                const pinnedText = "{{ app()->getLocale() === 'en' ? 'Pinned post' : 'Bài viết đã ghim' }}";
                const pinLabel = "{{ app()->getLocale() === 'en' ? 'Pin post' : 'Ghim bài viết' }}";
                const unpinLabel = "{{ app()->getLocale() === 'en' ? 'Unpin post' : 'Bỏ ghim bài viết' }}";

                // Gửi request ghim/bỏ ghim bài viết qua AJAX
                fetch(`/posts/${postId}/pin`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    // Check lỗi phân quyền (chưa đăng nhập hoặc không phải chủ bài viết)
                    if (response.status === 401) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Vui lòng đăng nhập để thực hiện.', 'error');
                        }
                        throw new Error('Unauthorized');
                    }
                    if (response.status === 403) {
                        return response.json().then(data => {
                            if (typeof window.showToast === 'function') {
                                window.showToast(data.message || 'Bạn không có quyền thực hiện.', 'error');
                            }
                            throw new Error('Forbidden');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const nowPinned = data.da_ghim;

                        if (nowPinned) {
                            // Find and unpin any other pinned posts in frontend
                            document.querySelectorAll('.btn-toggle-pin[data-pinned="1"]').forEach(btn => {
                                if (btn.dataset.postId !== postId) {
                                    btn.dataset.pinned = '0';
                                    const otherIcon = btn.querySelector('.pin-icon');
                                    if (otherIcon) {
                                        otherIcon.textContent = 'push_pin';
                                        otherIcon.style.fontVariationSettings = "'FILL' 0";
                                    }
                                    const otherText = btn.querySelector('.pin-text');
                                    if (otherText) otherText.textContent = pinLabel;
                                    
                                    const otherCard = btn.closest('article');
                                    if (otherCard) {
                                        const indicator = otherCard.querySelector('.pinned-indicator-container');
                                        if (indicator) indicator.innerHTML = '';
                                    }
                                }
                            });
                        }

                        // Update current button
                        pinButton.dataset.pinned = nowPinned ? '1' : '0';
                        const icon = pinButton.querySelector('.pin-icon');
                        if (icon) {
                            icon.textContent = 'push_pin';
                            icon.style.fontVariationSettings = nowPinned ? "'FILL' 1" : "'FILL' 0";
                        }
                        const text = pinButton.querySelector('.pin-text');
                        if (text) text.textContent = nowPinned ? unpinLabel : pinLabel;

                        // Update current post card indicator
                        const postCard = pinButton.closest('article');
                        if (postCard) {
                            const isProfilePageOfAuthor = postCard.dataset.isProfilePageOfAuthor === '1';
                            const indicatorContainer = postCard.querySelector('.pinned-indicator-container');
                            if (indicatorContainer) {
                                if (nowPinned && isProfilePageOfAuthor) {
                                    indicatorContainer.innerHTML = `
                                        <div class="pinned-indicator inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-400 border border-sky-400/20 mb-2 select-none shadow-sm shadow-sky-500/5">
                                            <span class="mr-0.5 text-[11px]">📌</span>
                                            <span>${pinnedText}</span>
                                        </div>
                                    `;
                                } else {
                                    indicatorContainer.innerHTML = '';
                                }
                            }

                            // Prepend post card if in profile tab feed
                            const container = document.getElementById('post-list-container');
                            if (container && nowPinned && isProfilePageOfAuthor) {
                                // Add smooth fade transition
                                postCard.style.transition = 'opacity 0.25s ease';
                                postCard.style.opacity = '0.3';
                                setTimeout(() => {
                                    container.prepend(postCard);
                                    postCard.style.opacity = '1';
                                    // Smooth scroll to top of feed
                                    const feedTop = container.getBoundingClientRect().top + window.scrollY - 100;
                                    window.scrollTo({ top: feedTop, behavior: 'smooth' });
                                }, 250);
                            }
                        }

                        if (typeof window.showToast === 'function') {
                            window.showToast(data.message, 'success');
                        }
                    } else {
                        if (typeof window.showToast === 'function') {
                            window.showToast(data.message || 'Có lỗi xảy ra', 'error');
                        }
                    }
                })
                .catch(error => {
                    if (error.message !== 'Unauthorized' && error.message !== 'Forbidden') {
                        console.error('Lỗi khi ghim bài viết:', error);
                        if (typeof window.showToast === 'function') {
                            window.showToast('Không thể kết nối máy chủ.', 'error');
                        }
                    }
                });
                return;
            }

            // ==========================================
            // HỦY TRẢ LỜI: Hủy phản hồi và đưa ô input về trạng thái viết bình luận mới
            // ==========================================
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

            // ==========================================
            // TRẢ LỜI BÌNH LUẬN: Kích hoạt chế độ trả lời bình luận cha
            // ==========================================
            if (commentReplyButton) {
                event.preventDefault();
                const area = commentReplyButton.closest('[data-reaction-area]');
                const form = area?.querySelector('.comment-submit-form');
                if (form) {
                    // Gán ID bình luận cha vào input ẩn để gửi lên server đúng luồng trả lời
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

            // ==========================================
            // ĐÓNG/MỞ HỘP THOẠI BÌNH LUẬN: Toggle hiển thị danh sách và ô nhập bình luận
            // ==========================================
            if (commentToggle) {
                event.stopPropagation();
                const area = commentToggle.closest('[data-reaction-area]');
                const box = area?.querySelector('[data-comment-box]');
                if (box) {
                    box.classList.toggle('hidden');
                }
                return;
            }

            // ==========================================
            // THẢ CẢM XÚC (LIKE/REACTION): Gửi AJAX cảm xúc đã chọn
            // ==========================================
            if (reactionOption) {
                event.stopPropagation();
                const reaction = reactionOption.dataset.reaction;
                const label = reactionOption.dataset.reactionLabel;
                const color = reactionOption.dataset.reactionColor;
                // Phải dùng getAttribute vì dataset API KHÔNG chuyển đổi dấu gạch ngang trước chữ số
                // dataset.reaction3d tìm data-reaction3d (sai), getAttribute tìm đúng data-reaction-3d
                const selected3d = reactionOption.getAttribute('data-reaction-3d');
                const area = reactionOption.closest('[data-reaction-area]');
                const form = area.querySelector('.reaction-submit-form');
                const action = form.action;
                const token = form.querySelector('input[name="_token"]').value;
                const body = new URLSearchParams();

                body.append('_token', token);
                body.append('loai_cam_xuc', reaction);

                // Cập nhật giao diện lập tức (Optimistic UI Update) & Ẩn picker
                const picker = area.querySelector('[data-reaction-picker]');
                if (picker) picker.classList.add('hidden');

                
                const triggerIconContainer = area.querySelector('[data-reaction-trigger-icon-container]');
                const triggerLabel = area.querySelector('[data-reaction-trigger-label]');
                const triggerBtn = area.querySelector('[data-reaction-trigger]');
                
                if (triggerIconContainer) {
                    triggerIconContainer.innerHTML = '<img src="' + selected3d + '" class="w-6 h-6 sm:w-7 sm:h-7 object-contain drop-shadow-md" data-reaction-trigger-img>';
                }
                if (triggerBtn) {
                    triggerBtn.className = 'group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 transition-all duration-300 bg-sky-400/10 text-sky-400';
                    triggerBtn.dataset.currentReaction = reaction;
                }
                if (triggerLabel) {
                    triggerLabel.textContent = label;
                    triggerLabel.className = 'text-[13px] sm:text-sm font-semibold tracking-wide ' + color;
                }

                // Gửi request cập nhật cảm xúc qua AJAX
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

                        // Cập nhật biểu tượng và màu sắc của nút cảm xúc tương ứng realtime
                        const triggerIconContainer = area.querySelector('[data-reaction-trigger-icon-container]');
                        const triggerLabel = area.querySelector('[data-reaction-trigger-label]');
                        const triggerBtn = area.querySelector('[data-reaction-trigger]');
                        const countNode = area.querySelector('[data-reaction-count]');
                        const picker = area.querySelector('[data-reaction-picker]');
                        const isRemoved = data.removed;

                        if (triggerIconContainer) {
                            if (isRemoved) {
                                triggerIconContainer.innerHTML = '<span class="material-symbols-outlined text-[20px] sm:text-[22px] text-slate-400" data-reaction-trigger-icon>sentiment_satisfied</span>';
                            } else {
                                triggerIconContainer.innerHTML = '<img src="' + selected3d + '" class="w-6 h-6 sm:w-7 sm:h-7 object-contain drop-shadow-md" data-reaction-trigger-img>';
                            }
                        }

                        if (triggerBtn) {
                            if (isRemoved) {
                                triggerBtn.className = 'group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 transition-all duration-300 text-slate-400 hover:bg-slate-800/60 hover:text-sky-300';
                                triggerBtn.dataset.currentReaction = '';
                            } else {
                                triggerBtn.className = 'group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 transition-all duration-300 bg-sky-400/10 text-sky-400';
                                triggerBtn.dataset.currentReaction = reaction;
                            }
                        }

                        if (triggerLabel) {
                            if (isRemoved) {
                                triggerLabel.textContent = "{{ __('messages.post_like') }}";
                                triggerLabel.className = 'text-[13px] sm:text-sm font-semibold tracking-wide text-slate-400';
                            } else {
                                triggerLabel.textContent = label;
                                triggerLabel.className = 'text-[13px] sm:text-sm font-semibold tracking-wide ' + color;
                            }
                        }

                        if (countNode) {
                            countNode.textContent = data.reactions_count;
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

            if (reactionTrigger) {
                event.preventDefault();
                event.stopPropagation();
                
                const area = reactionTrigger.closest('[data-reaction-area]');
                const currentReaction = reactionTrigger.dataset.currentReaction;
                
                // Đóng các picker khác nếu có
                reactionAreas.forEach(function (a) {
                    if (a !== area) {
                        const otherPicker = a.querySelector('[data-reaction-picker]');
                        if (otherPicker) otherPicker.classList.add('hidden');
                    }
                });
                
                if (currentReaction) {
                    // CÓ CẢM XÚC: Click sẽ HỦY/GỠ cảm xúc này
                    const form = area.querySelector('.reaction-submit-form');
                    const action = form.action;
                    const token = form.querySelector('input[name="_token"]').value;
                    const body = new URLSearchParams();
                    body.append('_token', token);
                    body.append('loai_cam_xuc', currentReaction);
                    
                    // Ẩn picker nếu đang mở
                    const picker = area.querySelector('[data-reaction-picker]');
                    if (picker) picker.classList.add('hidden');
                    
                    // Optimistic UI Update: Quay về trạng thái Thích mặc định
                    const triggerIconContainer = area.querySelector('[data-reaction-trigger-icon-container]');
                    const triggerLabel = area.querySelector('[data-reaction-trigger-label]');
                    
                    if (triggerIconContainer) {
                        triggerIconContainer.innerHTML = '<span class="material-symbols-outlined text-[20px] sm:text-[22px] text-slate-400" data-reaction-trigger-icon>sentiment_satisfied</span>';
                    }
                    reactionTrigger.className = 'group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 transition-all duration-300 text-slate-400 hover:bg-slate-800/60 hover:text-sky-300';
                    if (triggerLabel) {
                        triggerLabel.textContent = "{{ __('messages.post_like') }}";
                        triggerLabel.className = 'text-[13px] sm:text-sm font-semibold tracking-wide text-slate-400';
                    }
                    reactionTrigger.dataset.currentReaction = '';
                    
                    // Gửi request gỡ cảm xúc qua AJAX
                    fetch(action, {
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
                            const countNode = area.querySelector('[data-reaction-count]');
                            if (countNode) {
                                countNode.textContent = data.reactions_count;
                            }
                        }
                    });
                } else {
                    // CHƯA CÓ CẢM XÚC: Click sẽ MỞ/TẮT menu chọn cảm xúc (picker)
                    const picker = area.querySelector('[data-reaction-picker]');
                    if (picker) {
                        picker.classList.toggle('hidden');
                    }
                }
                return;
            }

            // Click ngoài các khu vực area sẽ ẩn picker
            reactionAreas.forEach(function (area) {
                const picker = area.querySelector('[data-reaction-picker]');
                if (picker && !area.contains(event.target)) {
                    picker.classList.add('hidden');
                }
            });
        });

        // LẮNG NGHE SỰ KIỆN SUBMIT BÌNH LUẬN: Gửi bình luận qua AJAX và cập nhật giao diện realtime không cần tải lại trang
        document.addEventListener('submit', function (event) {
            const commentForm = event.target.closest('.comment-submit-form');
            if (!commentForm) {
                return;
            }

            event.preventDefault();
            const action = commentForm.action;
            // Sử dụng FormData để thu thập toàn bộ dữ liệu gồm text nội dung và các tệp tin media (ảnh, video) đính kèm
            const body = new FormData(commentForm);
            console.log('Submitting comment content:', commentForm.querySelector('textarea[name="noi_dung"]').value);
            
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
                    // XỬ LÝ LỖI VALIDATION (422) HOẶC LỖI TỪ SERVER
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

                    // CẬP NHẬT GIAO DIỆN SAU KHI ĐĂNG THÀNH CÔNG
                    const area = commentForm.closest('[data-reaction-area]');
                    const countNode = area?.querySelector('[data-comment-count]');
                    const box = commentForm.closest('[data-comment-box]');
                    const textarea = commentForm.querySelector('textarea[name="noi_dung"]');

                    // Cập nhật số lượng bình luận hiển thị trên bài viết
                    if (countNode) {
                        countNode.textContent = '(' + data.comments_count + ')';
                    }

                    // Reset ô nhập liệu văn bản và trigger event 'input' để đồng bộ hóa highlighter nhắc tên (@)
                    if (textarea) {
                        textarea.value = '';
                        textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    }

                    // Reset input media và vùng hiển thị preview ảnh/video đính kèm
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

                    // DỰNG DOM BÌNH LUẬN MỚI REALTIME
                    if (box) {
                        const noComments = box.querySelector('[data-no-comments]');
                        const list = box.querySelector('[data-comment-list]');

                        if (noComments) {
                            noComments.remove();
                        }

                        const parentId = data.comment.parent_id;
                        
                        // Tạo khối bao quanh luồng (thread) bình luận
                        const newThread = document.createElement('div');
                        newThread.className = 'comment-thread w-full';
                        newThread.dataset.commentId = data.comment.id;

                        // Tạo khối chứa nội dung bình luận
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
                                    
                                    <!-- Render Media đính kèm nếu có -->
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

                        // Nút phản hồi (Trả lời) bình luận này
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

                        // Tạo container chứa các bình luận con (replies)
                        const replyContainer = document.createElement('div');
                        replyContainer.className = 'mt-2 space-y-2 pl-6 sm:pl-12 relative';
                        replyContainer.dataset.commentReplies = '';
                        newThread.appendChild(replyContainer);

                        // XÁC ĐỊNH VỊ TRÍ CHÈN BÌNH LUẬN TRÊN CÂY GIAO DIỆN
                        if (parentId) {
                            // Nếu là bình luận con: Tìm container phản hồi của bình luận cha để chèn vào
                            const parentReplies = list.querySelector('[data-comment-id="' + parentId + '"] [data-comment-replies]');
                            if (parentReplies) {
                                let verticalLine = parentReplies.querySelector('.absolute.bg-white\\/10');
                                if (!verticalLine) {
                                    // Tạo đường kẻ dọc kết nối các bình luận phản hồi cho trực quan
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
                            // Nếu là bình luận gốc (cha cấp cao nhất): Chèn lên đầu danh sách bình luận bài viết
                            if (list.firstChild && list.firstChild.dataset && list.firstChild.dataset.noComments !== undefined) {
                                list.innerHTML = '';
                                list.appendChild(newThread);
                            } else {
                                if (list.firstChild) list.insertBefore(newThread, list.firstChild);
                                else list.appendChild(newThread);
                            }
                        }
                        
                        // LƯU Ý: Không reset parentInput, actionLabel, cancelBtn để người dùng có thể liên tục phản hồi cùng bình luận đó.
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
        // XỬ LÝ QUẢN LÝ VÀ HIỂN THỊ HÌNH ẢNH/VIDEO ĐÍNH KÈM KHI BÌNH LUẬN (Realtime Preview)
        window.handleCommentMediaSelect = function(input, isUpdate = false) {
            const previewContainer = input.parentElement.querySelector('.comment-media-preview');
            if (!previewContainer) return;
            
            // Khởi tạo mảng lưu trữ danh sách tệp đã chọn nếu chưa tồn tại
            if (!input._selectedFiles) {
                input._selectedFiles = [];
            }
            
            // Nếu người dùng vừa thực hiện chọn tệp (không phải gọi lại từ hàm xóa phần tử)
            if (!isUpdate && input.files && input.files.length > 0) {
                Array.from(input.files).forEach(file => {
                    // TRÁNH CHỌN TRÙNG LẶP: So sánh tên và kích thước tệp để không đưa vào 2 lần
                    const exists = input._selectedFiles.some(f => f.name === file.name && f.size === file.size);
                    if (!exists) {
                        input._selectedFiles.push(file);
                    }
                });
            }
            
            // Xóa sạch khung xem trước cũ để vẽ lại toàn bộ danh sách mới
            previewContainer.innerHTML = '';
            
            if (input._selectedFiles.length > 0) {
                previewContainer.classList.remove('hidden');
                
                // Sử dụng đối tượng DataTransfer để đồng bộ tệp vật lý với thẻ input file của HTML form
                const dt = new DataTransfer();
                input._selectedFiles.forEach((file, index) => {
                    dt.items.add(file);
                    
                    // Tạo khối bao quanh thumbnail preview
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden border border-white/20 shadow-sm group shrink-0 bg-slate-900/50';
                    
                    // Nút xóa nhanh tệp đã chọn
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'absolute top-1 right-1 bg-slate-900/80 hover:bg-rose-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-all duration-200 flex items-center justify-center z-10';
                    removeBtn.innerHTML = '<span class="material-symbols-outlined text-[14px]">close</span>';
                    removeBtn.onclick = function(e) {
                        e.preventDefault();
                        wrapper.remove();
                        // Xóa tệp khỏi mảng lưu trữ tạm
                        input._selectedFiles.splice(index, 1);
                        // Gọi đệ quy cập nhật lại danh sách preview và input
                        window.handleCommentMediaSelect(input, true);
                    };
                    
                    // Phân biệt định dạng Video và Hình ảnh để render thẻ HTML cho đúng
                    if (file.type.startsWith('video/')) {
                        const video = document.createElement('video');
                        video.src = URL.createObjectURL(file);
                        video.className = 'w-full h-full object-cover';
                        wrapper.appendChild(video);
                        
                        // Icon play giả định đè lên thumbnail video
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
                
                // Gán lại danh sách tệp mới đã chuẩn hóa cho thuộc tính files của thẻ input
                input.files = dt.files;
            } else {
                // Nếu không còn tệp nào được chọn, ẩn vùng xem trước và làm sạch input file
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
        window.openConfirmModal = function(title, message, callback, btnText = 'Xác nhận') {
            const modal = document.getElementById('global-confirm-modal');
            if (!modal) return;
            
            document.getElementById('confirm-modal-title').textContent = title;
            document.getElementById('confirm-modal-message').textContent = message;
            
            const submitBtn = document.getElementById('confirm-modal-submit');
            if (submitBtn) {
                submitBtn.textContent = btnText;
            }
            
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

        // ===== CHỨC NĂNG CHIA SẺ BÀI VIẾT (SHARE POST MODAL) =====
        let currentShareUrl = null;
        let currentShareButton = null;

        // MỞ MODAL CHIA SẺ: Lưu lại URL chia sẻ và phần tử nút kích hoạt để lát cập nhật số liệu
        window.openShareModal = function(shareUrl, buttonEl) {
            const modal = document.getElementById('share-post-modal');
            const contentInput = document.getElementById('share-post-content');
            if (!modal) return;
            
            currentShareUrl = shareUrl;
            currentShareButton = buttonEl;
            if (contentInput) contentInput.value = ''; // Reset ô nhập nội dung chia sẻ

            modal.classList.remove('hidden');
            // Đợi hiệu ứng trình duyệt áp dụng hiển thị trước khi chạy hiệu ứng mờ/scale
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                const modalContent = modal.querySelector('.glass-panel');
                if (modalContent) modalContent.classList.remove('scale-95');
            });
        };

        // ĐÓNG MODAL CHIA SẺ: Ẩn giao diện modal với hiệu ứng và dọn dẹp biến tạm
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

        // GỬI REQUEST CHIA SẺ: Gọi AJAX lên endpoint chia sẻ của bài viết
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
                    window.showToast('Chia sẻ thành công', 'success');
                    
                    // CHÈN BÀI VIẾT MỚI VÀO FEED REALTIME:
                    // Trích xuất mã HTML nhận được từ response và đẩy thẳng lên đầu danh sách bài viết hiển thị
                    const container = document.getElementById('post-list-container');
                    if (container && data.html) {
                        const temp = document.createElement('div');
                        temp.innerHTML = data.html;
                        const newPost = temp.firstElementChild;
                        if (newPost) {
                            container.prepend(newPost);
                            // Cuộn mượt màn hình lên đầu danh sách bài đăng
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    }

                    // Cập nhật lại huy hiệu đếm số lượt chia sẻ trên nút bài viết gốc
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
    <x-edit-modal />
    <x-share-modal />
    <x-confirm-modal />

    <x-post-modal />
    <x-toast />

    <script>
        function updateGlobalNotificationCount() {
            fetch('/notifications/unread-count')
            .then(res => res.json())
            .then(data => {
                const badges = document.querySelectorAll('.notification-badge');
                badges.forEach(badge => {
                    if(data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                });
            })
            .catch(err => console.error('Error fetching notification count:', err));
        }

        @auth
            // Poll every 30 seconds for new notifications
            setInterval(updateGlobalNotificationCount, 30000);
        @endauth

        // Global Session Messages
        @if(session('success'))
            window.showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            window.showToast("{{ session('error') }}", 'error');
        @endif
    </script>
    <script>
        const searchInput = document.getElementById('search-user');
        const searchResults = document.getElementById('search-results');
        let timeout = null;

        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(async () => {
                const keyword = this.value.trim();

                if (!keyword) {
                    searchResults.innerHTML = '';
                    searchResults.classList.add('hidden');
                    return;
                }

                try {
                    const response = await fetch(
                        `/search/users?q=${encodeURIComponent(keyword)}`
                    );
                    const users = await response.json();

                    if (users.length === 0) {
                        searchResults.innerHTML = `
                            <div class="p-4 text-sm text-slate-400">
                                Không tìm thấy người dùng
                            </div>
                        `;
                        searchResults.classList.remove('hidden');
                        return;
                    }

                    searchResults.innerHTML = users.map(user => `
                        <a
                            href="/profile/${user.ten_dang_nhap}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition cursor-pointer"
                        >
                            <img
                                src="${user.avatar_url}"
                                class="w-10 h-10 rounded-full object-cover"
                            >
                            <div>
                                <p class="text-sm font-semibold text-white">
                                    ${user.ten_dang_nhap}
                                </p>
                                <p class="text-xs text-slate-400">
                                    ${user.tieu_su ?? ''}
                                </p>
                            </div>
                        </a>
                    `).join('');
                    searchResults.classList.remove('hidden');
                } catch (error) {
                    console.error(error);
                }
            }, 300);
        });

        // click ngoài -> đóng dropdown
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) &&
                !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    </script>
    <!-- Complete Mentions Suggestions Logic -->
    <script>
        // LÔ-GÍCH GỢI Ý VÀ HIGHLIGHT NHẮC TÊN (@MENTION) TRONG Ô NHẬP LIỆU
        document.addEventListener('DOMContentLoaded', function() {
            let activeTextarea = null;
            let suggestionBox = null;
            let selectedIndex = -1;
            let queryStart = -1;

            // 1. CÀI ĐẶT BỘ TÔ SÁNG MENTION TRONG TEXTAREA:
            // Tạo một khung backdrop trong suốt nằm ngay phía dưới ô textarea để hiển thị màu sắc làm nổi bật chữ có @
            function initHighlighter(textarea) {
                if (textarea.dataset.highlighterInit) return;
                textarea.dataset.highlighterInit = "true";

                // Tạo thẻ div bọc ngoài (wrapper) ô textarea
                const wrapper = document.createElement('div');
                wrapper.className = 'textarea-highlighter-wrapper';
                
                // Chèn wrapper vào DOM
                textarea.parentNode.insertBefore(wrapper, textarea);
                wrapper.appendChild(textarea);
                
                // Tạo thẻ backdrop chứa text được định dạng màu sắc
                const backdrop = document.createElement('div');
                backdrop.className = 'textarea-highlighter-backdrop';
                
                wrapper.insertBefore(backdrop, textarea);

                // Sao chép các thuộc tính CSS hiển thị của textarea sang wrapper để khớp hoàn hảo
                const computed = window.getComputedStyle(textarea);
                wrapper.style.display = computed.display === 'inline' ? 'inline-block' : computed.display;
                wrapper.style.margin = computed.margin;
                wrapper.style.verticalAlign = computed.verticalAlign;
                wrapper.style.flexGrow = computed.flexGrow;

                // Hàm an toàn tránh tấn công XSS khi render văn bản thô vào innerHTML của backdrop
                function escapeHtml(str) {
                    return str
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                // Hàm đồng bộ nội dung văn bản và áp dụng thẻ HTML highlight
                function sync() {
                    const comp = window.getComputedStyle(textarea);
                    backdrop.style.fontFamily = comp.fontFamily;
                    backdrop.style.fontSize = comp.fontSize;
                    backdrop.style.fontWeight = comp.fontWeight;
                    backdrop.style.lineHeight = comp.lineHeight;
                    backdrop.style.letterSpacing = comp.letterSpacing;
                    backdrop.style.padding = comp.padding;
                    backdrop.style.borderWidth = comp.borderWidth;
                    backdrop.style.borderColor = 'transparent';
                    backdrop.style.borderStyle = comp.borderStyle;
                    backdrop.style.boxSizing = comp.boxSizing;
                    backdrop.style.whiteSpace = 'pre-wrap';
                    backdrop.style.wordBreak = 'break-word';
                    backdrop.style.textAlign = comp.textAlign;
                    backdrop.style.textTransform = comp.textTransform;
                    
                    let text = textarea.value;
                    let html = escapeHtml(text);
                    
                    // HIGHLIGHT @all: Tô màu xanh dương
                    html = html.replace(/(?<=^|(?<=[^a-zA-Z0-9_\.]))@all/iu, '<span class="text-sky-400">@all</span>');
                    
                    // HIGHLIGHT @username: Tìm các ký tự bắt đầu bằng @ và bôi xanh đậm
                    html = html.replace(/(?<=^|(?<=[^a-zA-Z0-9_\.]))@([a-zA-Z0-9_]+)/gu, function(match, username) {
                        if (username.toLowerCase() === 'all') return match;
                        return `<span class="text-sky-400 font-bold">@${username}</span>`;
                    });

                    // Ngăn chặn sự sụp đổ khoảng trắng trong HTML backdrop (giúp khớp vị trí với textarea thực)
                    html = html.replace(/ {2}/g, ' &nbsp;');
                    html = html.replace(/ \n/g, '&nbsp;\n');
                    html = html.replace(/ $/g, '&nbsp;');
                    html = html.replace(/<\/span> /g, '<\/span>&nbsp;');
                    
                    backdrop.innerHTML = html + (text.endsWith('\n') ? '\n' : '');
                    backdrop.scrollTop = textarea.scrollTop;
                    backdrop.scrollLeft = textarea.scrollLeft;
                }

                // Lắng nghe thao tác nhập liệu và cuộn trang của người dùng để cập nhật backdrop tương ứng
                textarea.addEventListener('input', sync);
                textarea.addEventListener('scroll', () => {
                    backdrop.scrollTop = textarea.scrollTop;
                    backdrop.scrollLeft = textarea.scrollLeft;
                });

                // Sử dụng ResizeObserver để đồng bộ lại kích thước khi textarea bị kéo giãn tự do
                if (window.ResizeObserver) {
                    const ro = new ResizeObserver(() => {
                        sync();
                    });
                    ro.observe(textarea);
                }

                sync();
            }

            // Khởi tạo bộ highlight cho các textarea tĩnh đã load sẵn
            document.querySelectorAll('textarea').forEach(initHighlighter);

            // MUTATION OBSERVER: Lắng nghe và tự động đăng ký highlighter cho các textarea được thêm mới động (như khi nhấn reply bình luận)
            const observer = new MutationObserver((mutations) => {
                for (const mutation of mutations) {
                    for (const node of mutation.addedNodes) {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            if (node.tagName === 'TEXTAREA') {
                                initHighlighter(node);
                            } else {
                                node.querySelectorAll('textarea').forEach(initHighlighter);
                            }
                        }
                    }
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });

            // 2. LÔ-GÍCH TỰ ĐỘNG ĐỀ XUẤT NHẮC TÊN (AUTOCOMPLETE SUGGESTIONS)
            // Tạo thẻ gợi ý danh sách người dùng bay lơ lửng trên màn hình
            function createSuggestionBox() {
                if (document.getElementById('mention-suggestions')) {
                    suggestionBox = document.getElementById('mention-suggestions');
                    return;
                }
                suggestionBox = document.createElement('div');
                suggestionBox.id = 'mention-suggestions';
                suggestionBox.className = 'hidden absolute bg-slate-950/95 border border-white/10 rounded-2xl shadow-2xl p-2 z-[99999] max-h-60 overflow-y-auto w-64 backdrop-blur-md custom-scrollbar';
                document.body.appendChild(suggestionBox);
            }

            // Lắng nghe ký tự nhập để kích hoạt gợi ý
            document.addEventListener('input', async function(e) {
                const target = e.target;
                if (target.tagName !== 'TEXTAREA' && !(target.tagName === 'INPUT' && target.type === 'text')) {
                    return;
                }
                
                activeTextarea = target;
                createSuggestionBox();
                
                const value = activeTextarea.value;
                const selectionStart = activeTextarea.selectionStart;
                const textBeforeCaret = value.substring(0, selectionStart);
                
                // Tìm kiếm ký tự '@' cuối cùng đứng ở đầu dòng hoặc sau một khoảng trắng
                const atIndex = textBeforeCaret.lastIndexOf('@');
                if (atIndex !== -1 && (atIndex === 0 || /\s/.test(textBeforeCaret.charAt(atIndex - 1)))) {
                    const searchQ = textBeforeCaret.substring(atIndex + 1);
                    // Chỉ kích hoạt gợi ý khi người dùng chưa gõ khoảng trắng sau dấu @
                    if (!/\s/.test(searchQ)) { 
                        queryStart = atIndex;
                        await fetchSuggestions(searchQ);
                        return;
                    }
                }
                hideSuggestions();
            });

            // Gửi yêu cầu lấy danh sách gợi ý từ API
            async function fetchSuggestions(q) {
                try {
                    let postId = '';
                    if (activeTextarea) {
                        const form = activeTextarea.closest('form');
                        if (form) {
                            const action = form.getAttribute('action');
                            if (action) {
                                // Trích xuất bài viết ID từ URL để lọc người liên quan (chủ bài, người tham gia bình luận)
                                const match = action.match(/\/posts\/(\d+)\/comments?/);
                                if (match) {
                                    postId = match[1];
                                }
                            }
                        }
                    }
                    
                    const response = await fetch(`/api/users/mention-suggestions?q=${encodeURIComponent(q)}&post_id=${postId}`);
                    const users = await response.json();
                    
                    if (users.length === 0) {
                        hideSuggestions();
                        return;
                    }
                    
                    renderSuggestions(users);
                } catch (err) {
                    console.error(err);
                }
            }

            // Vẽ danh sách gợi ý người dùng ra giao diện HTML
            function renderSuggestions(users) {
                suggestionBox.innerHTML = users.map((user, idx) => {
                    const avatarHtml = user.is_all 
                        ? `<div class="w-8 h-8 rounded-full flex items-center justify-center bg-sky-500/20 text-sky-400 border border-sky-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                           </div>`
                        : `<img src="${user.avatar}" class="w-8 h-8 rounded-full object-cover border border-white/10">`;

                    const relationHtml = user.relation 
                        ? `<span class="px-1.5 py-0.5 rounded text-[10px] bg-sky-500/10 text-sky-400 font-medium">${user.relation}</span>`
                        : '';

                    return `
                        <div class="suggestion-item flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer hover:bg-white/5 transition duration-200 ${idx === 0 ? 'bg-white/5' : ''}" data-username="${user.username}" data-index="${idx}">
                            ${avatarHtml}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-white truncate">${user.name}</p>
                                    ${relationHtml}
                                </div>
                                <p class="text-xs text-slate-400 truncate">@${user.username}</p>
                            </div>
                        </div>
                    `;
                }).join('');
                
                selectedIndex = 0;
                positionSuggestionBox();
                suggestionBox.classList.remove('hidden');
            }

            // Định vị tọa độ bay của ô gợi ý chính xác nằm phía dưới con trỏ soạn thảo của textarea
            function positionSuggestionBox() {
                const rect = activeTextarea.getBoundingClientRect();
                suggestionBox.style.left = `${rect.left + window.scrollX}px`;
                suggestionBox.style.top = `${rect.bottom + window.scrollY + 6}px`;
            }

            // Ẩn hộp thoại đề xuất
            function hideSuggestions() {
                if (suggestionBox) {
                    suggestionBox.classList.add('hidden');
                }
                selectedIndex = -1;
                queryStart = -1;
            }

            // Đóng hộp thoại nếu click ra vùng ngoài
            document.addEventListener('click', function(e) {
                if (suggestionBox && !suggestionBox.contains(e.target) && e.target !== activeTextarea) {
                    hideSuggestions();
                }
                
                const item = e.target.closest('.suggestion-item');
                if (item) {
                    insertMention(item.dataset.username);
                }
            });

            // THAY THẾ CHỮ ĐANG GÕ BẰNG MENTION THỰC SỰ:
            // Nối chuỗi văn bản chèn thêm username đã chọn và tự động đính kèm thêm dấu cách phía sau
            function insertMention(username) {
                if (!activeTextarea) return;
                const value = activeTextarea.value;
                const selectionStart = activeTextarea.selectionStart;
                
                const before = value.substring(0, queryStart);
                const after = value.substring(selectionStart);
                
                activeTextarea.value = before + '@' + username + ' ' + after;
                activeTextarea.focus();
                
                const newCursorPos = queryStart + username.length + 2; // account for @ and space
                activeTextarea.setSelectionRange(newCursorPos, newCursorPos);
                
                // Kích hoạt lại sự kiện 'input' để đồng bộ hóa việc highlight chữ trong khung backdrop tức thời
                activeTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                
                hideSuggestions();
            }

            // HỖ TRỢ ĐIỀU HƯỚNG BẰNG PHÍM CƠ BẢN: Lên, Xuống, Enter chọn kết quả và Phím ESC để hủy bỏ
            document.addEventListener('keydown', function(e) {
                if (!suggestionBox || suggestionBox.classList.contains('hidden')) return;
                
                const items = suggestionBox.querySelectorAll('.suggestion-item');
                if (items.length === 0) return;
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (items[selectedIndex]) items[selectedIndex].classList.remove('bg-white/5');
                    selectedIndex = (selectedIndex + 1) % items.length;
                    if (items[selectedIndex]) {
                        items[selectedIndex].classList.add('bg-white/5');
                        items[selectedIndex].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (items[selectedIndex]) items[selectedIndex].classList.remove('bg-white/5');
                    selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                    if (items[selectedIndex]) {
                        items[selectedIndex].classList.add('bg-white/5');
                        items[selectedIndex].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (selectedIndex >= 0 && selectedIndex < items.length) {
                        insertMention(items[selectedIndex].dataset.username);
                    }
                } else if (e.key === 'Escape') {
                    hideSuggestions();
                }
            });

            // Modal danh sách cảm xúc
            const reactionsModal = document.getElementById('reactions-list-modal');
            const reactionsModalLoading = document.getElementById('reactions-modal-loading');
            const reactionsModalList = document.getElementById('reactions-modal-list');

            window.openReactionsModal = function(postId) {
                if (!reactionsModal) return;
                reactionsModal.classList.remove('hidden');
                reactionsModal.classList.add('flex');
                
                reactionsModalLoading.classList.remove('hidden');
                reactionsModalList.classList.add('hidden');
                reactionsModalList.innerHTML = '';
                
                fetch(`/posts/${postId}/reactors`)
                    .then(res => res.json())
                    .then(data => {
                        reactionsModalLoading.classList.add('hidden');
                        reactionsModalList.classList.remove('hidden');
                        
                        if (data.success && data.reactors && data.reactors.length > 0) {
                            reactionsModalList.innerHTML = data.reactors.map(user => {
                                const verifyBadge = user.is_verified ? `
                                    <span class="material-symbols-outlined text-base text-sky-400 shrink-0" data-icon="verified" style="font-variation-settings: 'FILL' 1;">
                                        verified
                                    </span>
                                ` : '';
                                
                                return `
                                    <div class="flex items-center justify-between p-2 rounded-xl hover:bg-white/5 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <a href="/profile/${user.username}">
                                                <img class="w-10 h-10 rounded-full object-cover border border-white/10" src="${user.avatar}" alt="${user.name}">
                                            </a>
                                            <div>
                                                <div class="flex items-center gap-1">
                                                    <a href="/profile/${user.username}" class="font-bold text-on-surface hover:text-sky-300 transition-colors text-sm">${user.name}</a>
                                                    ${verifyBadge}
                                                </div>
                                                <div class="text-xs text-slate-500">@${user.username}</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Loại cảm xúc -->
                                        <div class="flex items-center justify-center w-8 h-8 rounded-full ${user.reaction_bg}">
                                            <span class="material-symbols-outlined text-[18px] ${user.reaction_color}" style="font-variation-settings: 'FILL' 1;">${user.reaction_icon}</span>
                                        </div>
                                    </div>
                                `;
                            }).join('');
                        } else {
                            reactionsModalList.innerHTML = `
                                <div class="text-center py-8 text-slate-500">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-600">mood_bad</span>
                                    <p class="text-sm">Chưa có ai thả cảm xúc cho bài viết này.</p>
                                </div>
                            `;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        reactionsModalLoading.classList.add('hidden');
                        reactionsModalList.classList.remove('hidden');
                        reactionsModalList.innerHTML = `
                            <div class="text-center py-8 text-red-400">
                                <span class="material-symbols-outlined text-4xl mb-2">error</span>
                                <p class="text-sm">Có lỗi xảy ra khi tải danh sách. Vui lòng thử lại.</p>
                            </div>
                        `;
                    });
            }

            window.closeReactionsModal = function() {
                if (!reactionsModal) return;
                reactionsModal.classList.add('hidden');
                reactionsModal.classList.remove('flex');
            }

            // Click outside to close
            if (reactionsModal) {
                reactionsModal.addEventListener('click', function(e) {
                    if (e.target === reactionsModal) {
                        closeReactionsModal();
                    }
                });
            }

    </script>

    <!-- MODAL HIỂN THỊ CÁC USER ĐÃ THẢ CẢM XÚC -->
    <div id="reactions-list-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
        <div class="glass-panel-elevated w-full max-w-md rounded-2xl overflow-hidden shadow-2xl animate-modal-in flex flex-col max-h-[500px]">
            <!-- Header Modal -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                <h3 class="text-lg font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-sky-400">favorite</span>
                    Người đã bày tỏ cảm xúc
                </h3>
                <button type="button" onclick="closeReactionsModal()" class="text-slate-400 hover:text-white p-1 rounded-full hover:bg-white/5 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <!-- List -->
            <div id="reactions-list-container" class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-4">
                <!-- Loading State -->
                <div class="text-center py-8 text-slate-500" id="reactions-modal-loading">
                    <div class="inline-block animate-spin w-6 h-6 border-2 border-sky-400 border-t-transparent rounded-full mb-2"></div>
                    <p class="text-sm">Đang tải danh sách...</p>
                </div>
                
                <!-- Content Area -->
                <div id="reactions-modal-list" class="space-y-3.5 hidden">
                    <!-- User elements will be injected here -->
                </div>
            </div>
        </div>
    </div>
</body>

</html>