@extends('layouts.app')

@section('title', __('messages.settings_title'))

@section('content')

<div class="max-w-4xl mx-auto space-y-8 px-4 md:px-8 pt-6">

    {{-- Header --}}
    <div class="space-y-2">
        <h1 class="text-3xl font-bold tracking-tight text-on-surface">
            {{ __('messages.settings_title') }}
        </h1>

        <p class="text-on-surface-variant">
            {{ __('messages.settings_subtitle') }}
        </p>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Display & Language --}}
        <div class="md:col-span-2 glass-panel p-6 rounded-xl space-y-6">

            <div class="flex items-center gap-3 text-primary">
                <span class="material-symbols-outlined">palette</span>

                <h3 class="font-semibold">
                    {{ __('messages.display_and_language') }}
                </h3>
            </div>

            <div class="space-y-4">

                {{-- Dark Mode --}}
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/5">

                    <div class="flex items-center gap-3">

                        <span class="material-symbols-outlined text-on-surface-variant">
                            dark_mode
                        </span>

                        <div>
                            <p class="font-medium text-sm">
                                {{ __('messages.dark_mode') }}
                            </p>

                            <p class="text-xs text-on-surface-variant">
                                {{ __('messages.dark_mode_desc') }}
                            </p>
                        </div>

                    </div>

                    {{-- Toggle: fix bằng JS thay vì peer-checked --}}
                    <button
                        type="button"
                        id="theme-toggle-btn"
                        role="switch"
                        aria-checked="false"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-transparent bg-slate-700"
                    >
                        <span
                            id="theme-toggle-thumb"
                            class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 translate-x-0.5"
                        ></span>
                    </button>

                    {{-- Hidden checkbox để giữ tương thích nếu cần --}}
                    <input type="checkbox" id="theme-toggle" class="sr-only" aria-hidden="true">

                </div>

                {{-- Language --}}
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/5">

                    <div class="flex items-center gap-3">

                        <span class="material-symbols-outlined text-on-surface-variant">
                            language
                        </span>

                        <div>
                            <p class="font-medium text-sm">
                                {{ __('messages.language') }}
                            </p>

                            <p class="text-xs text-on-surface-variant">
                                {{ __('messages.language_desc') }}
                            </p>
                        </div>

                    </div>

                    <select
                        id="language-select"
                        class="bg-surface-container-high border border-outline-variant text-on-surface text-sm rounded-lg focus:ring-primary focus:border-primary block p-2 px-4 outline-none appearance-none cursor-pointer"
                    >

                        <option value="vi" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>
                            {{ __('messages.lang_vi') }}
                        </option>

                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>
                            {{ __('messages.lang_en') }}
                        </option>

                        <option value="ja" {{ app()->getLocale() == 'ja' ? 'selected' : '' }}>
                            日本語
                        </option>

                    </select>
                </div>

            </div>
        </div>

        {{-- Storage --}}
        <div class="glass-panel p-6 rounded-xl space-y-6 flex flex-col justify-between">

            <div class="space-y-4">

                <div class="flex items-center gap-3 text-tertiary">
                    <span class="material-symbols-outlined">database</span>

                    <h3 class="font-semibold">
                        {{ __('messages.storage') }}
                    </h3>
                </div>

                <div class="text-center py-4">

                    <div class="text-3xl font-bold text-on-surface">
                        128.5 MB
                    </div>

                    <p class="text-xs text-on-surface-variant mt-1">
                        {{ __('messages.storage_usage') }}
                    </p>

                </div>

            </div>

            <button class="w-full py-3 bg-white/10 hover:bg-white/20 border border-white/10 text-on-surface font-medium rounded-lg transition-all active:scale-95 text-sm">
                {{ __('messages.clear_cache') }}
            </button>
        </div>

        {{-- Devices --}}
        <div class="md:col-span-3 glass-panel p-6 rounded-xl space-y-6">

            <div class="flex items-center justify-between">

                <div class="flex items-center gap-3 text-secondary">

                    <span class="material-symbols-outlined">
                        devices
                    </span>

                    <h3 class="font-semibold">
                        {{ __('messages.device_management') }}
                    </h3>

                </div>

                <span class="text-xs text-on-surface-variant">
                    {{ $sessions->count() }} {{ __('messages.devices_active') }}
                </span>

            </div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    @foreach ($sessions as $session)

    <div class="p-4 bg-white/5 rounded-lg border border-white/5 flex items-start gap-4">

        <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary">

            <span class="material-symbols-outlined">

                @if (
                    str_contains($session->he_dieu_hanh, 'Android') ||
                    str_contains($session->he_dieu_hanh, 'iPhone')
                )

                    smartphone

                @elseif (
                    str_contains($session->he_dieu_hanh, 'MacOS')
                )

                    laptop_mac

                @elseif (
                    str_contains($session->he_dieu_hanh, 'Windows')
                )

                    laptop_windows

                @elseif (
                    str_contains($session->he_dieu_hanh, 'Linux')
                )

                    computer

                @else

                    devices

                @endif

            </span>

        </div>

        <div class="flex-1">

            <div class="flex justify-between">

                <p class="font-semibold text-sm">
                    {{ $session->ten_thiet_bi }}
                </p>

                @if ($session->la_phien_hien_tai)

                    <span class="text-[10px] bg-primary/20 text-primary px-1.5 py-0.5 rounded uppercase font-bold">
                        {{ __('messages.current') }}
                    </span>

                @endif

            </div>

            <p class="text-xs text-on-surface-variant">
                {{ $session->dia_chi_ip }}
            </p>

            <p class="text-[10px] text-on-surface-variant mt-2">
                {{ $session->trinh_duyet }}
                •
                {{ $session->lan_hoat_dong_cuoi->diffForHumans() }}
            </p>

        </div>

    </div>

    @endforeach

