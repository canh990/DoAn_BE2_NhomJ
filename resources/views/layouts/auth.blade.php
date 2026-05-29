<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NHOMJ')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
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