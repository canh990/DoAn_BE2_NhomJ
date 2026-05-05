@extends('layouts.app')

@section('title', 'Chỉnh sửa hồ sơ - ' . ($user->name ?? 'Người dùng'))

@section('content')
<div class="max-w-4xl mx-auto p-4 md:p-8 pb-24">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('profile') }}" class="p-2 glass-panel rounded-full text-slate-400 hover:text-primary transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-on-surface">Chỉnh sửa hồ sơ</h1>
            <p class="text-sm text-on-surface-variant">Cập nhật thông tin cá nhân và giao diện của bạn.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200 animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <section class="relative mb-24">
            <div class="h-48 md:h-64 rounded-3xl overflow-hidden relative group bg-slate-800">
                <img
                    id="cover-preview"
                    alt="Ảnh bìa"
                    class="w-full h-full object-cover transition-opacity duration-300 opacity-70 group-hover:opacity-50"
                    src="{{ $user->anh_bia ? asset('storage/' . $user->anh_bia) : 'https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=2029&auto=format&fit=crop' }}"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent"></div>
                
                <label for="anh_bia" class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                    <div class="flex items-center gap-2 rounded-xl bg-black/50 backdrop-blur-md px-4 py-2 text-white border border-white/20">
                        <span class="material-symbols-outlined">cover_guides</span>
                        <span class="text-sm font-medium">Thay đổi ảnh bìa</span>
                    </div>
                </label>
                <input id="anh_bia" name="anh_bia" type="file" accept="image/*" class="hidden" onchange="previewImage(this, 'cover-preview')">
            </div>

            <div class="absolute -bottom-16 left-8 group">
                <div class="relative">
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-background overflow-hidden glass-panel-elevated shadow-2xl">
                        <img
                            id="avatar-preview"
                            alt="{{ $user->name }}"
                            class="w-full h-full object-cover"
                            src="{{ $user->anh_dai_dien ? asset('storage/' . $user->anh_dai_dien) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}"
                        />
                    </div>
                    <label for="anh_dai_dien" class="absolute inset-0 flex items-center justify-center rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                        <span class="material-symbols-outlined text-white text-3xl">photo_camera</span>
                    </label>
                    <input id="anh_dai_dien" name="anh_dai_dien" type="file" accept="image/*" class="hidden" onchange="previewImage(this, 'avatar-preview')">
                </div>
            </div>
        </section>

        <div class="glass-panel rounded-3xl p-6 md:p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Tên hiển thị</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors">person</span>
                        <input
                            name="ten_dang_nhap"
                            type="text"
                            value="{{ old('ten_dang_nhap', $user->ten_dang_nhap) }}"
                            class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-on-surface focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all outline-none"
                        >
                    </div>
                    @error('ten_dang_nhap') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Địa chỉ Email</label>
                    <div class="relative opacity-60">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">mail</span>
                        <input
                            type="email"
                            value="{{ $user->email }}"
                            disabled
                            class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-3.5 pl-12 pr-4 text-slate-400 cursor-not-allowed"
                        >
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Tiểu sử</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-4 text-slate-500 group-focus-within:text-primary transition-colors">description</span>
                    <textarea
                        name="tieu_su"
                        rows="3"
                        placeholder="Hãy chia sẻ điều gì đó về bản thân..."
                        class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-on-surface focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all outline-none resize-none"
                    >{{ old('tieu_su', $user->tieu_su) }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Ngày sinh</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">cake</span>
                        <input
                            name="ngay_sinh"
                            type="date"
                            value="{{ old('ngay_sinh', optional($user->ngay_sinh)->format('Y-m-d') ?? $user->ngay_sinh) }}"
                            class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-on-surface focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all outline-none [color-scheme:dark]"
                        >
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Nơi ở</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">location_on</span>
                        <input
                            name="noi_o"
                            type="text"
                            value="{{ old('noi_o', $user->noi_o) }}"
                            placeholder="Ví dụ: Hà Nội, Việt Nam"
                            class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-on-surface focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all outline-none"
                        >
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Quyền riêng tư hồ sơ</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">lock</span>
                    <select
                        name="quyen_rieng_tu"
                        class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-on-surface focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all outline-none appearance-none"
                    >
                        <option value="cong_khai" @selected(old('quyen_rieng_tu', $user->quyen_rieng_tu) === 'cong_khai')>Công khai với mọi người</option>
                        <option value="ban_be" @selected(old('quyen_rieng_tu', $user->quyen_rieng_tu) === 'ban_be')>Chỉ bạn bè mới thấy</option>
                        <option value="rieng_tu" @selected(old('quyen_rieng_tu', $user->quyen_rieng_tu) === 'rieng_tu')>Chỉ mình tôi</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-white/5">
                <a href="{{ route('profile') }}" class="px-6 py-3 rounded-2xl font-medium text-slate-400 hover:bg-white/5 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-8 py-3 bg-primary text-white font-bold rounded-2xl hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-primary/20">
                    Lưu thay đổi
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    /**
     * Hàm hiển thị ảnh xem trước ngay lập tức
     */
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        
        if (file) {
            // Kiểm tra định dạng file
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                // Thêm hiệu ứng flash nhẹ khi đổi ảnh
                preview.classList.add('animate-pulse');
                setTimeout(() => preview.classList.remove('animate-pulse'), 500);
            }
            
            reader.readAsDataURL(file);
        }
    }
</script>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out forwards;
    }
</style>
@endsection