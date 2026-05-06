<!DOCTYPE html>
@php $theme = session('personal_theme', null); @endphp
<html class="{{ $theme === 'light' ? 'light' : 'dark' }}" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cài đặt - NHOMJ')</title>
    
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

        /* --- ĐỒNG BỘ: CSS BẮT BUỘC ĐỂ MÀU SÁNG HOẠT ĐỘNG TRONG APP --- */
        html:not(.dark) body { background-color: #f8fafc !important; color: #0f1524 !important;}
        html:not(.dark) .bg-background { background-color: #f8fafc !important; }
        html:not(.dark) .text-on-background, html:not(.dark) .text-on-surface { color: #0f1524 !important; }
        html:not(.dark) .text-on-surface-variant, html:not(.dark) .text-slate-400 { color: #475569 !important; }
        html:not(.dark) .glass-panel, html:not(.dark) .glass-panel-elevated { background: rgba(255, 255, 255, 0.8) !important; border: 1px solid rgba(0, 0, 0, 0.1) !important; }
        html:not(.dark) .bg-\[\#0a0e1a\]\/60 { background-color: rgba(255, 255, 255, 0.8) !important; }
        html:not(.dark) .bg-white\/5 { background-color: rgba(0, 0, 0, 0.03) !important; border-color: rgba(0, 0, 0, 0.1) !important; }
        html:not(.dark) .border-white\/10, html:not(.dark) .border-sky-400\/10 { border-color: rgba(0, 0, 0, 0.1) !important; }
        html:not(.dark) select { background-color: #fff !important; color: #0f1524 !important; border-color: rgba(0,0,0,0.2) !important; }
        html:not(.dark) .text-sky-300 { color: #0284c7 !important; }
    </style>
</head>
<body class="antialiased selection:bg-primary/30 selection:text-primary transition-colors duration-300">

    <header class="fixed top-0 w-full z-50 bg-[#0a0e1a]/60 backdrop-blur-xl border-b border-sky-400/10 shadow-[0_0_30px_rgba(125,211,252,0.05)] font-inter tracking-tight flex justify-between items-center px-6 h-16 transition-colors duration-300">
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
        </div>
    </header>

    <aside class="fixed left-0 top-16 h-[calc(100vh-64px)] w-64 p-4 border-r border-sky-400/10 flex flex-col gap-2 z-40 hidden md:flex transition-colors duration-300">
     @auth
        @php $user = Auth::user(); @endphp
        <div class="mb-4 px-4 py-2">
            <div class="flex items-center gap-3 mb-1">
                <img class="w-10 h-10 rounded-full border border-sky-400/30 object-cover" alt="{{ $user->name }}" src="{{ $user->anh_dai_dien ? asset('storage/' . $user->anh_dai_dien) : 'https://via.placeholder.com/100' }}"/>
                <div>
                    <p class="text-sm font-bold text-sky-300 font-inter">{{ $user->name }}</p>
                </div>
            </div>
        </div>
    @endauth
        <nav class="flex flex-col gap-1 flex-1">
            <a class="flex items-center gap-3 text-slate-400 px-4 py-3 hover:bg-white/5 rounded-xl hover:text-sky-200 transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="#">
                <span class="material-symbols-outlined" data-icon="home">home</span> Bảng tin
            </a>
            <a class="flex items-center gap-3 text-slate-400 px-4 py-3 hover:bg-white/5 rounded-xl hover:text-sky-200 transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="#">
                <span class="material-symbols-outlined" data-icon="explore">explore</span> Khám phá
            </a>
            <a class="flex items-center gap-3 text-slate-400 px-4 py-3 hover:bg-white/5 rounded-xl hover:text-sky-200 transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="#">
                <span class="material-symbols-outlined" data-icon="notifications">notifications</span> Thông báo
            </a>
            <a class="flex items-center gap-3 text-slate-400 px-4 py-3 hover:bg-white/5 rounded-xl hover:text-sky-200 transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="#">
                <span class="material-symbols-outlined" data-icon="chat">chat</span> Tin nhắn
            </a>
            <a class="flex items-center gap-3 text-slate-400 px-4 py-3 hover:bg-white/5 rounded-xl hover:text-sky-200 transition-colors cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="#">
                <span class="material-symbols-outlined" data-icon="person">person</span> Hồ sơ
            </a>
            <a class="flex items-center gap-3 bg-sky-400/20 text-sky-300 rounded-xl px-4 py-3 border border-sky-400/20 cursor-pointer transition-transform active:translate-x-1 font-inter text-sm font-medium" href="#">
                <span class="material-symbols-outlined" data-icon="settings">settings</span> Cài đặt
            </a>
        </nav>
        <button class="mt-4 w-full py-3 bg-sky-400/20 border border-sky-400/30 text-sky-300 font-bold rounded-xl hover:bg-sky-400/30 transition-all active:scale-95">
            Đăng bài mới
        </button>
    </aside>

    <main class="md:ml-64 pt-16 min-h-screen">
        <div class="p-4 md:p-8 pb-12">
            <div class="max-w-4xl mx-auto space-y-8">
                
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold tracking-tight text-on-surface">Cài đặt hệ thống</h1>
                    <p class="text-on-surface-variant">Tùy chỉnh trải nghiệm của bạn trên NHOMJ</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 glass-panel p-6 rounded-xl space-y-6 transition-colors duration-300">
                        <div class="flex items-center gap-3 text-primary">
                            <span class="material-symbols-outlined">palette</span>
                            <h3 class="font-semibold">Hiển thị &amp; Ngôn ngữ</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/5 transition-colors duration-300">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-on-surface-variant">dark_mode</span>
                                    <div>
                                        <p class="font-medium text-sm">Chế độ tối</p>
                                        <p class="text-xs text-on-surface-variant">Giảm mỏi mắt trong môi trường thiếu sáng</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input id="theme-toggle" class="sr-only peer" type="checkbox" {{ $theme === 'light' ? '' : 'checked' }}/>
                                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/5 transition-colors duration-300">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-on-surface-variant">language</span>
                                    <div>
                                        <p class="font-medium text-sm">Ngôn ngữ</p>
                                        <p class="text-xs text-on-surface-variant">Thay đổi ngôn ngữ hiển thị giao diện</p>
                                    </div>
                                </div>
                                <select class="bg-surface-container-high border border-outline-variant text-on-surface text-sm rounded-lg focus:ring-primary focus:border-primary block p-2 px-4 outline-none appearance-none cursor-pointer transition-colors duration-300">
                                    <option selected="" value="vi">Tiếng Việt</option>
                                    <option value="en">English</option>
                                    <option value="jp">日本語</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="glass-panel p-6 rounded-xl space-y-6 flex flex-col justify-between transition-colors duration-300">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 text-tertiary">
                                <span class="material-symbols-outlined">database</span>
                                <h3 class="font-semibold">Bộ nhớ</h3>
                            </div>
                            <div class="text-center py-4">
                                <div class="text-3xl font-bold text-on-surface">128.5 MB</div>
                                <p class="text-xs text-on-surface-variant mt-1">Dung lượng bộ nhớ đệm hiện tại</p>
                            </div>
                        </div>
                        <form action="{{ route('cache.clear') }}" method="POST" class="w-full m-0">
                            @csrf
                            <button type="submit" class="w-full py-3 bg-white/10 hover:bg-white/20 border border-white/10 text-on-surface font-medium rounded-lg transition-all active:scale-95 text-sm">
                                Xóa bộ nhớ đệm
                            </button>
                        </form>
                    </div>

                    <div class="md:col-span-3 glass-panel p-6 rounded-xl space-y-6 transition-colors duration-300">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 text-secondary">
                                <span class="material-symbols-outlined">devices</span>
                                <h3 class="font-semibold">Quản lý thiết bị</h3>
                            </div>
                            <span class="text-xs text-on-surface-variant">3 thiết bị đang hoạt động</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-white/5 rounded-lg border border-white/5 flex items-start gap-4 transition-colors duration-300">
                                <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined">laptop_mac</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <p class="font-semibold text-sm">MacBook Pro M2</p>
                                        <span class="text-[10px] bg-primary/20 text-primary px-1.5 py-0.5 rounded uppercase font-bold">Hiện tại</span>
                                    </div>
                                    <p class="text-xs text-on-surface-variant">Hồ Chí Minh, Việt Nam</p>
                                    <p class="text-[10px] text-on-surface-variant mt-2">Trình duyệt Chrome • 2 phút trước</p>
                                </div>
                            </div>

                            <div class="p-4 bg-white/5 rounded-lg border border-white/5 flex items-start gap-4 hover:border-white/20 transition-all cursor-pointer">
                                <div class="w-10 h-10 rounded-full bg-on-surface-variant/20 flex items-center justify-center text-on-surface-variant">
                                    <span class="material-symbols-outlined">smartphone</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-sm">iPhone 15 Pro</p>
                                    <p class="text-xs text-on-surface-variant">Hà Nội, Việt Nam</p>
                                    <p class="text-[10px] text-on-surface-variant mt-2">Ứng dụng NHOMJ • 3 giờ trước</p>
                                </div>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="text-error opacity-60 hover:opacity-100 transition-opacity">
                                        <span class="material-symbols-outlined text-sm">logout</span>
                                    </button>
                                </form>
                            </div>

                            <div class="p-4 bg-white/5 rounded-lg border border-white/5 flex items-start gap-4 hover:border-white/20 transition-all cursor-pointer">
                                <div class="w-10 h-10 rounded-full bg-on-surface-variant/20 flex items-center justify-center text-on-surface-variant">
                                    <span class="material-symbols-outlined">tablet_android</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-sm">iPad Air 5</p>
                                    <p class="text-xs text-on-surface-variant">Đà Nẵng, Việt Nam</p>
                                    <p class="text-[10px] text-on-surface-variant mt-2">Trình duyệt Safari • 1 ngày trước</p>
                                </div>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="text-error opacity-60 hover:opacity-100 transition-opacity">
                                        <span class="material-symbols-outlined text-sm">logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-3 glass-panel p-6 rounded-xl transition-colors duration-300">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex items-center gap-3 text-on-surface-variant">
                                <span class="material-symbols-outlined">help_outline</span>
                                <h3 class="font-semibold">Trợ giúp &amp; Chính sách</h3>
                            </div>
                            <div class="flex flex-wrap gap-4 md:gap-8">
                                <a class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-sm">description</span>Điều khoản sử dụng</a>
                                <a class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-sm">shield</span>Chính sách bảo mật</a>
                                <a class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-sm">support_agent</span>Liên hệ hỗ trợ</a>
                                <a class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-sm">info</span>Về NHOMJ</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 border border-error/20 bg-error/5 rounded-xl flex items-center justify-between transition-colors duration-300">
                    <div>
                        <h4 class="font-semibold text-error">Vùng nguy hiểm</h4>
                        <p class="text-xs text-on-surface-variant">Hành động này không thể hoàn tác</p>
                    </div>
                    <form action="{{ route('account.disable') }}" method="POST" class="m-0" onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn vô hiệu hoá tài khoản này không? Mọi dữ liệu sẽ bị ẩn.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-2 border border-error/50 text-error hover:bg-error hover:text-white transition-all rounded-lg text-sm font-medium">
                            Vô hiệu hóa tài khoản
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <nav class="md:hidden fixed bottom-0 w-full glass-panel-elevated flex justify-around items-center h-16 z-50 border-t border-sky-400/10">
        <button class="p-2 text-slate-400"><span class="material-symbols-outlined" data-icon="home">home</span></button>
        <button class="p-2 text-slate-400"><span class="material-symbols-outlined" data-icon="explore">explore</span></button>
        <button class="p-2 text-sky-300 bg-sky-400/20 rounded-xl"><span class="material-symbols-outlined" data-icon="settings">settings</span></button>
        <button class="p-2 text-slate-400"><span class="material-symbols-outlined" data-icon="notifications">notifications</span></button>
        <button class="p-2 text-slate-400"><span class="material-symbols-outlined" data-icon="mail">mail</span></button>
    </nav>

    <div class="fixed top-[-10%] right-[-5%] w-[400px] h-[400px] bg-primary/10 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
    <div class="fixed bottom-[-5%] left-[-5%] w-[300px] h-[300px] bg-tertiary/10 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

    <script src="/js/language-toggle.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const html = document.documentElement;
            const themeToggle = document.getElementById('theme-toggle');

            if(themeToggle) {
                themeToggle.addEventListener('change', function() {
                    if (this.checked) {
                        html.classList.add('dark');
                        html.classList.remove('light');
                        localStorage.setItem('theme', 'dark');
                        
                        // Nếu cần lưu vào Backend Laravel không load lại trang
                        // fetch('/set-theme?theme=dark');
                    } else {
                        html.classList.remove('dark');
                        html.classList.add('light');
                        localStorage.setItem('theme', 'light');
                        
                        // fetch('/set-theme?theme=light');
                    }
                });
            }
        });
    </script>
</body>
</html>