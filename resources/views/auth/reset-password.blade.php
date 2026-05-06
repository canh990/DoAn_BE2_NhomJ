@extends('layouts.forgot') {{-- Sử dụng layout chung cho các trang xác thực --}}

@section('content')
<section class="w-full max-w-md">
    <div class="glass-card p-8 rounded-xl glow-subtle">
        <div class="mb-8 text-center">
            <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-primary/20">
                <span class="material-symbols-outlined text-primary text-3xl">password</span>
            </div>
            <h2 class="text-2xl font-bold font-headline text-on-surface mb-2 tracking-tight">Đặt lại mật khẩu</h2>
            <p class="text-on-surface-variant text-sm">Vui lòng tạo mật khẩu mới mạnh mẽ.</p>
        </div>

        <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="email" value="{{ session('email_reset') }}">
            
            <div class="space-y-2">
                <label class="text-xs font-semibold text-on-surface-variant ml-1 uppercase tracking-wider">Mật khẩu mới</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-xl">key</span>
                    <input id="password" name="password" class="w-full bg-surface-container border border-outline-variant rounded-lg py-3 pl-12 pr-4 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all" type="password" required />
                </div>
               
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-on-surface-variant ml-1 uppercase tracking-wider">Xác nhận mật khẩu</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-xl">verified</span>
                    <input name="password_confirmation" class="w-full bg-surface-container border border-outline-variant rounded-lg py-3 pl-12 pr-4 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all" type="password" required />
                </div>
            </div>
 {{-- Password Requirements Checklist --}}
                <div class="mt-3 space-y-1.5 px-1">
                    <div id="req-length" class="flex items-center gap-2 text-xs text-on-surface-variant transition-colors duration-200">
                        <span id="icon-length" class="material-symbols-outlined text-sm">radio_button_unchecked</span>
                        Ít nhất 8 ký tự
                    </div>
                    <div id="req-alphanum" class="flex items-center gap-2 text-xs text-on-surface-variant transition-colors duration-200">
                        <span id="icon-alphanum" class="material-symbols-outlined text-sm">radio_button_unchecked</span>
                        Bao gồm chữ cái và số
                    </div>
                    <div id="req-special" class="flex items-center gap-2 text-xs text-on-surface-variant transition-colors duration-200">
                        <span id="icon-special" class="material-symbols-outlined text-sm">radio_button_unchecked</span>
                        Một ký tự đặc biệt (!@#$)
                    </div>
                </div>
            <button type="submit" class="w-full bg-gradient-to-r from-primary to-tertiary text-on-primary font-bold py-3.5 rounded-lg shadow-xl shadow-primary/10 hover:brightness-110 transition-all active:scale-95">
                Cập nhật mật khẩu
            </button>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        
        const requirements = [
            { id: 'length', validate: (val) => val.length >= 8 },
            { id: 'alphanum', validate: (val) => /[a-zA-Z]/.test(val) && /[0-9]/.test(val) },
            { id: 'special', validate: (val) => /[!@#$]/.test(val) }
        ];

        passwordInput.addEventListener('input', function() {
            const value = this.value;

            requirements.forEach(req => {
                const element = document.getElementById(`req-${req.id}`);
                const icon = document.getElementById(`icon-${req.id}`);
                const isMet = req.validate(value);

                if (isMet) {
                    element.classList.remove('text-on-surface-variant');
                    element.classList.add('text-primary');
                    icon.textContent = 'check_circle';
                } else {
                    element.classList.add('text-on-surface-variant');
                    element.classList.remove('text-primary');
                    icon.textContent = 'radio_button_unchecked';
                }
            });
        });
    });
</script>
@endpush