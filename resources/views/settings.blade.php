@extends('layouts.app')

@section('title', __('messages.settings_title'))

@section('content')
<div class="max-w-4xl mx-auto space-y-8 px-4 md:px-8 pt-6">
    <div class="space-y-2">
        <h1 class="text-3xl font-bold tracking-tight text-on-surface">{{ __('messages.settings_title') }}</h1>
        <p class="text-on-surface-variant">{{ __('messages.settings_subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 glass-panel p-6 rounded-xl space-y-6">
            <div class="flex items-center gap-3 text-primary">
                <span class="material-symbols-outlined">palette</span>
                <h3 class="font-semibold">{{ __('messages.display_and_language') }}</h3>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/5">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-on-surface-variant">dark_mode</span>
                        <div>
                            <p class="font-medium text-sm">{{ __('messages.dark_mode') }}</p>
                            <p class="text-xs text-on-surface-variant">{{ __('messages.dark_mode_desc') }}</p>
                        </div>
                    </div>
                    <div class="relative inline-flex items-center cursor-pointer">
    <input class="sr-only peer" type="checkbox" id="theme-toggle">
    <div class="w-11 h-6 bg-slate-700 rounded-full peer 
        peer-checked:bg-blue-600 
        after:content-[''] after:absolute after:top-[2px] after:left-[2px] 
        after:bg-white after:border-gray-300 after:border after:rounded-full 
        after:h-5 after:w-5 after:transition-all 
        peer-checked:after:translate-x-full">
    </div>
</div>
                </div>

                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/5">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-on-surface-variant">language</span>
                        <div>
                            <p class="font-medium text-sm">{{ __('messages.language') }}</p>
                            <p class="text-xs text-on-surface-variant">{{ __('messages.language_desc') }}</p>
                        </div>
                    </div>
                    <select id="language-select" class="bg-surface-container-high border border-outline-variant text-on-surface text-sm rounded-lg focus:ring-primary focus:border-primary block p-2 px-4 outline-none appearance-none cursor-pointer">
                        <option value="vi" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>{{ __('messages.lang_vi') }}</option>
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>{{ __('messages.lang_en') }}</option>
                        <option value="ja" {{ app()->getLocale() == 'ja' ? 'selected' : '' }}>日本語</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="glass-panel p-6 rounded-xl space-y-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-3 text-tertiary">
                    <span class="material-symbols-outlined">database</span>
                    <h3 class="font-semibold">{{ __('messages.storage') }}</h3>
                </div>
                <div class="text-center py-4">
                    <div class="text-3xl font-bold text-on-surface">128.5 MB</div>
                    <p class="text-xs text-on-surface-variant mt-1">{{ __('messages.storage_usage') }}</p>
                </div>
            </div>
            <button class="w-full py-3 bg-white/10 hover:bg-white/20 border border-white/10 text-on-surface font-medium rounded-lg transition-all active:scale-95 text-sm">{{ __('messages.clear_cache') }}</button>
        </div>

        <div class="md:col-span-3 glass-panel p-6 rounded-xl space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 text-secondary">
                    <span class="material-symbols-outlined">devices</span>
                    <h3 class="font-semibold">{{ __('messages.device_management') }}</h3>
                </div>
                <span class="text-xs text-on-surface-variant">3 {{ __('messages.devices_active') }}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-white/5 rounded-lg border border-white/5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">laptop_mac</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between">
                            <p class="font-semibold text-sm">MacBook Pro M2</p>
                            <span class="text-[10px] bg-primary/20 text-primary px-1.5 py-0.5 rounded uppercase font-bold">{{ __('messages.current') }}</span>
                        </div>
                        <p class="text-xs text-on-surface-variant">Hồ Chí Minh, Việt Nam</p>
                        <p class="text-[10px] text-on-surface-variant mt-2">{{ __('messages.browser_chrome') }} • 2 {{ __('messages.minutes_ago') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-3 glass-panel p-6 rounded-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-3 text-on-surface-variant">
                    <span class="material-symbols-outlined">help_outline</span>
                    <h3 class="font-semibold">{{ __('messages.help_policies') }}</h3>
                </div>
                <div class="flex flex-wrap gap-4 md:gap-8">
                    <a class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-sm">description</span> {{ __('messages.terms') }}</a>
                    <a class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-sm">shield</span> {{ __('messages.privacy') }}</a>
                    <a class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-sm">support_agent</span> {{ __('messages.support') }}</a>
                    <a class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-sm">info</span> {{ __('messages.about') }}</a>
                </div>
            </div>
        </div>

        <div class="md:col-span-3 p-6 border border-error/20 bg-error/5 rounded-xl flex items-center justify-between">
            <div>
                <h4 class="font-semibold text-error">{{ __('messages.danger_zone_title') }}</h4>
                <p class="text-xs text-on-surface-variant">{{ __('messages.danger_zone_desc') }}</p>
            </div>
            <button class="px-6 py-2 border border-error/50 text-error hover:bg-error hover:text-white transition-all rounded-lg text-sm font-medium">{{ __('messages.disable_account') }}</button>
        </div>
        </div>

    <script>
        (function() {
            const map = {
                'home': '{{ route('home') }}',
                'explore': '{{ url('/explore') }}',
                'notifications': '{{ url('/notifications') }}',
                'chat': '{{ url('/chat') }}',
                'person': '{{ route('profile') }}',
                'settings': '{{ route('settings.index') }}'
            };

            function normalizePath(url) {
                try {
                    const u = new URL(url, location.origin);
                    return u.pathname.replace(/\/$/, '');
                } catch (e) {
                    return url.replace(/\/$/, '');
                }
            }

            function setActiveByUrl(url) {
                const aside = document.querySelector('aside');
                if (!aside) return;
                const nav = aside.querySelector('nav');
                if (!nav) return;
                const path = normalizePath(url || location.pathname);
                nav.querySelectorAll('a').forEach(a => {
                    a.classList.remove('sidebar-active');
                    a.classList.remove('text-sky-300');
                });
                const matched = Array.from(nav.querySelectorAll('a')).find(a => {
                    const href = a.getAttribute('href') || '';
                    const hpath = normalizePath(href);
                    return hpath && (hpath === path);
                });
                if (matched) matched.classList.add('sidebar-active');
            }

            async function fetchAndReplace(url, pushHistory = true) {
                try {
                    const res = await fetch(url, { credentials: 'same-origin' });
                    if (!res.ok) throw new Error('Fetch failed: ' + res.status);
                    const text = await res.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(text, 'text/html');
                    const newMain = doc.querySelector('main');
                    if (newMain) {
                        const main = document.querySelector('main');
                        main.innerHTML = newMain.innerHTML;
                        const newTitle = doc.querySelector('title');
                        if (newTitle) document.title = newTitle.textContent;
                        if (pushHistory) history.pushState({ url }, '', url);
                        setActiveByUrl(url);
                        initSidebar();
                        return true;
                    }
                    throw new Error('No <main> found in response');
                } catch (err) {
                    console.error('[settings] fetchAndReplace error', err);
                    return false;
                }
            }

            function initSidebar() {
                try {
                    const aside = document.querySelector('aside');
                    if (!aside) return;
                    const nav = aside.querySelector('nav');
                    if (!nav) return;
                    const anchors = nav.querySelectorAll('a');
                    anchors.forEach(a => {
                        const icon = a.querySelector('span[data-icon]');
                        const key = icon ? icon.getAttribute('data-icon') : null;
                        const target = key ? map[key] : null;
                        if (target && (!a.getAttribute('href') || a.getAttribute('href') === '#')) {
                            a.setAttribute('href', target);
                        }
                        const newA = a.cloneNode(true);
                        a.parentNode.replaceChild(newA, a);
                        newA.addEventListener('click', function(ev) {
                            const href = newA.getAttribute('href') || target;
                            if (!href || href === '#') return;
                            ev.preventDefault();
                            setActiveByUrl(href);
                            fetchAndReplace(href).then(ok => {
                                if (!ok) window.location.href = href;
                            });
                        });
                    });
                    // Do not inject a settings link here — layout already provides it.
                    setActiveByUrl(location.pathname);
                } catch (e) {
                    console.error('[settings] initSidebar error', e);
                }
            }

            window.addEventListener('popstate', function(e) {
                const url = (e.state && e.state.url) || location.pathname;
                fetchAndReplace(url, false);
            });

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initSidebar);
            } else {
                initSidebar();
            }
        })();
    </script>

@endsection
