@extends('layouts.auth')

@section('title', 'Quên mật khẩu - NHOMJ')

@section('content')

<div class="w-full max-w-md relative z-10">
    <div class="glass-panel w-full p-8 rounded-2xl shadow-[0_0_50px_rgba(125,211,252,0.05)]">
        {{-- Header --}}
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 border border-primary/20 mb-4">
                <span class="material-symbols-outlined text-primary text-3xl">lock_reset</span>
            </div>
            <h2 class="text-2xl font-bold text-on-surface mb-2">Quên mật khẩu?</h2>
            <p class="text-on-surface-variant text-sm">
                Nhập email đã đăng ký, chúng tôi sẽ gửi hướng dẫn đặt lại mật khẩu.
            </p>
        </div>

        {{-- Thông báo --}}
        @if (session('status'))
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl text-sm mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">check_circle</span>
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-error/10 border border-error/30 text-error px-4 py-3 rounded-xl text-sm mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">error</span>
                {{ session('error') }}
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-on-secondary-container tracking-wider uppercase ml-1">Email</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-xl">mail</span>
                    <input name="email" type="email" value="{{ old('email') }}" class="glass-input w-full pl-11 pr-4 py-3 rounded-xl text-on-surface placeholder:text-outline" placeholder="Nhập email của bạn" required autofocus>
                </div>
                @error('email') <span class="text-error text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-primary/20 border border-primary/30 text-primary font-bold py-3.5 rounded-xl hover:bg-primary/30 transition-all active:scale-[0.98] shadow-[0_0_20px_rgba(125,211,252,0.1)]">
                <span class="flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-xl">send</span>
                    Gửi liên kết đặt lại
                </span>
            </button>
        </form>

        {{-- Back to login --}}
        <div class="mt-8 text-center">
            <p class="text-on-surface-variant text-sm">
                Nhớ ra mật khẩu?
                <a class="text-primary font-semibold hover:underline decoration-primary/30 underline-offset-4 transition-all" href="{{ url('/login') }}">Đăng nhập</a>
            </p>
        </div>
    </div>
</div>

@endsection
