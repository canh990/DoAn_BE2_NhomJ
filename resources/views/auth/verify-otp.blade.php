@extends('layouts.auth')

@section('title', 'Xác thực OTP - NHOMJ')

@section('content')

<div class="w-full max-w-md relative z-10">
    <div class="glass-panel w-full p-8 rounded-2xl shadow-[0_0_50px_rgba(125,211,252,0.05)]">
        {{-- Header --}}
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 border border-primary/20 mb-4">
                <span class="material-symbols-outlined text-primary text-3xl">verified</span>
            </div>
            <h2 class="text-2xl font-bold text-on-surface mb-2">Xác thực email</h2>
            <p class="text-on-surface-variant text-sm">
                Chúng tôi đã gửi mã OTP 6 số đến
            </p>
            <p class="text-primary font-semibold text-sm mt-1">{{ $email }}</p>
        </div>

        {{-- Thông báo lỗi --}}
        @if (session('error'))
            <div class="bg-error/10 border border-error/30 text-error px-4 py-3 rounded-xl text-sm mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">error</span>
                {{ session('error') }}
            </div>
        @endif

        {{-- Thông báo thành công (gửi lại OTP) --}}
        @if (session('success'))
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl text-sm mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        {{-- Form nhập OTP --}}
        <form action="{{ route('otp.verify') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user_id }}">

            <div class="space-y-2">
                <label class="text-xs font-semibold text-on-secondary-container tracking-wider uppercase ml-1">
                    Nhập mã OTP
                </label>
                <div class="flex justify-center gap-2" id="otp-container">
                    @for ($i = 0; $i < 6; $i++)
                        <input
                            type="text"
                            maxlength="1"
                            inputmode="numeric"
                            pattern="[0-9]"
                            class="otp-input w-12 h-14 text-center text-xl font-bold glass-input rounded-xl text-on-surface focus:border-primary focus:ring-1 focus:ring-primary/30"
                            data-index="{{ $i }}"
                            autocomplete="off"
                            required
                        >
                    @endfor
                </div>
                {{-- Hidden field chứa mã OTP đầy đủ --}}
                <input type="hidden" name="otp_code" id="otp-full-code">
                @error('otp_code') <span class="text-error text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit" id="btn-verify" class="w-full bg-primary/20 border border-primary/30 text-primary font-bold py-3.5 rounded-xl hover:bg-primary/30 transition-all active:scale-[0.98] shadow-[0_0_20px_rgba(125,211,252,0.1)]">
                <span class="flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-xl">verified</span>
                    Xác thực
                </span>
            </button>
        </form>

        {{-- Nút gửi lại OTP --}}
        <div class="mt-4">
            <form action="{{ route('otp.resend') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user_id }}">
                <button type="submit" class="w-full text-sm text-on-surface-variant hover:text-primary transition-colors py-2">
                    Không nhận được mã? <span class="text-primary font-semibold">Gửi lại</span>
                </button>
            </form>
        </div>

        {{-- Divider --}}
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-outline-variant"></div></div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-[#0f1524] px-4 text-on-surface-variant">hoặc</span>
            </div>
        </div>

        {{-- Nút bỏ qua --}}
        <form action="{{ route('otp.skip') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <button type="submit" class="w-full border border-outline-variant/50 text-on-surface-variant font-medium py-3 rounded-xl hover:bg-surface-variant/30 hover:text-on-surface transition-all active:scale-[0.98]">
                <span class="flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-xl">skip_next</span>
                    Bỏ qua, xác thực sau
                </span>
            </button>
        </form>

        {{-- Countdown --}}
        <div class="mt-6 text-center">
            <p class="text-xs text-on-surface-variant">
                Mã hết hạn sau <span id="countdown" class="text-primary font-semibold">10:00</span>
            </p>
        </div>
    </div>
</div>

{{-- Script xử lý nhập OTP từng ô --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.otp-input');
    const fullCode = document.getElementById('otp-full-code');

    function updateFullCode() {
        let code = '';
        inputs.forEach(input => code += input.value);
        fullCode.value = code;
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', function (e) {
            // Chỉ cho phép số
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            updateFullCode();
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && this.value === '' && index > 0) {
                inputs[index - 1].focus();
                inputs[index - 1].value = '';
                updateFullCode();
            }
        });

        // Hỗ trợ paste mã OTP
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            for (let i = 0; i < Math.min(pasteData.length, 6); i++) {
                inputs[i].value = pasteData[i];
            }
            if (pasteData.length > 0) {
                inputs[Math.min(pasteData.length, 6) - 1].focus();
            }
            updateFullCode();
        });
    });

    // Focus ô đầu tiên
    inputs[0].focus();

    // Countdown timer (10 phút)
    const countdownEl = document.getElementById('countdown');
    let totalSeconds = 10 * 60;

    const timer = setInterval(function () {
        totalSeconds--;
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        countdownEl.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

        if (totalSeconds <= 60) {
            countdownEl.classList.remove('text-primary');
            countdownEl.classList.add('text-error');
        }

        if (totalSeconds <= 0) {
            clearInterval(timer);
            countdownEl.textContent = 'Đã hết hạn';
        }
    }, 1000);
});
</script>

@endsection
