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

    <div class="p-4 bg-white/5 rounded-lg border border-white/5 flex justify-between items-start gap-4">

        <div class="flex items-start gap-4">

            <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary shrink-0">

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

            <div>

                <div class="flex items-center gap-2 flex-wrap">

                    <p class="font-semibold text-sm">
                        {{ $session->ten_thiet_bi }}
                    </p>

                    @if ($session->la_phien_hien_tai)

                        <span class="text-[10px] bg-primary/20 text-primary px-1.5 py-0.5 rounded uppercase font-bold shrink-0">
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
                    {{ $session->lan_hoat_dong_cuoi ? $session->lan_hoat_dong_cuoi->diffForHumans() : '' }}
                </p>

            </div>

        </div>

        @if (!$session->la_phien_hien_tai)

            <form id="logout-session-{{ $session->id }}" action="{{ route('settings.sessions.logout', $session->id) }}" method="POST" class="shrink-0">

                @csrf

                @method('DELETE')

                <button type="button" onclick="window.openConfirmModal('Đăng xuất thiết bị?', 'Bạn có chắc chắn muốn đăng xuất khỏi thiết bị này không?', () => document.getElementById('logout-session-{{ $session->id }}').submit(), 'Đăng xuất')" class="w-8 h-8 rounded-full bg-error/10 hover:bg-error/20 text-error flex items-center justify-center transition-all active:scale-95" title="Đăng xuất thiết bị">

                    <span class="material-symbols-outlined text-sm">logout</span>

                </button>

            </form>

        @endif

    </div>

    @endforeach

