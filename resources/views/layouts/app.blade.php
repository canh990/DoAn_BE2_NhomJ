<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NHOMJ')</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
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
            <img 
                class="w-10 h-10 rounded-full border border-sky-400/30 object-cover" 
                alt="{{ $user->name }}" 
<img src="{{ $user->anh_dai_dien ? asset('storage/' . $user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}" 
     alt="Avatar">            
            <div>
                <p class="text-sm font-bold text-sky-300 font-inter">
                    {{ $user->name }}
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
            <a class="flex items-center gap-3 {{ request()->routeIs('messages') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="{{ route('messages') }}">
                <span class="material-symbols-outlined" data-icon="chat">chat</span>
                Tin nhắn
            </a>
            <a class="flex items-center gap-3 {{ request()->routeIs('profile') ? 'bg-sky-400/20 text-sky-300 border border-sky-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }} px-4 py-3 rounded-xl transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="{{ route('profile') }}">
                <span class="material-symbols-outlined" data-icon="person">person</span>
                Hồ sơ
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
            const reactionAreas = document.querySelectorAll('[data-reaction-area]');

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
    </script>
</body>
</html>