</div>

        {{-- Help --}}
        <div class="md:col-span-3 glass-panel p-6 rounded-xl">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">

                <div class="flex items-center gap-3 text-on-surface-variant">

                    <span class="material-symbols-outlined">
                        help_outline
                    </span>

                    <h3 class="font-semibold">
                        {{ __('messages.help_policies') }}
                    </h3>

                </div>

             <div class="flex flex-wrap gap-4 md:gap-8">

    {{-- Đổi thẻ <a> thành <button> để mở Modal tại chỗ --}}
    <button onclick="openModal('termsModal')" class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1 outline-none">
        <span class="material-symbols-outlined text-sm">description</span>
        {{ __('messages.terms') }}
    </button>

    <button onclick="openModal('privacyModal')" class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1 outline-none">
        <span class="material-symbols-outlined text-sm">shield</span>
        {{ __('messages.privacy') }}
    </button>

    <button onclick="openModal('supportModal')" class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1 outline-none">
        <span class="material-symbols-outlined text-sm">support_agent</span>
        {{ __('messages.support') }}
    </button>

    <button onclick="openModal('aboutModal')" class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1 outline-none">
        <span class="material-symbols-outlined text-sm">info</span>
        {{ __('messages.about') }}
    </button>

</div>

            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="md:col-span-3 p-6 border border-error/20 bg-error/5 rounded-xl flex items-center justify-between">

            <div>

                <h4 class="font-semibold text-error">
                    {{ __('messages.danger_zone_title') }}
                </h4>

                <p class="text-xs text-on-surface-variant">
                    {{ __('messages.danger_zone_desc') }}
                </p>

            </div>

            <button class="px-6 py-2 border border-error/50 text-error hover:bg-error hover:text-white transition-all rounded-lg text-sm font-medium">
                {{ __('messages.disable_account') }}
            </button>

        </div>

    </div>