</div>

        {{-- Giao diện Hỗ trợ (Help Center) --}}
        <!-- Tiêu đề lớn -->
        <div class="md:col-span-3 border border-white/10 bg-slate-900/50 backdrop-blur-md rounded-xl p-4 flex justify-between items-center">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-lg">help</span>
                {{ __('messages.support') }}
            </h2>
            <div class="flex gap-4">
                <button onclick="openModal('termsModal')" class="text-xs text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">description</span>
                    {{ __('messages.terms') }}
                </button>
                <button onclick="openModal('privacyModal')" class="text-xs text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">shield</span>
                    {{ __('messages.privacy') }}
                </button>
            </div>
        </div>

        <!-- Bố cục Grid 2 cột -->
        <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Cột bên trái (Chiếm 2/3 chiều ngang): Khung Chatbot AI -->
            <div class="md:col-span-2 flex flex-col h-[480px] border border-white/10 bg-slate-900/80 backdrop-blur-md rounded-xl overflow-hidden shadow-2xl">
                <!-- Header -->
                <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between bg-white/5">
                    <div class="flex items-center gap-3">
                        <!-- Bot Avatar -->
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-on-primary font-bold text-xs shadow-md">
                            NJ
                        </div>
                        <div>
                            <h4 class="font-bold text-xs text-white flex items-center gap-1.5 leading-none">
                                NHOMJ
                                <span class="w-2 h-2 rounded-full bg-slate-500 ring-2 ring-slate-900" title="Offline" id="bot-status-dot"></span>
                            </h4>
                            <p class="text-[10px] text-slate-400 italic mt-0.5">Agt chat live</p> {{-- Chuỗi này thường giữ nguyên hoặc dùng Agt Live --}}
                        </div>
                    </div>
                </div>

                <!-- Khung nội dung chat -->
                <div id="chat-messages-box" class="flex-1 p-4 overflow-y-auto space-y-4 bg-slate-950/20 scroll-smooth">
                    <!-- Bot message với FAQ buttons -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-slate-300 shrink-0 text-xs font-bold">
                            NJ
                        </div>
                        <div class="space-y-3 max-w-[80%]">
                            <div class="p-3 bg-white/5 border border-white/5 text-xs text-slate-200 rounded-2xl rounded-tl-none leading-relaxed">
                                {{ __('messages.chatbot_welcome') }}
                            </div>
                            <!-- Khối câu hỏi tự động dạng danh sách nút bấm xếp dọc -->
                            <div class="flex flex-col gap-2 w-full">
                                <button type="button" class="faq-btn text-left text-xs bg-white/5 hover:bg-primary/10 border border-white/10 hover:border-primary/20 text-slate-300 hover:text-primary transition-all duration-200 p-2.5 rounded-xl active:scale-[0.98] outline-none">
                                    {{ __('messages.chatbot_faq_report') }}
                                </button>
                                <button type="button" class="faq-btn text-left text-xs bg-white/5 hover:bg-primary/10 border border-white/10 hover:border-primary/20 text-slate-300 hover:text-primary transition-all duration-200 p-2.5 rounded-xl active:scale-[0.98] outline-none">
                                    {{ __('messages.chatbot_faq_lost_acc') }}
                                </button>
                                <button type="button" class="faq-btn text-left text-xs bg-white/5 hover:bg-primary/10 border border-white/10 hover:border-primary/20 text-slate-300 hover:text-primary transition-all duration-200 p-2.5 rounded-xl active:scale-[0.98] outline-none">
                                    {{ __('messages.chatbot_faq_forgot_pwd') }}
                                </button>
                                <button type="button" class="faq-btn text-left text-xs bg-white/5 hover:bg-primary/10 border border-white/10 hover:border-primary/20 text-slate-300 hover:text-primary transition-all duration-200 p-2.5 rounded-xl active:scale-[0.98] outline-none">
                                    {{ __('messages.chatbot_faq_2fa') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thanh nhập liệu (Input Chat) -->
                <div class="p-3 border-t border-white/10 bg-slate-900/50">
                    <form id="chatbot-form" onsubmit="event.preventDefault(); sendChatMessage();" class="flex gap-2">
                        <input
                            type="text"
                            id="chatbot-input"
                            placeholder="{{ __('messages.chatbot_placeholder') }}"
                            autocomplete="off"
                            class="flex-1 bg-white/5 border border-white/10 rounded-full px-4 py-2 text-xs text-white placeholder:text-slate-500 focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/50 transition-all"
                        />
                        <button
                            type="submit"
                            class="px-5 py-2 bg-primary text-black font-semibold hover:bg-primary/80 transition-colors cursor-pointer text-xs rounded-none"
                        >
                            {{ __('messages.chatbot_send') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Cột bên phải (Chiếm 1/3 chiều ngang): Khối thông tin liên hệ trực tiếp -->
            <div class="flex flex-col gap-4">
                <!-- SĐT Tổng đài -->
                <div class="p-5 border border-white/10 bg-slate-900/80 backdrop-blur-md rounded-xl flex items-center gap-4 hover:border-white/20 transition-all duration-300">
                    <div class="w-12 h-12 rounded-full bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                        <span class="material-symbols-outlined text-2xl">call</span>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white leading-none">1900 1111</p>
                        <p class="text-xs text-slate-400 italic mt-1">{{ __('messages.support_247') }}</p>
                    </div>
                </div>

                <!-- Email Hỗ trợ -->
                <div class="p-5 border border-white/10 bg-slate-900/80 backdrop-blur-md rounded-xl flex items-center gap-4 hover:border-white/20 transition-all duration-300">
                    <div class="w-12 h-12 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/20">
                        <span class="material-symbols-outlined text-2xl">mail</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">support@nhomj.vn</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ __('messages.support_email_label') }}</p>
                    </div>
                </div>

                <!-- Thời gian làm việc -->
                <div class="p-5 border border-white/10 bg-slate-900/80 backdrop-blur-md rounded-xl flex items-center gap-4 hover:border-white/20 transition-all duration-300">
                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0 border border-primary/20">
                        <span class="material-symbols-outlined text-2xl">check_circle</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">T2-CN (24/7)</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ __('messages.support_working_time') }}</p>
                    </div>
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
            {{ __('messages.support_info_title') }}
        </h3>
        
        <div class="space-y-3 text-sm text-on-surface-variant">
            <p>{{ __('messages.support_contact_desc') }}</p>
            <div class="p-3 bg-white/5 rounded-lg border border-white/5 space-y-2">
                <p><strong>Email:</strong> {{ $supportInfo['email'] ?? 'it@company.com' }}</p>
                <p><strong>Hotline:</strong> {{ $supportInfo['hotline'] ?? '1900 xxxx' }}</p>
            </div>
            <a href="{{ $supportInfo['zalo_group'] ?? '#' }}" target="_blank" class="inline-block mt-2 px-4 py-2 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-colors font-medium">
                {{ __('messages.support_zalo_btn') }}
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
            {{ __('messages.about_system_title') }}
        </h3>
        
        <div class="space-y-3 text-sm text-on-surface-variant">
            <p class="text-base text-on-surface font-medium">{{ $aboutInfo['app_name'] ?? 'Phần mềm Quản trị' }}</p>
            <p>{{ __('messages.about_version') }}: <span class="text-primary font-mono bg-primary/10 px-1 rounded">{{ $aboutInfo['version'] ?? 'v1.0' }}</span></p>
            <p>{{ __('messages.about_last_update') }}: {{ $aboutInfo['release_date'] ?? 'N/A' }}</p>
            <div class="pt-3 mt-3 border-t border-white/10">
                <p>{{ __('messages.about_copyright', ['year' => date('Y')]) }} <strong>{{ $aboutInfo['company'] ?? 'Công ty' }}</strong>.</p>
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
            {{ __('messages.terms') }}
        </h3>
        
        <div class="space-y-3 text-sm text-on-surface-variant max-h-[60vh] overflow-y-auto pr-2 leading-relaxed">
            <p class="font-semibold text-on-surface">{{ __('messages.terms_c1_title') }}</p>
            <p>{{ __('messages.terms_c1_content') }}</p>
            <p class="font-semibold text-on-surface">{{ __('messages.terms_c2_title') }}</p>
            <p>{{ __('messages.terms_c2_content') }}</p>
            <p class="font-semibold text-on-surface">{{ __('messages.terms_c3_title') }}</p>
            <p>{{ __('messages.terms_c3_content') }}</p>
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
            {{ __('messages.privacy') }}
        </h3>
        
        <div class="space-y-3 text-sm text-on-surface-variant max-h-[60vh] overflow-y-auto pr-2 leading-relaxed">
            <p class="font-semibold text-on-surface">{{ __('messages.privacy_c1_title') }}</p>
            <p>{{ __('messages.privacy_c1_content') }}</p>
            <p class="font-semibold text-on-surface">{{ __('messages.privacy_c2_title') }}</p>
            <p>{{ __('messages.privacy_c2_content') }}</p>
            <p class="font-semibold text-on-surface">{{ __('messages.privacy_c3_title') }}</p>
            <p>{{ __('messages.privacy_c3_content') }}</p>
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

// --- LOGIC CHATBOT HỖ TRỢ ---
document.addEventListener('DOMContentLoaded', function () {
    // --- LOGIC CHUYỂN ĐỔI CHẾ ĐỘ SÁNG / TỐI (DARK MODE) ---
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const themeToggleThumb = document.getElementById('theme-toggle-thumb');

    if (themeToggleBtn && themeToggleThumb) {
        function syncThemeToggleUI() {
            const isLight = document.documentElement.classList.contains('light');
            const hiddenCheckbox = document.getElementById('theme-toggle');
            if (isLight) {
                themeToggleBtn.setAttribute('aria-checked', 'false');
                themeToggleBtn.className = "relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-transparent bg-slate-400 dark:bg-slate-700";
                themeToggleThumb.className = "inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 translate-x-0.5";
                if (hiddenCheckbox) hiddenCheckbox.checked = false;
            } else {
                themeToggleBtn.setAttribute('aria-checked', 'true');
                themeToggleBtn.className = "relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-transparent bg-sky-500";
                themeToggleThumb.className = "inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 translate-x-5";
                if (hiddenCheckbox) hiddenCheckbox.checked = true;
            }
        }

        syncThemeToggleUI();

        themeToggleBtn.addEventListener('click', function () {
            const currentTheme = document.documentElement.classList.contains('light') ? 'light' : 'dark';
            const nextTheme = currentTheme === 'light' ? 'dark' : 'light';

            fetch('{{ route("settings.personal.setTheme") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ theme: nextTheme })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok') {
                    if (data.theme === 'light') {
                        document.documentElement.classList.remove('dark');
                        document.documentElement.classList.add('light');
                    } else {
                        document.documentElement.classList.remove('light');
                        document.documentElement.classList.add('dark');
                    }
                    syncThemeToggleUI();
                    if (typeof window.showToast === 'function') {
                        window.showToast(data.theme === 'light' ? "{{ __('messages.theme_light_success') }}" : "{{ __('messages.theme_dark_success') }}", 'success');
                    }
                }
            })
            .catch(err => {
                console.error("Lỗi khi chuyển theme:", err);
                if (typeof window.showToast === 'function') {
                    window.showToast('Không thể thay đổi cài đặt giao diện.', 'error');
                }
            });
        });
    }

    const faqButtons = document.querySelectorAll('.faq-btn');
    const chatInput = document.getElementById('chatbot-input');
    const chatMessagesBox = document.getElementById('chat-messages-box');

    const userId = "{{ auth()->id() }}";
    const storageKey = `chat_messages_${userId}`;

    // Click câu hỏi mẫu -> Điền vào ô input và focus
    faqButtons.forEach(button => {
        button.addEventListener('click', function () {
            if (chatInput) {
                chatInput.value = this.textContent.trim();
                chatInput.focus();
            }
        });
    });

    const botReplies = {
        "{{ __('messages.chatbot_faq_report') }}": "{{ __('messages.chatbot_reply_report') }}",
        "{{ __('messages.chatbot_faq_lost_acc') }}": "{{ __('messages.chatbot_reply_lost_acc') }}",
        "{{ __('messages.chatbot_faq_forgot_pwd') }}": "{{ __('messages.chatbot_reply_forgot_pwd') }}",
        "{{ __('messages.chatbot_faq_2fa') }}": "{{ __('messages.chatbot_reply_2fa') }}"
    };

    window.sendChatMessage = function() {
        if (!chatInput) return;
        const messageText = chatInput.value.trim();
        if (messageText === '') return;

        // Reset ô input
        chatInput.value = '';

        // Hiển thị tin nhắn người dùng bên phải và lưu
        appendMessage(messageText, 'user');

        // Hiển thị hiệu ứng 3 dấu chấm nhấp nháy (Loading) của Bot
        showBotLoading();

        // Tự động rep sau 1-2 giây
        setTimeout(() => {
            removeBotLoading();
            const reply = botReplies[messageText] || "{{ __('messages.chatbot_default_reply') }}";
            appendMessage(reply, 'bot');
        }, 1500);
    };

    function renderMessage(text, sender) {
        if (!chatMessagesBox) return;
        
        const msgDiv = document.createElement('div');
        msgDiv.className = `flex items-start gap-3 ${sender === 'user' ? 'justify-end' : ''}`;

        if (sender === 'user') {
            msgDiv.innerHTML = `
                <div class="max-w-[80%] p-3 bg-primary/20 border border-primary/20 text-xs text-primary rounded-2xl rounded-tr-none leading-relaxed">
                    ${escapeHtml(text)}
                </div>
                <div class="w-8 h-8 rounded-full bg-primary/30 flex items-center justify-center text-primary shrink-0 text-xs font-bold shadow-sm">
                    U
                </div>
            `;
        } else {
            msgDiv.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-slate-300 shrink-0 text-xs font-bold shadow-sm">
                    NJ
                </div>
                <div class="max-w-[80%] p-3 bg-white/5 border border-white/5 text-xs text-slate-200 rounded-2xl rounded-tl-none leading-relaxed">
                    ${text}
                </div>
            `;
        }

        chatMessagesBox.appendChild(msgDiv);
        chatMessagesBox.scrollTop = chatMessagesBox.scrollHeight;
    }

    function appendMessage(text, sender) {
        renderMessage(text, sender);
        saveMessageToHistory(text, sender);
    }

    function saveMessageToHistory(text, sender) {
        const stored = localStorage.getItem(storageKey);
        let messages = [];
        if (stored) {
            try {
                messages = JSON.parse(stored);
            } catch (e) {
                messages = [];
            }
        }
        messages.push({ text, sender });
        localStorage.setItem(storageKey, JSON.stringify(messages));
    }

    function loadChatHistory() {
        const stored = localStorage.getItem(storageKey);
        if (stored) {
            try {
                const messages = JSON.parse(stored);
                messages.forEach(msg => {
                    renderMessage(msg.text, msg.sender);
                });
            } catch (e) {
                console.error("Lỗi khi tải lịch sử chat:", e);
            }
        }
    }

    function showBotLoading() {
        if (!chatMessagesBox) return;
        
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'bot-loading-indicator';
        loadingDiv.className = 'flex items-start gap-3';
        loadingDiv.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-slate-300 shrink-0 text-xs font-bold shadow-sm">
                NJ
            </div>
            <div class="max-w-[80%] p-3 bg-white/5 border border-white/5 text-xs text-slate-400 rounded-2xl rounded-tl-none flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></span>
                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce [animation-delay:0.4s]"></span>
            </div>
        `;
        chatMessagesBox.appendChild(loadingDiv);
        chatMessagesBox.scrollTop = chatMessagesBox.scrollHeight;
    }

    function removeBotLoading() {
        const indicator = document.getElementById('bot-loading-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Tải lại lịch sử chat khi load trang
    loadChatHistory();
});
</script>

@endsection