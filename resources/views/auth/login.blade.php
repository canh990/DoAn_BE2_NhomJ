<!DOCTYPE html>
<html class="dark" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>NHOMJ - Đăng nhập</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
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
                },
            },
        }
    </script>
    <style>
        .glass-panel {
            background: rgba(15, 21, 36, 0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(125, 211, 252, 0.1);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            background-color: #0a0e1a;
            background-image:
                radial-gradient(circle at 0% 0%, rgba(125, 211, 252, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(200, 160, 240, 0.05) 0%, transparent 50%);
        }
    </style>
</head>

<body class="font-body text-on-surface min-h-screen flex items-center justify-center p-4">
    <div class="max-w-6xl w-full grid grid-cols-1 md:grid-cols-2 gap-8 items-center">

        <!-- Left Side: Visual Anchor -->
        <div class="hidden md:flex flex-col gap-8">
            <div class="space-y-4">
                <h1 class="text-6xl font-extrabold tracking-tighter bg-gradient-to-r from-primary to-tertiary bg-clip-text text-transparent">
                    NHOMJ
                </h1>
                <p class="text-xl text-on-surface-variant max-w-md leading-relaxed">
                    Kết nối cộng đồng, chia sẻ khoảnh khắc và khám phá thế giới trong không gian số băng giá đầy mê hoặc.
                </p>
            </div>
            <div class="glass-panel p-6 rounded-xl flex items-center gap-4 max-w-sm">
                <div class="w-12 h-12 rounded-full overflow-hidden border border-primary/20">
                    <img alt="User avatar" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDr0uMLFfIgKJRexAruVeLdVLhJMyphtSGJoLxmMHjl84CJJsT8RxQt_eQ5B3mZ31TUpB8zep4mUNFGqu_4qlyGxGW_0hj-6vbQXMemsxrGB2tWHVse3oxRTXLTLj8d01g4Rpq6Ga-H27KJx26VpvRbixwL4lTvo8vlvv1U_KLosOFW3Rg-i-7JbdT_IG_U2oTYi5ELfRq6lMvG2VTF_DXsk4MOjTY3sJh8wgrUBO8jBBV7iC8q3KvnZCeNyprugv0l_5ixBUHpcpk" />
                </div>
                <div>
                    <p class="text-sm font-semibold">Tham gia cùng 2M+ người dùng</p>
                    <p class="text-xs text-on-surface-variant">Khám phá xu hướng mới nhất ngay hôm nay.</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="h-24 rounded-lg bg-surface-container overflow-hidden opacity-40 border border-primary/10">
                    <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCGTx9SbGArXph3rUyqF1kfBwGngcjDeEup2HErTElGxkibYaGrOgyn-nCpM41J6ZOvuN03JxKGL5TpV2YfvDH9PyaocALkuSpruiEHcIYT0MZPylvNHPDO5kjtHlTAGgHDhUP3KZzKTjsWKIEqR1Hv7ylOROA7Ewq6mkFzitbgz34SobpelFW2mM93hz5nQmXlOmHl3NZvJ6MpaLCT1lohXKPWQwvMeF--euB7C5nve1IXokGHrVsR1lvQNR0mOSnvAJAmEm_2Jd0" />
                </div>
                <div class="h-24 rounded-lg bg-surface-container overflow-hidden opacity-60 border border-primary/10">
                    <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCXTrm_YMtuT-RhlWBDRMrtIC8DZ-z8g-N5tuKHjznFoOxX8qVNtohlBcAB9xlKla-gR5NJmzMYOjtJhTDh-r-RVmMoNUUBtzwgxJPkxnrOnkTyKBwalafWxKlLnhIEddmZ6mp6Z2c7ytQvYBq1vcZgu1j6RLL5RWmu7ThqZgkLDHObypBWdxLoQcAjLU2oPxu4h6cPFW27Z1JSC0FeBtHmpBR9Rq6_d3FUGxPCSLgBBCb_RFKQDYizXtjIpBpLpyJs20Q8IpXJSkY" />
                </div>
                <div class="h-24 rounded-lg bg-surface-container overflow-hidden opacity-40 border border-primary/10">
                    <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBSIt_SQ9OBZKyt2m8weUEPO1S2_nDZnFV5Ilz5vNL408bS3VaAQR1UWX5_vPduw9vVPVXaCSGbAtHAQK5ZpfYub-PkB-axH_sAFetA_us9lHfdvmsq1mM3J15tkw8tXg1g1lITCODxC2-ciysIo3anHwvTVn42XNj44bedt8asfZxyAKbRuPeStGPKEjbpG8wzEZYmlDZHTcsSzL9KqEvetIyY0fkSn_zYmqJ9GRibPBtH25nCmCvNT0S0LO0XIBW_0tIO8" />
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full flex justify-center md:justify-end">
            <div class="glass-panel w-full max-w-[420px] p-8 md:p-10 rounded-xl shadow-[0_0_30px_rgba(125,211,252,0.05)]">

                <div class="text-center mb-10 md:hidden">
                    <h2 class="text-3xl font-bold text-primary tracking-tight">NHOMJ</h2>
                </div>

                <div class="mb-8">
                    <h3 class="text-2xl font-bold tracking-tight mb-2">Chào mừng trở lại</h3>
                    <p class="text-on-surface-variant text-sm">Vui lòng nhập thông tin để truy cập tài khoản của bạn.</p>
                </div>

                {{-- Hiển thị lỗi validation --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg bg-error-container border border-error/30">
                        <ul class="text-sm text-on-error-container space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Hiển thị thông báo session (vd: đăng xuất thành công) --}}
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-lg bg-primary/10 border border-primary/30 text-sm text-primary">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-lg bg-error-container border border-error/30 text-sm text-on-error-container">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf

                    <!-- Email/Phone Input -->
                    <div class="space-y-2">
                        <label for="login" class="text-xs font-semibold uppercase tracking-wider text-on-surface-variant ml-1">
                            Email hoặc Số điện thoại
                        </label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">
                                alternate_email
                            </span>
                            <input
                                id="login"
                                name="login"
                                type="text"
                                value="{{ old('login') }}"
                                autocomplete="username"
                                required
                                class="w-full h-12 bg-surface-container-low border @error('login') border-error @else border-outline-variant @enderror focus:border-primary focus:ring-1 focus:ring-primary/20 rounded-lg pl-12 pr-4 text-on-surface placeholder:text-outline transition-all"
                                placeholder="name@example.com / 0912345678 / username"
                            />
                        </div>
                        @error('login')
                            <p class="text-xs text-error ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center px-1">
                            <label for="password" class="text-xs font-semibold uppercase tracking-wider text-on-surface-variant">
                                Mật khẩu
                            </label>
                            <a class="text-xs font-medium text-primary hover:text-primary-fixed-dim transition-colors" href="{{ route('password.request') }}">
                                Quên mật khẩu?
                            </a>
                        </div>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">
                                lock
                            </span>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="w-full h-12 bg-surface-container-low border @error('password') border-error @else border-outline-variant @enderror focus:border-primary focus:ring-1 focus:ring-primary/20 rounded-lg pl-12 pr-12 text-on-surface placeholder:text-outline transition-all"
                                placeholder="••••••••"
                            />
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface transition-colors">
                                <span class="material-symbols-outlined" id="eye-icon">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-error ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-2 px-1">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="w-4 h-4 rounded border-outline-variant bg-surface-container-low text-primary focus:ring-primary/20"
                        />
                        <label for="remember" class="text-sm text-on-surface-variant cursor-pointer">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full h-12 bg-primary/10 border border-primary/30 hover:bg-primary/20 text-primary font-bold rounded-lg transition-all active:scale-[0.98] mt-4">
                        Đăng nhập
                    </button>
                </form>

                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-outline-variant"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-surface-container px-4 text-on-surface-variant font-medium">Hoặc đăng nhập với</span>
                    </div>
                </div>

                <!-- Social Login -->
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-3 h-12 glass-panel rounded-lg hover:bg-white/5 transition-all">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        <span class="text-sm font-medium">Google</span>
                    </a>
                    <a href="{{ route('auth.facebook') }}" class="flex items-center justify-center gap-3 h-12 glass-panel rounded-lg hover:bg-white/5 transition-all">
                        <svg class="w-5 h-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span class="text-sm font-medium">Facebook</span>
                    </a>
                </div>

                <div class="mt-10 text-center">
                    <p class="text-on-surface-variant text-sm">
                        Chưa có tài khoản?
                        <a class="text-tertiary font-semibold ml-1 hover:underline" href="{{ route('register') }}">Đăng ký ngay</a>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- Background Decoration -->
    <div class="fixed top-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-primary/5 blur-[120px] z-[-1]"></div>
    <div class="fixed bottom-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-tertiary/5 blur-[120px] z-[-1]"></div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>
    
</body>
</html>