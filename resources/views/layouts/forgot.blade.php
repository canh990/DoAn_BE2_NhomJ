<!doctype html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'NHOMJ - Khôi phục mật khẩu')</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "tertiary-fixed-dim": "#c8a0f0",
                        "on-surface": "#e0e8f0",
                        error: "#ff6b6b",
                        "on-tertiary": "#1a002e",
                        "on-primary": "#001f2e",
                        secondary: "#88b4cc",
                        "primary-container": "#0e4d6e",
                        "on-primary-fixed": "#001f2e",
                        "on-tertiary-container": "#e8d0ff",
                        "on-background": "#e0e8f0",
                        "on-tertiary-fixed-variant": "#4d2a73",
                        "on-secondary-fixed-variant": "#2a4a5e",
                        "primary-fixed-dim": "#7dd3fc",
                        "surface-tint": "#7dd3fc",
                        surface: "#0f1524",
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
                        primary: "#7dd3fc",
                        "primary-fixed": "#c8eaff",
                        "error-container": "#3d1414",
                        tertiary: "#c8a0f0",
                        "on-tertiary-fixed": "#1a002e",
                        "on-secondary-fixed": "#0d1f2b",
                        "surface-variant": "#1a2438",
                        "surface-container-low": "#111828",
                        "surface-container-high": "#1a2438",
                        "on-primary-fixed-variant": "#004d73",
                        outline: "#4a6070",
                        "on-secondary-container": "#c0d8e8",
                        "secondary-fixed": "#c0d8e8",
                        "on-secondary": "#001f2e",
                        background: "#0a0e1a",
                        "on-surface-variant": "#a0b4c4",
                        "surface-bright": "#1a2438",
                        "on-primary-container": "#c8eaff",
                        "secondary-fixed-dim": "#88b4cc",
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        lg: "1rem",
                        xl: "1.5rem",
                        full: "9999px",
                    },
                    fontFamily: {
                        headline: ["Inter"],
                        body: ["Inter"],
                        label: ["Inter"],
                    },
                },
            },
        };
    </script>
    
    <style>
        .glass-card {
            background: rgba(15, 21, 36, 0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(125, 211, 252, 0.1);
        }
        .material-symbols-outlined {
            font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
        }
        .glow-subtle {
            box-shadow: 0 0 30px rgba(125, 211, 252, 0.05);
        }
        /* Hiệu ứng hiển thị thông báo lỗi/thành công */
        @keyframes fade-in-down {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in-down 0.3s ease-out forwards; }
    </style>
</head>

<body class="bg-background text-on-surface font-body min-h-screen selection:bg-primary/30 relative">
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-primary/10 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-tertiary/10 rounded-full blur-[120px]"></div>
    </div>

    <header class="fixed top-0 w-full z-50 bg-[#0a0e1a]/60 backdrop-blur-xl border-b border-sky-400/10 h-16 flex justify-between items-center px-6">
        <div class="text-2xl font-bold bg-gradient-to-r from-sky-400 to-purple-400 bg-clip-text text-transparent font-headline tracking-tight">
            NHOMJ
        </div>
        <a class="text-slate-400 hover:text-sky-300 transition-colors flex items-center gap-2" href="{{ route('login') }}">
            <span class="material-symbols-outlined">arrow_back</span>
            <span class="text-sm font-medium">Quay lại đăng nhập</span>
        </a>
    </header>

    <main class="relative z-10 pt-32 pb-20 px-6 flex flex-col items-center min-h-screen justify-center">
        
        @if(session('error'))
            <div class="w-full max-w-md mb-6 rounded-xl border border-red-400/20 bg-red-400/10 px-4 py-3 text-sm text-red-300 animate-fade-in text-center">
                {{ session('error') }}
            </div>
        @endif
        
        @if(session('success'))
            <div class="w-full max-w-md mb-6 rounded-xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-300 animate-fade-in text-center">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')

        <footer class="mt-12 text-center">
            <p class="text-xs text-on-surface-variant/50 font-medium tracking-tight">
                Bảo mật bởi hệ thống NHOMJ Core &copy; {{ date('Y') }}
            </p>
        </footer>
    </main>

    <div class="fixed top-0 right-[15%] w-px h-screen bg-gradient-to-b from-transparent via-primary/20 to-transparent opacity-30 pointer-events-none"></div>
    <div class="fixed top-0 left-[15%] w-px h-screen bg-gradient-to-b from-transparent via-tertiary/20 to-transparent opacity-30 pointer-events-none"></div>

    @stack('scripts')
</body>
</html>