<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NHOMJ')</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <script>
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
        .glass-panel { background: rgba(15, 21, 36, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(125, 211, 252, 0.1); }
        .glass-input { background: rgba(26, 36, 56, 0.4); border: 1px solid rgba(74, 96, 112, 0.3); transition: all 0.3s ease; }
        .glass-input:focus { background: rgba(26, 36, 56, 0.6); border-color: #7dd3fc; box-shadow: 0 0 15px rgba(125, 211, 252, 0.1); outline: none; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="bg-background font-body text-on-surface min-h-screen selection:bg-primary/30 selection:text-primary">
    <main class="relative min-h-screen flex items-center justify-center p-6 overflow-hidden">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-tertiary/10 rounded-full blur-[120px]"></div>
        
        @yield('content')

        <div class="fixed bottom-6 w-full text-center px-6">
            <p class="text-[10px] text-outline uppercase tracking-widest opacity-50">
                Bằng cách đăng ký, bạn đồng ý với Điều khoản & Chính sách bảo mật của NHOMJ
            </p>
        </div>
    </main>
</body>
</html>