</div>
{{-- MODAL HỖ TRỢ (SUPPORT) --}}
<div id="supportModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
    <div class="glass-panel w-full max-w-sm p-6 rounded-xl relative m-4 border border-white/10 shadow-2xl">
        {{-- Nút X đóng --}}
        <button onclick="closeModal('supportModal')" class="absolute top-4 right-4 text-on-surface-variant hover:text-error transition-colors outline-none">
            <span class="material-symbols-outlined">close</span>
        </button>
        
        <h3 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">support_agent</span>
            Thông tin Hỗ trợ
        </h3>
        
        <div class="space-y-3 text-sm text-on-surface-variant">
            <p>Nếu bạn gặp sự cố, vui lòng liên hệ bộ phận IT:</p>
            <div class="p-3 bg-white/5 rounded-lg border border-white/5 space-y-2">
                <p><strong>Email:</strong> {{ $supportInfo['email'] ?? 'it@company.com' }}</p>
                <p><strong>Hotline:</strong> {{ $supportInfo['hotline'] ?? '1900 xxxx' }}</p>
            </div>
            <a href="{{ $supportInfo['zalo_group'] ?? '#' }}" target="_blank" class="inline-block mt-2 px-4 py-2 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-colors font-medium">
                Tham gia nhóm Zalo
            </a>
        </div>
    </div>
</div>

{{-- MODAL GIỚI THIỆU (ABOUT) --}}
<div id="aboutModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
    <div class="glass-panel w-full max-w-sm p-6 rounded-xl relative m-4 border border-white/10 shadow-2xl">
        {{-- Nút X đóng --}}
        <button onclick="closeModal('aboutModal')" class="absolute top-4 right-4 text-on-surface-variant hover:text-error transition-colors outline-none">
            <span class="material-symbols-outlined">close</span>
        </button>
        
        <h3 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">info</span>
            Giới thiệu Hệ thống
        </h3>
        
        <div class="space-y-3 text-sm text-on-surface-variant">
            <p class="text-base text-on-surface font-medium">{{ $aboutInfo['app_name'] ?? 'Phần mềm Quản trị' }}</p>
            <p>Phiên bản: <span class="text-primary font-mono bg-primary/10 px-1 rounded">{{ $aboutInfo['version'] ?? 'v1.0' }}</span></p>
            <p>Cập nhật lần cuối: {{ $aboutInfo['release_date'] ?? 'N/A' }}</p>
            <div class="pt-3 mt-3 border-t border-white/10">
                <p>Bản quyền © {{ date('Y') }} thuộc về <strong>{{ $aboutInfo['company'] ?? 'Công ty' }}</strong>.</p>
            </div>
        </div>
    </div>
</div>
{{-- MODAL ĐIỀU KHOẢN (TERMS) --}}
<div id="termsModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
    <div class="glass-panel w-full max-w-lg p-6 rounded-xl relative m-4 border border-white/10 shadow-2xl">
        <button onclick="closeModal('termsModal')" class="absolute top-4 right-4 text-on-surface-variant hover:text-error transition-colors outline-none">
            <span class="material-symbols-outlined">close</span>
        </button>
        
        <h3 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">description</span>
            Điều khoản sử dụng
        </h3>
        
        <div class="space-y-3 text-sm text-on-surface-variant max-h-[60vh] overflow-y-auto pr-2 leading-relaxed">
            <p class="font-semibold text-on-surface">1. Quy định chung</p>
            <p>Khi truy cập và sử dụng hệ thống quản trị, bạn đồng ý tuân thủ tuyệt đối các quy chế an toàn thông tin của tổ chức đưa ra.</p>
            <p class="font-semibold text-on-surface">2. Bảo mật tài khoản</p>
            <p>Người dùng có trách nhiệm tự bảo mật tài khoản, không cung cấp cookie hoặc token phiên đăng nhập cho bất kỳ bên thứ ba nào.</p>
            <p class="font-semibold text-on-surface">3. Giới hạn sử dụng</p>
            <p>Nghiêm cấm mọi hành vi sử dụng công cụ tự động để can thiệp, phá hoại hoặc khai thác dữ liệu trái phép từ hệ thống.</p>
        </div>
    </div>
