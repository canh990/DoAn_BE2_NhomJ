@extends('layouts.auth')

@section('title', 'Đăng ký - NHOMJ')

@section('content')

<div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-12 items-center relative z-10">
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
        {{ session('success') }}
    </div>
@endif
    <div class="hidden lg:flex flex-col space-y-8">
        <div>
            <h1 class="text-5xl font-extrabold tracking-tight bg-gradient-to-r from-primary to-tertiary bg-clip-text text-transparent mb-4">
                Chào mừng đến với NHOMJ
            </h1>
            <p class="text-xl text-on-surface-variant leading-relaxed max-w-md">
                Kết nối, chia sẻ và kiến tạo tương lai cùng cộng đồng kỹ thuật số hàng đầu.
            </p>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="glass-panel p-6 rounded-xl space-y-3">
                <span class="material-symbols-outlined text-primary text-3xl">hub</span>
                <h3 class="font-semibold text-on-surface">Kết nối sâu rộng</h3>
                <p class="text-sm text-on-surface-variant">Mạng lưới thành viên đa quốc gia.</p>
            </div>
            <div class="glass-panel p-6 rounded-xl space-y-3 translate-y-6">
                <span class="material-symbols-outlined text-tertiary text-3xl">security</span>
                <h3 class="font-semibold text-on-surface">Bảo mật tuyệt đối</h3>
                <p class="text-sm text-on-surface-variant">Mã hóa đầu cuối cho mọi dữ liệu.</p>
            </div>
        </div>

        <div class="mt-8 flex items-center gap-4">
            <div class="flex -space-x-3">
                <img class="w-10 h-10 rounded-full border-2 border-background" src="https://i.pravatar.cc/150?u=1" alt="User 1">
                <img class="w-10 h-10 rounded-full border-2 border-background" src="https://i.pravatar.cc/150?u=2" alt="User 2">
                <img class="w-10 h-10 rounded-full border-2 border-background" src="https://i.pravatar.cc/150?u=3" alt="User 3">
            </div>
            <p class="text-sm text-on-surface-variant">+10,000 người đã tham gia tuần này</p>
        </div>
    </div>

    <div class="w-full flex justify-center">
        <div class="glass-panel w-full max-w-md p-8 rounded-2xl shadow-[0_0_50px_rgba(125,211,252,0.05)]">
            <div class="mb-8 text-center lg:text-left">
                <h2 class="text-3xl font-bold text-on-surface mb-2">Tạo tài khoản</h2>
                <p class="text-on-surface-variant">Bắt đầu hành trình của bạn ngay hôm nay</p>
            </div>
