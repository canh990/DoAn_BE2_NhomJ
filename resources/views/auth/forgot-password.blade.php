@extends('layouts.forgot') {{-- Sử dụng layout chung cho các trang xác thực --}}

@section('content')
<section class="w-full max-w-md">
    <div class="glass-card p-8 rounded-xl glow-subtle">
        <div class="mb-8 text-center">
            <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-primary/20">
                <span class="material-symbols-outlined text-primary text-3xl">lock_reset</span>
            </div>
            <h1 class="text-2xl font-bold font-headline text-on-surface mb-2 tracking-tight">Quên mật khẩu?</h1>
            <p class="text-on-surface-variant text-sm">Nhập email liên kết với tài khoản của bạn để nhận mã OTP.</p>
        </div>

        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="text-xs font-semibold text-on-surface-variant ml-1 uppercase tracking-wider">Địa chỉ Email</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-xl">mail</span>
                    <input name="email" class="w-full bg-surface-container border border-outline-variant rounded-lg py-3 pl-12 pr-4 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('email') border-error @enderror" placeholder="email@vi-du.com" type="email" value="{{ old('email') }}" required />
                </div>
                @error('email') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full bg-primary/20 hover:bg-primary/30 border border-primary/30 text-primary font-semibold py-3.5 rounded-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                Gửi mã OTP
                <span class="material-symbols-outlined text-xl">arrow_forward</span>
            </button>
        </form>
    </div>
</section>
@endsection