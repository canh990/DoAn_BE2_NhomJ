@extends('layouts.forgot') {{-- Sử dụng layout chung cho các trang xác thực --}}

@section('content')
<section class="w-full max-w-md">
    <div class="glass-card p-8 rounded-xl glow-subtle">
        @php
            $isRegisterFlow = request()->routeIs('otp.show');
            $verifyRoute = $isRegisterFlow ? route('otp.verify') : route('password.otp.verify');
            $resendRoute = $isRegisterFlow ? route('otp.resend') : route('password.email');
        @endphp

        <div class="mb-8 text-center">
            <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-primary/20">
                <span class="material-symbols-outlined text-primary text-3xl">vpn_key</span>
            </div>
            @if($isRegisterFlow)
                <h2 class="text-2xl font-bold font-headline text-on-surface mb-2 tracking-tight">Xác thực Email</h2>
                <p class="text-on-surface-variant text-sm">Mã OTP đã được gửi đến email <span class="font-semibold text-primary">{{ session('email_reset', Auth::user()->email ?? '') }}</span>. Vui lòng nhập mã để hoàn tất đăng ký tài khoản.</p>
            @else
                <h2 class="text-2xl font-bold font-headline text-on-surface mb-2 tracking-tight">Khôi phục mật khẩu</h2>
                <p class="text-on-surface-variant text-sm">Mã OTP đã được gửi đến email <span class="font-semibold text-primary">{{ session('email_reset') }}</span>. Vui lòng nhập mã để đặt lại mật khẩu của bạn.</p>
            @endif
        </div>

        {{-- Thông báo lỗi --}}
        @if (session('error'))
            <div class="mb-6 p-4 rounded-lg bg-error-container border border-error/30 text-sm text-on-error-container">
                {{ session('error') }}
            </div>
        @endif

        {{-- Thông báo thành công (gửi lại OTP) --}}
        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/30 text-sm text-green-400">
                {{ session('success') }}
            </div>
        @endif


        {{-- Form nhập OTP --}}
        <form action="{{ $verifyRoute }}" method="POST" class="space-y-6">
            @csrf
            {{-- Nếu là quên mật khẩu thì cần gửi lại email để resend --}}
            @if(!$isRegisterFlow)
                <input type="hidden" name="email" value="{{ session('email_reset') }}">
            @endif

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

            {{-- Nút xác thực --}}
            <button type="submit" class="w-full bg-gradient-to-r from-primary to-tertiary text-on-primary font-bold py-3.5 rounded-lg shadow-xl shadow-primary/10 hover:brightness-110 transition-all active:scale-95">
                Xác nhận OTP
            </button>
        </form>

        {{-- Gửi lại OTP --}}
        <div class="mt-4 text-center">
            <form action="{{ $resendRoute }}" method="POST" id="resend-otp-form">
                @csrf
                @if(!$isRegisterFlow)
                    <input type="hidden" name="email" value="{{ session('email_reset') }}">
                @endif
                <p class="text-sm text-on-surface-variant py-2">
                    Không nhận được mã? 
                    <button type="submit" id="resend-otp-btn" class="text-primary font-semibold hover:underline disabled:opacity-50 disabled:cursor-not-allowed disabled:no-underline transition-all disabled:text-on-surface-variant">
                        Gửi lại
                    </button>
                </p>
            </form>
        </div>

        @if($isRegisterFlow)
        {{-- Divider --}}
        <div class="relative my-4">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-outline-variant/50"></div></div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-surface-container px-4 text-on-surface-variant">hoặc</span>
            </div>
        </div>

        {{-- Nút bỏ qua xác thực (Chỉ cho luồng đăng ký) --}}
        <form action="{{ route('otp.skip') }}" method="POST">
            @csrf
            <button type="submit" class="w-full border border-outline-variant/50 text-on-surface-variant font-medium py-3 rounded-lg hover:bg-surface-variant/30 hover:text-on-surface transition-all active:scale-95 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-xl">skip_next</span>
                Bỏ qua, xác thực sau
            </button>
        </form>
        @endif

        {{-- Countdown --}}
        <div class="mt-6 text-center">
            <p class="text-xs text-on-surface-variant">
                Mã hết hạn sau <span id="countdown" class="text-primary font-semibold">10:00</span>
            </p>
        </div>
    </div>
</section>

{{-- Script xử lý nhập OTP từng ô --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpInputs = document.querySelectorAll('#otp-inputs input');

        otpInputs.forEach((input, index) => {
            // Xử lý khi nhập (chấp nhận mọi bàn phím, kể cả tiếng Việt)
            input.addEventListener('input', function(e) {
                // Lọc bỏ mọi ký tự không phải số
                let val = this.value.replace(/[^0-9]/g, '');
                
                if (val.length > 0) {
                    // Ưu tiên lấy số vừa được gõ thêm vào (số cuối cùng)
                    this.value = val.slice(-1);
                    
                    // Tự động nhảy sang ô tiếp theo
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                } else {
                    this.value = '';
                }
            });

            // Chỉ dùng keydown để điều hướng (Backspace)
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    if (this.value === '' && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                }
            });

            // Paste mã OTP
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                for (let i = 0; i < Math.min(pasteData.length, otpInputs.length); i++) {
                    otpInputs[i].value = pasteData[i];
                }
                const focusIdx = Math.min(pasteData.length, otpInputs.length - 1);
                if (focusIdx >= 0) {
                    otpInputs[focusIdx].focus();
                }
            });
        });

        // Focus ô đầu tiên
        if (otpInputs.length > 0) otpInputs[0].focus();

        // Countdown timer (10 phút)
        const countdownEl = document.getElementById('countdown');
        let totalSeconds = 10 * 60;

        const timer = setInterval(function() {
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
        // Resend OTP Cooldown timer (60s) dùng localStorage chống refresh
        const resendForm = document.getElementById('resend-otp-form');
        const resendBtn = document.getElementById('resend-otp-btn');
        const COOLDOWN_MS = 60000; // 60 seconds
        const emailKey = '{{ session('email_reset', Auth::user()->email ?? 'default') }}';
        const storageKey = `last_otp_request_${emailKey}`;

        // Nếu mới vào lần đầu (chưa có trong localStorage), set để bắt đầu đếm 60s
        if (!localStorage.getItem(storageKey)) {
            localStorage.setItem(storageKey, Date.now());
        }

        function updateResendButton() {
            const lastSend = localStorage.getItem(storageKey);
            if (lastSend) {
                const elapsed = Date.now() - parseInt(lastSend, 10);
                if (elapsed < COOLDOWN_MS) {
                    const remaining = Math.ceil((COOLDOWN_MS - elapsed) / 1000);
                    resendBtn.disabled = true;
                    resendBtn.innerHTML = `Gửi lại sau <span class="font-bold text-error">${remaining}s</span>`;
                    return true;
                }
            }
            resendBtn.disabled = false;
            resendBtn.innerHTML = 'Gửi lại';
            return false;
        }

        let resendInterval = setInterval(() => {
            if (!updateResendButton()) clearInterval(resendInterval);
        }, 1000);
        updateResendButton();

        if (resendForm) {
            resendForm.addEventListener('submit', function(e) {
                if (updateResendButton()) {
                    e.preventDefault();
                    return;
                }
                localStorage.setItem(storageKey, Date.now());
            });
        }
    });
</script>
@endsection