</div>

{{-- MODAL CHÍNH SÁCH BẢO MẬT (PRIVACY) --}}
<div id="privacyModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
    <div class="glass-panel w-full max-w-lg p-6 rounded-xl relative m-4 border border-white/10 shadow-2xl">
        <button onclick="closeModal('privacyModal')" class="absolute top-4 right-4 text-on-surface-variant hover:text-error transition-colors outline-none">
            <span class="material-symbols-outlined">close</span>
        </button>
        
        <h3 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">shield</span>
            Chính sách bảo mật
        </h3>
        
        <div class="space-y-3 text-sm text-on-surface-variant max-h-[60vh] overflow-y-auto pr-2 leading-relaxed">
            <p class="font-semibold text-on-surface">1. Thông tin thu thập</p>
            <p>Hệ thống thu thập dữ liệu lịch sử đăng nhập bao gồm: Địa chỉ IP, thông tin thiết bị (Hệ điều hành, Trình duyệt) nhằm mục đích bảo vệ tài khoản của chính bạn.</p>
            <p class="font-semibold text-on-surface">2. Mục đích sử dụng</p>
            <p>Các dữ liệu thu thập chỉ phục vụ cho tính năng "Quản lý thiết bị" giúp bạn phát hiện các phiên đăng nhập bất thường.</p>
            <p class="font-semibold text-on-surface">3. Cam kết</p>
            <p>Chúng tôi cam kết không chia sẻ thông tin nhật ký hoạt động cá nhân của bạn cho bất kỳ đối tác bên ngoài nào.</p>
        </div>
    </div>
</div>
{{-- Background Effects --}}
<div class="fixed top-[-10%] right-[-5%] w-[400px] h-[400px] bg-primary/10 rounded-full blur-[120px] -z-10 pointer-events-none"></div>

<div class="fixed bottom-[-5%] left-[-5%] w-[300px] h-[300px] bg-tertiary/10 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

{{-- Language Script --}}
<script src="/js/language-toggle.js"></script>

{{-- Theme Script --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    const html = document.documentElement;
    const btn = document.getElementById('theme-toggle-btn');
    const thumb = document.getElementById('theme-toggle-thumb');
    const hiddenCheckbox = document.getElementById('theme-toggle');

    function setTheme(isDark) {
        if (isDark) {
            html.classList.add('dark');
            html.classList.remove('light');
            btn.style.backgroundColor = '#2563eb'; // blue-600
            thumb.style.transform = 'translateX(1.25rem)'; // translate-x-5
            btn.setAttribute('aria-checked', 'true');
            if (hiddenCheckbox) hiddenCheckbox.checked = true;
            localStorage.setItem('theme', 'dark');
        } else {
            html.classList.remove('dark');
            html.classList.add('light');
            btn.style.backgroundColor = '#334155'; // slate-700
            thumb.style.transform = 'translateX(0.125rem)'; // translate-x-0.5
            btn.setAttribute('aria-checked', 'false');
            if (hiddenCheckbox) hiddenCheckbox.checked = false;
            localStorage.setItem('theme', 'light');
        }
    }

    // Load saved theme
    const savedTheme = localStorage.getItem('theme');
    setTheme(savedTheme === 'dark');

    // Toggle on click
    btn.addEventListener('click', function () {
        const isDark = this.getAttribute('aria-checked') === 'true';
        setTheme(!isDark);
    });
});
// --- XỬ LÝ MODAL POPUP ---
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

// Cho phép nhấn ra vùng tối bên ngoài để đóng modal
window.addEventListener('click', function(event) {
    // Kiểm tra nếu click trúng lớp nền đen (chứa class bg-black/50)
    if (event.target.classList.contains('bg-black/50')) {
        event.target.classList.remove('flex');
        event.target.classList.add('hidden');
    }
});
</script>

@endsection