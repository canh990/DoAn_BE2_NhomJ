@extends('layouts.forgot') {{-- Sử dụng layout chung cho các trang xác thực --}}

@section('content')
<section class="w-full max-w-md">
    <div class="glass-card p-8 rounded-xl glow-subtle">
        <div class="mb-8 text-center">
            <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-primary/20">
                <span class="material-symbols-outlined text-primary text-3xl">vpn_key</span>
            </div>
            <h2 class="text-2xl font-bold font-headline text-on-surface mb-2 tracking-tight">Xác thực OTP</h2>
            <p class="text-on-surface-variant text-sm">Mã OTP đã được gửi đến email <span class="font-semibold text-primary">{{ session('email_reset') }}</span>. Vui lòng nhập mã để tiếp tục.</p>
        </div>

        @if (session('error'))
            <div class="mb-6 p-4 rounded-lg bg-error-container border border-error/30 text-sm text-on-error-container">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('password.otp.verify') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="text-xs font-semibold text-on-surface-variant ml-1 uppercase tracking-wider">Mã OTP</label>
                <div class="flex justify-center gap-2" id="otp-inputs">
                    @for ($i = 0; $i < 6; $i++)
                        <input
                            type="text"
                            name="otp[]"
                            maxlength="1"
                            class="w-10 h-12 text-center text-2xl font-bold bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('otp.'.$i) border-error @enderror"
                            required
                            inputmode="numeric"
                            pattern="[0-9]"
                            id="otp-{{ $i }}"
                        />
                    @endfor
                </div>
                @error('otp') <p class="text-error text-xs mt-1 text-center">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-primary to-tertiary text-on-primary font-bold py-3.5 rounded-lg shadow-xl shadow-primary/10 hover:brightness-110 transition-all active:scale-95">
                Xác nhận OTP
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-on-surface-variant text-sm">
                Không nhận được mã?
                <a href="{{ route('password.request') }}" class="text-primary font-semibold hover:underline">Gửi lại mã</a>
            </p>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpInputs = document.querySelectorAll('#otp-inputs input');
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });
    });
</script>
@endsection