<form action="{{ route('register.post') }}" method="POST" class="space-y-5" autocomplete="off">
    @csrf

    {{-- TÊN ĐĂNG NHẬP --}}
    <div class="space-y-2">
        <label class="text-xs font-semibold uppercase tracking-wider text-on-surface-variant ml-1">
            Tên đăng nhập
        </label>

        <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">
                person
            </span>

            <input
                name="ten_dang_nhap"
                type="text"
                value="{{ old('ten_dang_nhap') }}"
                autocomplete="off"
                required
                class="w-full h-12 bg-surface-container-low border 
                @error('ten_dang_nhap') border-error @else border-outline-variant @enderror
                focus:border-primary focus:ring-1 focus:ring-primary/20 
                rounded-lg pl-12 pr-4 text-on-surface 
                placeholder:text-outline transition-all"
                placeholder="vd: nguyen.van.a (chữ thường, số, dấu chấm)"
            />
        </div>

        @error('ten_dang_nhap')
            <p class="text-xs text-error ml-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- TÊN HIỂN THỊ --}}
    <div class="space-y-2">
        <label class="text-xs font-semibold uppercase tracking-wider text-on-surface-variant ml-1">
            Tên hiển thị <span class="normal-case font-normal text-outline">(tùy chọn)</span>
        </label>

        <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">
                badge
            </span>

            <input
                name="ten_hien_thi"
                type="text"
                value="{{ old('ten_hien_thi') }}"
                autocomplete="off"
                class="w-full h-12 bg-surface-container-low border 
                @error('ten_hien_thi') border-error @else border-outline-variant @enderror
                focus:border-primary focus:ring-1 focus:ring-primary/20 
                rounded-lg pl-12 pr-4 text-on-surface 
                placeholder:text-outline transition-all"
                placeholder="Tên thật của bạn (vd: Nguyễn Văn A)"
            />
        </div>

        @error('ten_hien_thi')
            <p class="text-xs text-error ml-1">{{ $message }}</p>
        @enderror
        <p class="text-xs text-outline ml-1">Tên này sẽ hiển thị với bạn bè. Nếu bỏ trống, tên đăng nhập sẽ được dùng.</p>
    </div>

    {{-- EMAIL --}}
    <div class="space-y-2">
        <label class="text-xs font-semibold uppercase tracking-wider text-on-surface-variant ml-1">
            Email
        </label>

        <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">
                mail
            </span>

            <input
                name="email"
                type="email"
                value="{{ old('email') }}"
                autocomplete="off"
                required
                class="w-full h-12 bg-surface-container-low border 
                @error('email') border-error @else border-outline-variant @enderror
                focus:border-primary focus:ring-1 focus:ring-primary/20 
                rounded-lg pl-12 pr-4 text-on-surface 
                placeholder:text-outline transition-all"
                placeholder="example@email.com"
            />
        </div>

        @error('email')
            <p class="text-xs text-error ml-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- SỐ ĐIỆN THOẠI --}}
    <div class="space-y-2">
        <label class="text-xs font-semibold uppercase tracking-wider text-on-surface-variant ml-1">
            Số điện thoại
        </label>

        <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">
                call
            </span>

            <input
                name="so_dien_thoai"
                type="tel"
                value="{{ old('so_dien_thoai') }}"
                autocomplete="off"
                required
                class="w-full h-12 bg-surface-container-low border 
                @error('so_dien_thoai') border-error @else border-outline-variant @enderror
                focus:border-primary focus:ring-1 focus:ring-primary/20 
                rounded-lg pl-12 pr-4 text-on-surface 
                placeholder:text-outline transition-all"
                placeholder="nhập số điện thoại"
            />
        </div>

        @error('so_dien_thoai')
            <p class="text-xs text-error ml-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- MẬT KHẨU --}}
    <div class="space-y-2">
        <label class="text-xs font-semibold uppercase tracking-wider text-on-surface-variant ml-1">
            Mật khẩu
        </label>

        <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">
                lock
            </span>

            <input
                name="mat_khau"
                type="password"
                autocomplete="new-password"
                required
                class="w-full h-12 bg-surface-container-low border 
                @error('mat_khau') border-error @else border-outline-variant @enderror
                focus:border-primary focus:ring-1 focus:ring-primary/20 
                rounded-lg pl-12 pr-4 text-on-surface 
                placeholder:text-outline transition-all"
                placeholder="••••••••"
            />
        </div>

        @error('mat_khau')
            <p class="text-xs text-error ml-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- BUTTON --}}
    <button type="submit"
        class="w-full h-12 bg-primary text-white font-semibold rounded-lg 
        hover:opacity-90 transition-all active:scale-[0.98]">
        Đăng ký
    </button>
</form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-outline-variant"></div></div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-[#0f1524] px-4 text-on-surface-variant">Hoặc tiếp tục với</span>
                </div>
            </div>

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

            <div class="mt-8 text-center">
                <p class="text-on-surface-variant text-sm">
                    Đã có tài khoản? 
                    <a class="text-primary font-semibold hover:underline decoration-primary/30 underline-offset-4 transition-all" href="{{ url('/login') }}">Đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection