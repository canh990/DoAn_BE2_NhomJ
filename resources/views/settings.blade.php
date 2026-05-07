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
                    3 {{ __('messages.devices_active') }}
                </span>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="p-4 bg-white/5 rounded-lg border border-white/5 flex items-start gap-4">

                    <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary">

                        <span class="material-symbols-outlined">
                            laptop_mac
                        </span>

                    </div>

                    <div class="flex-1">

                        <div class="flex justify-between">

                            <p class="font-semibold text-sm">
                                MacBook Pro M2
                            </p>

                            <span class="text-[10px] bg-primary/20 text-primary px-1.5 py-0.5 rounded uppercase font-bold">
                                {{ __('messages.current') }}
                            </span>

                        </div>

                        <p class="text-xs text-on-surface-variant">
                            Hồ Chí Minh, Việt Nam
                        </p>

                        <p class="text-[10px] text-on-surface-variant mt-2">
                            {{ __('messages.browser_chrome') }}
                            •
                            2 {{ __('messages.minutes_ago') }}
                        </p>

                    </div>

                </div>

            </div>
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

                    <a href="#" class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">description</span>
                        {{ __('messages.terms') }}
                    </a>

                    <a href="#" class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">shield</span>
                        {{ __('messages.privacy') }}
                    </a>

                    <a href="#" class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">support_agent</span>
                        {{ __('messages.support') }}
                    </a>

                    <a href="#" class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">info</span>
                        {{ __('messages.about') }}
                    </a>

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
</script>

@endsection