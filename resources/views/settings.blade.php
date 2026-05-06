@extends('layouts.app')

@section('title', 'Cài đặt - NHOMJ')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 px-4 md:px-8 pt-6">
    <!-- Header Section -->
    <div class="space-y-2">
        <h1 class="text-3xl font-bold tracking-tight text-on-surface">Cài đặt hệ thống</h1>
        <p class="text-on-surface-variant">Tùy chỉnh trải nghiệm của bạn trên NHOMJ</p>
    </div>
    <!-- Settings Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Display & Language -->
        <div class="md:col-span-2 glass-panel p-6 rounded-xl space-y-6">
            <div class="flex items-center gap-3 text-primary">
                <span class="material-symbols-outlined">palette</span>
                <h3 class="font-semibold">Hiển thị &amp; Ngôn ngữ</h3>
            </div>
            <div class="space-y-4">
                <!-- Dark Mode Toggle -->
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/5">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-on-surface-variant">dark_mode</span>
                        <div>
                            <p class="font-medium text-sm">Chế độ tối</p>
                            <p class="text-xs text-on-surface-variant">Giảm mỏi mắt trong môi trường thiếu sáng</p>
                        </div>
                    </div>
                    <div class="relative inline-flex items-center cursor-pointer">
                        <input class="sr-only peer" type="checkbox" id="theme-toggle"/>
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </div>
                </div>
                <!-- Language Dropdown -->
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/5">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-on-surface-variant">language</span>
                        <div>
                            <p class="font-medium text-sm">Ngôn ngữ</p>
                            <p class="text-xs text-on-surface-variant">Thay đổi ngôn ngữ hiển thị giao diện</p>
                        </div>
                    </div>
                    <select class="bg-surface-container-high border border-outline-variant text-on-surface text-sm rounded-lg focus:ring-primary focus:border-primary block p-2 px-4 outline-none appearance-none cursor-pointer">
                        <option selected value="vi">Tiếng Việt</option>
                        <option value="en">English</option>
                        <option value="jp">日本語</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- Storage & Performance -->
        <div class="glass-panel p-6 rounded-xl space-y-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-3 text-tertiary">
                    <span class="material-symbols-outlined">database</span>
                    <h3 class="font-semibold">Bộ nhớ</h3>
                </div>
                <div class="text-center py-4">
                    <div class="text-3xl font-bold text-on-surface">128.5 MB</div>
                    <p class="text-xs text-on-surface-variant mt-1">Dung lượng bộ nhớ đệm hiện tại</p>
                </div>
            </div>
            <button class="w-full py-3 bg-white/10 hover:bg-white/20 border border-white/10 text-on-surface font-medium rounded-lg transition-all active:scale-95 text-sm">Xóa bộ nhớ đệm</button>
        </div>
        <!-- Device Management -->
        <div class="md:col-span-3 glass-panel p-6 rounded-xl space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 text-secondary">
                    <span class="material-symbols-outlined">devices</span>
                    <h3 class="font-semibold">Quản lý thiết bị</h3>
                </div>
                <span class="text-xs text-on-surface-variant">3 thiết bị đang hoạt động</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-white/5 rounded-lg border border-white/5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">laptop_mac</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between">
                            <p class="font-semibold text-sm">MacBook Pro M2</p>
                            <span class="text-[10px] bg-primary/20 text-primary px-1.5 py-0.5 rounded uppercase font-bold">Hiện tại</span>
                        </div>
                        <p class="text-xs text-on-surface-variant">Hồ Chí Minh, Việt Nam</p>
                        <p class="text-[10px] text-on-surface-variant mt-2">Trình duyệt Chrome • 2 phút trước</p>
                    </div>
                </div>
                <div class="p-4 bg-white/5 rounded-lg border border-white/5 flex items-start gap-4 hover:border-white/20 transition-all cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-on-surface-variant/20 flex items-center justify-center text-on-surface-variant">
                        <span class="material-symbols-outlined">smartphone</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-sm">iPhone 15 Pro</p>
                        <p class="text-xs text-on-surface-variant">Hà Nội, Việt Nam</p>
                        <p class="text-[10px] text-on-surface-variant mt-2">Ứng dụng NHOMJ • 3 giờ trước</p>
                    </div>
                    <button class="text-error opacity-60 hover:opacity-100 transition-opacity"><span class="material-symbols-outlined text-sm">logout</span></button>
                </div>
                <div class="p-4 bg-white/5 rounded-lg border border-white/5 flex items-start gap-4 hover:border-white/20 transition-all cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-on-surface-variant/20 flex items-center justify-center text-on-surface-variant">
                        <span class="material-symbols-outlined">tablet_android</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-sm">iPad Air 5</p>
                        <p class="text-xs text-on-surface-variant">Đà Nẵng, Việt Nam</p>
                        <p class="text-[10px] text-on-surface-variant mt-2">Trình duyệt Safari • 1 ngày trước</p>
                    </div>
                    <button class="text-error opacity-60 hover:opacity-100 transition-opacity"><span class="material-symbols-outlined text-sm">logout</span></button>
                </div>
            </div>
        </div>
        <!-- Help & Policies -->
        <div class="md:col-span-3 glass-panel p-6 rounded-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-3 text-on-surface-variant">
                    <span class="material-symbols-outlined">help_outline</span>
                    <h3 class="font-semibold">Trợ giúp &amp; Chính sách</h3>
                </div>
                <div class="flex flex-wrap gap-4 md:gap-8">
                    <a class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-sm">description</span> Điều khoản sử dụng</a>
                    <a class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-sm">shield</span> Chính sách bảo mật</a>
                    <a class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-sm">support_agent</span> Liên hệ hỗ trợ</a>
                    <a class="text-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-sm">info</span> Về NHOMJ</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="p-6 border border-error/20 bg-error/5 rounded-xl flex items-center justify-between">
        <div>
            <h4 class="font-semibold text-error">Vùng nguy hiểm</h4>
            <p class="text-xs text-on-surface-variant">Hành động này không thể hoàn tác</p>
        </div>
        <button class="px-6 py-2 border border-error/50 text-error hover:bg-error hover:text-white transition-all rounded-lg text-sm font-medium">Vô hiệu hóa tài khoản</button>
    </div>
</div>

@endsection
