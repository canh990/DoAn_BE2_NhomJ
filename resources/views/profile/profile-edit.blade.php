@extends('layouts.app')

@section('title', 'Chỉnh sửa hồ sơ - ' . ($user->name ?? 'Người dùng'))

@section('content')
<!-- Cropper.js for premium profile cropping -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" crossorigin="anonymous"></script>

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
        
        <!-- Hidden inputs for removal flags -->
        <input type="hidden" name="remove_avatar" id="remove_avatar" value="0">
        <input type="hidden" name="remove_cover" id="remove_cover" value="0">

        <section class="relative mb-24">
            <div class="h-48 md:h-64 rounded-3xl overflow-hidden relative group bg-slate-800 {{ $errors->has('anh_bia') ? 'border-2 border-red-500' : '' }}">
                <img
                    id="cover-preview"
                    alt="Ảnh bìa"
                    class="w-full h-full object-cover transition-opacity duration-300 opacity-70 group-hover:opacity-50"
                    src="{{ $user->anh_bia ? asset('storage/' . $user->anh_bia) : 'https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=2029&auto=format&fit=crop' }}" />
                <div class="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent"></div>

                <label for="anh_bia" class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                    <div class="flex items-center gap-2 rounded-xl bg-black/50 backdrop-blur-md px-4 py-2 text-white border border-white/20">
                        <span class="text-sm font-medium">Thay đổi ảnh bìa</span>
                    </div>
                </label>
                <input id="anh_bia" name="anh_bia" type="file" accept="image/*" class="hidden" onchange="previewImage(this, 'cover-preview')">
                
                @if($user->anh_bia)
                <button type="button" id="btn-remove-cover" onclick="removeCoverAction()" class="absolute top-4 right-4 bg-rose-500 hover:bg-rose-600 text-white p-2 rounded-full shadow-lg transition-all active:scale-90 border-2 border-white/10 z-10" title="Xóa ảnh bìa">
                    <span class="material-symbols-outlined text-sm font-bold">close</span>
                </button>
                @endif

                @error('anh_bia')
                <div class="absolute top-4 left-4 flex items-center gap-1.5 rounded-xl bg-red-500/90 backdrop-blur-md px-3 py-1.5 text-white border border-red-500/20 text-xs font-bold z-10 animate-fade-in">
                    <span class="material-symbols-outlined text-sm">error</span>
                    <span>{{ $message }}</span>
                </div>
                @enderror
            </div>

            <div class="absolute -bottom-16 left-8 group">
                <div class="relative">
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 {{ $errors->has('anh_dai_dien') ? 'border-red-500' : 'border-background' }} overflow-hidden glass-panel-elevated shadow-2xl">
                        <img
                            id="avatar-preview"
                            class="w-full h-full object-cover"
                            alt="{{ $user->name }}"
                            src="{{ $user->avatar_url }}" />
                    </div>
                    <label for="anh_dai_dien" class="absolute inset-0 flex items-center justify-center rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                        <span class="material-symbols-outlined text-white text-3xl">photo_camera</span>
                    </label>
                    <input id="anh_dai_dien" name="anh_dai_dien" type="file" accept="image/*" class="hidden" onchange="previewImage(this, 'avatar-preview')">
                </div>
                @if($user->anh_dai_dien)
                <button type="button" id="btn-remove-avatar" onclick="removeAvatarAction()" class="absolute -top-2 -right-2 bg-rose-500 hover:bg-rose-600 text-white p-1.5 rounded-full shadow-lg transition-all active:scale-90 border-2 border-background z-10" title="Xóa ảnh đại diện">
                    <span class="material-symbols-outlined text-sm font-bold">close</span>
                </button>
                @endif

                @error('anh_dai_dien')
                <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 whitespace-nowrap flex items-center gap-1 text-red-400 text-xs font-bold animate-fade-in bg-slate-950/90 px-3 py-1.5 rounded-xl border border-red-500/20 shadow-lg">
                    <span class="material-symbols-outlined text-[14px]">error</span>
                    <span>{{ $message }}</span>
                </div>
                @enderror
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
                            class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-on-surface focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                    </div>
                    @error('ten_dang_nhap') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Số điện thoại</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors">call</span>
                        <input
                            name="so_dien_thoai"
                            type="tel"
                            value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}"
                            placeholder="Ví dụ: 0987654321"
                            class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-on-surface focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                    </div>
                    @error('so_dien_thoai') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Địa chỉ Email</label>
                    <div class="relative opacity-60">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">mail</span>
                        <input
                            type="email"
                            value="{{ $user->email }}"
                            disabled
                            class="w-full bg-slate-900/50 border border-white/5 rounded-2xl py-3.5 pl-12 pr-4 text-slate-400 cursor-not-allowed">
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
                        class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-on-surface focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all outline-none resize-none">{{ old('tieu_su', $user->tieu_su) }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase text-slate-500">Ngày sinh</label>

                    <input
                        type="date"
                        name="ngay_sinh"
                        value="{{ old('ngay_sinh', $user->ngay_sinh ? \Carbon\Carbon::parse($user->ngay_sinh)->format('Y-m-d') : '') }}"
                        class="w-full bg-slate-900/50 border {{ $errors->has('ngay_sinh') ? 'border-red-500/50' : 'border-white/10' }} rounded-2xl py-3.5 px-4 text-on-surface outline-none transition-all focus:border-sky-400/50">

                    @error('ngay_sinh')
                    <div class="flex items-center gap-1 mt-1 text-red-400">
                        <span class="material-symbols-outlined text-sm">error</span>
                        <span class="text-xs font-medium">{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Nơi ở</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">location_on</span>
                        <select
                            id="select-noi-o"
                            name="noi_o"
                            class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-on-surface focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all outline-none appearance-none">
                            <option value="">Chọn tỉnh / thành phố</option>
                            @if(!empty($user->noi_o))
                                <option value="{{ $user->noi_o }}" selected>{{ $user->noi_o }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="glass-panel rounded-2xl p-6 flex flex-col justify-between shadow-[0_0_30px_rgba(125,211,252,0.02)] border border-white/5 relative">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined">lock</span>
                                </div>
                                <h3 class="text-xl font-bold text-on-surface">Chế độ riêng tư</h3>
                            </div>
                            <p class="text-on-surface-variant text-sm leading-relaxed max-w-md">
                                Khi tài khoản của bạn ở chế độ riêng tư, chỉ những người theo dõi bạn mới có thể xem nội dung và các bài đăng của bạn.
                            </p>
                        </div>
                        <!-- Premium Toggle -->
                        <label class="relative inline-flex items-center cursor-pointer mt-1">
                            <input type="checkbox" id="privacy-toggle" name="quyen_rieng_tu_toggle" class="sr-only peer" @checked(old('quyen_rieng_tu', $user->quyen_rieng_tu) === 'rieng_tu') onchange="handlePrivacyToggle(this)">
                            <input type="hidden" name="quyen_rieng_tu" id="quyen_rieng_tu_value" value="{{ old('quyen_rieng_tu', $user->quyen_rieng_tu ?? 'cong_khai') }}">
                            <div class="w-14 h-8 bg-surface-container-highest rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-1 after:left-1 after:bg-primary after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary/20 border border-white/10"></div>
                        </label>
                    </div>
                    
                    <div class="mt-8 flex gap-4">
                        <div class="flex-1 glass-panel-elevated p-4 rounded-lg border transition-all duration-300 {{ old('quyen_rieng_tu', $user->quyen_rieng_tu) !== 'rieng_tu' ? 'border-primary/50 bg-primary/5' : 'border-white/5 opacity-40' }}" id="panel-privacy-public">
                            <span class="text-primary text-xs font-bold uppercase tracking-wider block mb-1">Công khai</span>
                            <p class="text-xs text-on-surface-variant">Mọi người đều có thể tìm thấy bạn.</p>
                        </div>
                        <div class="flex-1 glass-panel-elevated p-4 rounded-lg border transition-all duration-300 {{ old('quyen_rieng_tu', $user->quyen_rieng_tu) === 'rieng_tu' ? 'border-tertiary/50 bg-tertiary/5' : 'border-white/5 opacity-40' }}" id="panel-privacy-private">
                            <span class="text-tertiary text-xs font-bold uppercase tracking-wider block mb-1">Riêng tư</span>
                            <p class="text-xs text-on-surface-variant">Yêu cầu phê duyệt người theo dõi.</p>
                        </div>
                    </div>
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

    {{-- Blocked Users List Panel --}}
    <div class="glass-panel rounded-3xl p-6 md:p-8 space-y-6 mt-8 animate-fade-in">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-400 border border-amber-500/20 shadow-lg shadow-amber-500/5">
                <span class="material-symbols-outlined text-2xl">gavel</span>
            </div>
            <div>
                <h3 class="text-xl font-bold text-on-surface">Danh sách người dùng đã chặn</h3>
                <p class="text-sm text-slate-400">Quản lý những tài khoản bạn đã chặn. Khi chặn, cả hai bên sẽ không thể tương tác hoặc theo dõi nhau.</p>
            </div>
        </div>

        <div id="blocked-users-list" class="space-y-4 pt-4 border-t border-white/5">
            @forelse($blockedUsers as $blockedUser)
                <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 hover:border-white/10 transition-all block-user-item duration-300" data-user-id="{{ $blockedUser->id }}">
                    <div class="flex items-center gap-3">
                        <img 
                            src="{{ $blockedUser->avatar_url }}" 
                            alt="{{ $blockedUser->name }}" 
                            class="w-12 h-12 rounded-full object-cover border-2 border-white/10"
                        />
                        <div>
                            <p class="font-bold text-on-surface text-sm">{{ $blockedUser->name }}</p>
                            <p class="text-xs text-slate-400">{{ '@' . $blockedUser->ten_dang_nhap }}</p>
                        </div>
                    </div>
                    
                    <button 
                        type="button" 
                        onclick="unblockUserAction('{{ $blockedUser->id }}', '{{ $blockedUser->name }}', this)" 
                        class="rounded-xl border border-sky-400/20 bg-sky-400/5 px-4 py-2 text-xs font-semibold text-sky-400 hover:bg-sky-400/20 transition-all active:scale-95 flex items-center gap-1 shadow-lg shadow-sky-500/5"
                    >
                        <span class="material-symbols-outlined text-sm">lock_open</span>
                        Bỏ chặn
                    </button>
                </div>
            @empty
                <div class="text-center py-8" id="blocked-empty-state">
                    <span class="material-symbols-outlined text-4xl text-slate-600 mb-2">person_off</span>
                    <p class="text-slate-400 text-sm font-medium">Bạn chưa chặn người dùng nào.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="glass-panel border-red-500/20 rounded-3xl p-6 flex flex-col md:flex-row items-center justify-between gap-4 mt-8 mb-4 border" style="border-color: rgba(239, 68, 68, 0.2);">
        <div>
            <h3 class="font-bold text-red-500">Vô hiệu hóa tài khoản</h3>
            <p class="text-xs text-slate-400">Tạm thời ẩn hồ sơ và nội dung của bạn khỏi NHOMJ.</p>
        </div>
        <button type="button" onclick="document.getElementById('deactivate-modal').classList.remove('hidden')" class="text-red-500 font-semibold hover:bg-red-500/10 px-4 py-2 rounded-xl transition-colors">Vô hiệu hóa</button>
    </div>

    <div class="glass-panel border-red-600/30 rounded-3xl p-6 flex flex-col md:flex-row items-center justify-between gap-4 mb-8 border" style="border-color: rgba(220, 38, 38, 0.3);">
        <div>
            <h3 class="font-bold text-red-600">Xóa vĩnh viễn tài khoản</h3>
            <p class="text-xs text-slate-400">Xóa toàn bộ dữ liệu của bạn khỏi hệ thống. Hành động này không thể hoàn tác.</p>
        </div>
        <button type="button" onclick="document.getElementById('delete-modal').classList.remove('hidden')" class="bg-red-600/10 text-red-600 font-bold hover:bg-red-600 hover:text-white px-5 py-2.5 rounded-xl transition-all border border-red-600/20 hover:border-red-600 shadow-sm">Xóa tài khoản</button>
    </div>

    <div id="deactivate-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="glass-panel rounded-3xl p-6 w-full max-w-md animate-fade-in relative bg-[#0a0e1a] border border-red-500/20">
            <button type="button" onclick="document.getElementById('deactivate-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
            <h3 class="text-xl font-bold text-red-500 mb-2">Xác nhận vô hiệu hóa</h3>
            @if(empty(auth()->user()->nha_cung_cap_oauth))
                <p class="text-sm text-slate-400 mb-6">Vui lòng nhập mật khẩu của bạn để xác nhận vô hiệu hóa tài khoản. Bạn có thể khôi phục tài khoản bằng cách đăng nhập lại.</p>
            @else
                <p class="text-sm text-slate-400 mb-6">Vui lòng sử dụng mã OTP gửi qua email để xác nhận vô hiệu hóa tài khoản. Bạn có thể khôi phục tài khoản bằng cách đăng nhập lại.</p>
            @endif
            
            <form action="{{ route('profile.deactivate') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    @if(empty(auth()->user()->nha_cung_cap_oauth))
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-red-500 transition-colors">lock</span>
                            <input
                                name="password_deactivate"
                                type="password"
                                required
                                placeholder="Mật khẩu của bạn"
                                class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-white focus:border-red-500/50 focus:ring-4 focus:ring-red-500/10 transition-all outline-none">
                        </div>
                        @error('password_deactivate') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                    @else
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-red-500 transition-colors">mark_email_read</span>
                            <input
                                name="otp_deactivate"
                                type="text"
                                required
                                placeholder="Nhập mã OTP 6 số"
                                maxlength="6"
                                class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-32 text-white focus:border-red-500/50 focus:ring-4 focus:ring-red-500/10 transition-all outline-none tracking-widest font-mono">
                            <button type="button" onclick="sendOtp(this)" class="absolute right-2 top-1/2 -translate-y-1/2 text-sky-400 hover:text-sky-300 text-sm font-semibold px-3 py-1.5 rounded-lg bg-sky-400/10 hover:bg-sky-400/20 transition-colors">
                                Gửi OTP
                            </button>
                        </div>
                        @error('otp_deactivate') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                    @endif
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('deactivate-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl font-medium text-slate-400 hover:bg-white/5 transition-colors">
                            Hủy
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-red-600/20">
                            Vô hiệu hóa
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div class="glass-panel rounded-3xl p-6 w-full max-w-md animate-fade-in relative bg-red-950/20 border border-red-600/30">
            <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
            <div class="flex items-center gap-3 mb-2">
                <span class="material-symbols-outlined text-red-500 text-3xl">warning</span>
                <h3 class="text-xl font-bold text-red-500">Xóa vĩnh viễn</h3>
            </div>
            @if(empty(auth()->user()->nha_cung_cap_oauth))
                <p class="text-sm text-slate-300 mb-6 font-medium">Hành động này KHÔNG THỂ hoàn tác. Vui lòng nhập mật khẩu của bạn để tiếp tục xóa toàn bộ dữ liệu khỏi hệ thống.</p>
            @else
                <p class="text-sm text-slate-300 mb-6 font-medium">Hành động này KHÔNG THỂ hoàn tác. Vui lòng sử dụng mã OTP gửi qua email để tiếp tục xóa toàn bộ dữ liệu khỏi hệ thống.</p>
            @endif
            
            <form action="{{ route('profile.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="space-y-4">
                    @if(empty(auth()->user()->nha_cung_cap_oauth))
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-red-500 transition-colors">lock</span>
                            <input
                                name="password_delete"
                                type="password"
                                required
                                placeholder="Nhập mật khẩu để xác nhận"
                                class="w-full bg-black/40 border border-red-500/20 rounded-2xl py-3.5 pl-12 pr-4 text-white focus:border-red-500 focus:ring-4 focus:ring-red-500/20 transition-all outline-none">
                        </div>
                        @error('password_delete') <p class="text-xs text-red-400 font-bold">{{ $message }}</p> @enderror
                    @else
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-red-500 transition-colors">mark_email_read</span>
                            <input
                                name="otp_delete"
                                type="text"
                                required
                                placeholder="Nhập mã OTP 6 số"
                                maxlength="6"
                                class="w-full bg-black/40 border border-red-500/20 rounded-2xl py-3.5 pl-12 pr-32 text-white focus:border-red-500 focus:ring-4 focus:ring-red-500/20 transition-all outline-none tracking-widest font-mono">
                            <button type="button" onclick="sendOtp(this)" class="absolute right-2 top-1/2 -translate-y-1/2 text-sky-400 hover:text-sky-300 text-sm font-semibold px-3 py-1.5 rounded-lg bg-sky-400/10 hover:bg-sky-400/20 transition-colors">
                                Gửi OTP
                            </button>
                        </div>
                        @error('otp_delete') <p class="text-xs text-red-400 font-bold">{{ $message }}</p> @enderror
                    @endif
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl font-medium text-slate-400 hover:bg-white/10 transition-colors">
                            Hủy bỏ
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-red-600/30">
                            Xóa vĩnh viễn
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Crop Image Modal -->
    <div id="crop-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div class="glass-panel rounded-3xl w-full max-w-2xl animate-fade-in relative bg-slate-900/95 border border-white/10 overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">crop_free</span>
                    Chỉnh sửa hình ảnh
                </h3>
                <button type="button" onclick="closeCropModal()" class="text-slate-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Content Area -->
            <div class="p-6 flex-1 overflow-y-auto flex flex-col items-center justify-center min-h-[300px] max-h-[50vh] bg-slate-950/30">
                <div class="w-full h-full flex items-center justify-center overflow-hidden rounded-2xl border border-white/5 relative bg-black/20">
                    <img id="crop-image" class="max-w-full max-h-full block" src="" alt="Source image">
                </div>
            </div>

            <!-- Controls (Zoom & Actions) -->
            <div class="px-6 py-4 border-t border-white/5 space-y-4 bg-slate-900/60">
                <!-- Zoom Slider -->
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined text-slate-400 text-sm">zoom_out</span>
                    <input type="range" id="crop-zoom" min="0" max="100" value="0" class="flex-1 accent-primary h-1.5 bg-white/10 rounded-lg appearance-none cursor-pointer">
                    <span class="material-symbols-outlined text-slate-400 text-sm">zoom_in</span>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="rotateImage(-90)" class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition-all" title="Xoay trái">
                            <span class="material-symbols-outlined">rotate_left</span>
                        </button>
                        <button type="button" onclick="rotateImage(90)" class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition-all" title="Xoay phải">
                            <span class="material-symbols-outlined">rotate_right</span>
                        </button>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeCropModal()" class="px-5 py-2.5 rounded-xl font-medium text-slate-400 hover:bg-white/5 transition-colors">
                            Hủy
                        </button>
                        <button type="button" onclick="saveCroppedImage()" class="px-6 py-2.5 bg-primary text-white font-bold rounded-xl hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">check</span>
                            Cắt & Lưu
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($errors->has('password_deactivate') || $errors->has('otp_deactivate'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('deactivate-modal').classList.remove('hidden');
    });
</script>
@endif
@if($errors->has('password_delete') || $errors->has('otp_delete'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('delete-modal').classList.remove('hidden');
    });
</script>
@endif

<script>
    window.handlePrivacyToggle = function(toggle) {
        const hiddenVal = document.getElementById('quyen_rieng_tu_value');
        const panelPublic = document.getElementById('panel-privacy-public');
        const panelPrivate = document.getElementById('panel-privacy-private');
        
        if (toggle.checked) {
            hiddenVal.value = 'rieng_tu';
            
            panelPublic.classList.add('opacity-40');
            panelPublic.classList.remove('border-primary/50', 'bg-primary/5');
            panelPublic.classList.add('border-white/5');
            
            panelPrivate.classList.remove('opacity-40', 'border-white/5');
            panelPrivate.classList.add('border-tertiary/50', 'bg-tertiary/5');
        } else {
            hiddenVal.value = 'cong_khai';
            
            panelPrivate.classList.add('opacity-40');
            panelPrivate.classList.remove('border-tertiary/50', 'bg-tertiary/5');
            panelPrivate.classList.add('border-white/5');
            
            panelPublic.classList.remove('opacity-40', 'border-white/5');
            panelPublic.classList.add('border-primary/50', 'bg-primary/5');
        }
    }

    async function unblockUserAction(userId, userName, btn) {
        if (!confirm(`Bạn có chắc chắn muốn bỏ chặn ${userName} không?`)) return;
        
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[12px]">progress_activity</span>';

        try {
            const response = await fetch(`/user/${userId}/unblock`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (window.showToast) {
                    window.showToast(data.message || 'Đã bỏ chặn thành công!', 'success');
                }
                
                // Find and fade out user element
                const row = document.querySelector(`.block-user-item[data-user-id="${userId}"]`);
                if (row) {
                    row.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        row.remove();
                        // Check if block list is now empty, if so, show empty state
                        const remaining = document.querySelectorAll('.block-user-item');
                        if (remaining.length === 0) {
                            const list = document.getElementById('blocked-users-list');
                            list.innerHTML = `
                                <div class="text-center py-8" id="blocked-empty-state">
                                    <span class="material-symbols-outlined text-4xl text-slate-600 mb-2">person_off</span>
                                    <p class="text-slate-400 text-sm font-medium">Bạn chưa chặn người dùng nào.</p>
                                </div>
                            `;
                        }
                    }, 300);
                }
            } else {
                btn.disabled = false;
                btn.innerHTML = originalText;
                if (window.showToast) window.showToast('Có lỗi xảy ra, vui lòng thử lại.', 'error');
            }
        } catch (error) {
            btn.disabled = false;
            btn.innerHTML = originalText;
            console.error('Lỗi bỏ chặn:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectNoiO = document.getElementById('select-noi-o');
        if (selectNoiO) {
            const currentValue = "{{ old('noi_o', $user->noi_o) }}";
            
            fetch('https://provinces.open-api.vn/api/v2/p/')
                .then(response => response.json())
                .then(data => {
                    // Clear existing options except placeholder
                    selectNoiO.innerHTML = '<option value="">Chọn tỉnh / thành phố</option>';
                    
                    data.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.name;
                        option.textContent = province.name;
                        
                        // Check if it matches the current value
                        if (currentValue && (
                            province.name.toLowerCase() === currentValue.toLowerCase() ||
                            province.name.replace(/^(Thành phố|Tỉnh)\s+/i, '').toLowerCase() === currentValue.toLowerCase()
                        )) {
                            option.selected = true;
                        }
                        
                        selectNoiO.appendChild(option);
                    });
                    
                    // If current value doesn't match any standard province name, keep it as custom option at top
                    if (currentValue && !Array.from(selectNoiO.options).some(opt => opt.value === currentValue)) {
                        const customOption = document.createElement('option');
                        customOption.value = currentValue;
                        customOption.textContent = currentValue;
                        customOption.selected = true;
                        selectNoiO.insertBefore(customOption, selectNoiO.options[1]);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải danh sách tỉnh/thành phố:', error);
                });
        }
    });

    function removeAvatarAction() {
        window.openConfirmModal('Xóa ảnh đại diện?', 'Bạn có chắc chắn muốn xóa ảnh đại diện hiện tại không?', () => {
            const preview = document.getElementById('avatar-preview');
            preview.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent('{{ $user->name }}') + '&background=random';
            
            // Đánh dấu để xóa khi nhấn Lưu thay đổi
            document.getElementById('remove_avatar').value = '1';
            
            const btnRemove = document.getElementById('btn-remove-avatar');
            if (btnRemove) btnRemove.classList.add('hidden');
            
            if (window.showToast) window.showToast('Đã đánh dấu xóa ảnh đại diện. Nhấn Lưu thay đổi để hoàn tất.', 'info');
        }, 'Xóa ảnh');
    }

    function removeCoverAction() {
        window.openConfirmModal('Xóa ảnh bìa?', 'Bạn có chắc chắn muốn xóa ảnh bìa hiện tại không?', () => {
            const preview = document.getElementById('cover-preview');
            preview.src = 'https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=2029&auto=format&fit=crop';
            
            // Đánh dấu để xóa khi nhấn Lưu thay đổi
            document.getElementById('remove_cover').value = '1';
            
            const btnRemove = document.getElementById('btn-remove-cover');
            if (btnRemove) btnRemove.classList.add('hidden');
            
            if (window.showToast) window.showToast('Đã đánh dấu xóa ảnh bìa. Nhấn Lưu thay đổi để hoàn tất.', 'info');
        }, 'Xóa ảnh bìa');
    }

    /**
     * Gửi mã OTP xác nhận hành động tài khoản
     */
    function sendOtp(btn) {
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">progress_activity</span>';

        fetch('{{ route('profile.send-action-otp') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.showToast) {
                    window.showToast(data.message, 'success');
                } else {
                    alert(data.message);
                }
                
                // Đếm ngược 60s để gửi lại
                let timeLeft = 60;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                const timer = setInterval(() => {
                    if (timeLeft <= 0) {
                        clearInterval(timer);
                        btn.disabled = false;
                        btn.innerHTML = 'Gửi lại';
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        btn.innerHTML = `Gửi lại (${timeLeft}s)`;
                        timeLeft--;
                    }
                }, 1000);
            } else {
                if (window.showToast) {
                    window.showToast(data.message, 'error');
                } else {
                    alert(data.message);
                }
                btn.disabled = false;
                btn.innerHTML = 'Gửi OTP';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (window.showToast) {
                window.showToast('Có lỗi xảy ra khi gửi OTP.', 'error');
            } else {
                alert('Có lỗi xảy ra khi gửi OTP.');
            }
            btn.disabled = false;
            btn.innerHTML = 'Gửi OTP';
        });
    }
</script>
<script>
    let cropperInstance = null;
    let activeInputId = null;

    /**
     * Hàm hiển thị ảnh xem trước ngay lập tức
     */
    function previewImage(input, previewId) {
        const file = input.files[0];

        if (file) {
            // 1. Kiểm tra loại tệp
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                const errMsg = 'Định dạng ảnh không hợp lệ! Vui lòng chọn ảnh jpg, jpeg, png hoặc webp.';
                if (window.showToast) window.showToast(errMsg, 'error');
                else alert(errMsg);
                input.value = '';
                return;
            }

            // 2. Kiểm tra kích thước tệp
            const maxSize = previewId === 'avatar-preview' ? 2 * 1024 * 1024 : 4 * 1024 * 1024;
            const limitText = previewId === 'avatar-preview' ? '2MB' : '4MB';
            if (file.size > maxSize) {
                const errMsg = `Kích thước ảnh vượt quá giới hạn cho phép (${limitText})!`;
                if (window.showToast) window.showToast(errMsg, 'error');
                else alert(errMsg);
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                openCropModal(e.target.result, input.id);
            };
            reader.readAsDataURL(file);
        }
    }

    function openCropModal(imgSrc, inputId) {
        activeInputId = inputId;
        const cropModal = document.getElementById('crop-modal');
        const cropImage = document.getElementById('crop-image');
        
        cropImage.src = imgSrc;
        
        // Hiện Modal
        cropModal.classList.remove('hidden');
        
        const isAvatar = inputId === 'anh_dai_dien';
        
        if (isAvatar) {
            cropModal.classList.add('circle-crop');
        } else {
            cropModal.classList.remove('circle-crop');
        }
        
        if (cropperInstance) {
            cropperInstance.destroy();
        }
        
        // Khởi tạo Cropper
        cropperInstance = new Cropper(cropImage, {
            aspectRatio: isAvatar ? 1 : (896 / 256), // Ratio 1:1 cho avatar, cover tỷ lệ khung hiển thị
            viewMode: 1, 
            dragMode: 'move', 
            autoCropArea: 0.9,
            restore: false,
            guides: false,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: !isAvatar,
            toggleDragModeOnDblclick: false,
            ready: function() {
                const zoomSlider = document.getElementById('crop-zoom');
                zoomSlider.value = 0;
            }
        });

        // Thiết lập sự kiện thanh trượt Zoom
        const zoomSlider = document.getElementById('crop-zoom');
        zoomSlider.oninput = function() {
            const zoomValue = 1 + (parseFloat(this.value) / 50);
            cropperInstance.zoomTo(zoomValue);
        };
    }

    function rotateImage(deg) {
        if (cropperInstance) {
            cropperInstance.rotate(deg);
        }
    }

    function closeCropModal() {
        const cropModal = document.getElementById('crop-modal');
        cropModal.classList.add('hidden');
        
        if (activeInputId && !document.getElementById(activeInputId).getAttribute('data-saved')) {
            document.getElementById(activeInputId).value = '';
        }
        
        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }
    }

    function saveCroppedImage() {
        if (!cropperInstance) return;
        
        const isAvatar = activeInputId === 'anh_dai_dien';
        const options = isAvatar 
            ? { width: 500, height: 500, imageSmoothingEnabled: true, imageSmoothingQuality: 'high' }
            : { width: 1200, height: 342, imageSmoothingEnabled: true, imageSmoothingQuality: 'high' };
            
        const canvas = cropperInstance.getCroppedCanvas(options);
        
        canvas.toBlob(function(blob) {
            const fileInput = document.getElementById(activeInputId);
            fileInput.setAttribute('data-saved', 'true');
            
            // Gán file đã cắt trực tiếp vào Input file bằng DataTransfer API
            const file = new File([blob], isAvatar ? "cropped_avatar.png" : "cropped_cover.png", { type: "image/png" });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            
            // Cập nhật ảnh Preview lên giao diện
            const previewId = isAvatar ? 'avatar-preview' : 'cover-preview';
            const preview = document.getElementById(previewId);
            preview.src = canvas.toDataURL('image/png');
            
            preview.style.opacity = '1';
            preview.animate([
                { opacity: 0, transform: 'scale(0.95)' },
                { opacity: 1, transform: 'scale(1)' }
            ], { duration: 300, easing: 'ease-out' });
            
            if (isAvatar) {
                const removeAvatarInput = document.getElementById('remove_avatar');
                if (removeAvatarInput) removeAvatarInput.value = '0';
                const btn = document.getElementById('btn-remove-avatar');
                if (btn) btn.classList.remove('hidden');
            } else {
                const removeCoverInput = document.getElementById('remove_cover');
                if (removeCoverInput) removeCoverInput.value = '0';
                const btn = document.getElementById('btn-remove-cover');
                if (btn) btn.classList.remove('hidden');
            }
            
            // Đóng Modal
            const cropModal = document.getElementById('crop-modal');
            cropModal.classList.add('hidden');
            
            if (window.showToast) {
                window.showToast('Cắt và căn chỉnh ảnh thành công!', 'success');
            }
            
            fileInput.removeAttribute('data-saved');
        }, 'image/png');
    }

    function togglePrivacyUI(mode) {
        const publicLabel = document.getElementById('label-privacy-public');
        const privateLabel = document.getElementById('label-privacy-private');
        
        if (mode === 'cong_khai') {
            publicLabel.className = 'relative flex flex-col md:flex-row items-center justify-center gap-2 rounded-xl py-3.5 px-4 cursor-pointer transition-all duration-300 group select-none overflow-hidden bg-primary text-white font-bold shadow-lg shadow-primary/20';
            privateLabel.className = 'relative flex flex-col md:flex-row items-center justify-center gap-2 rounded-xl py-3.5 px-4 cursor-pointer transition-all duration-300 group select-none overflow-hidden text-slate-400 hover:text-white hover:bg-white/5';
        } else {
            privateLabel.className = 'relative flex flex-col md:flex-row items-center justify-center gap-2 rounded-xl py-3.5 px-4 cursor-pointer transition-all duration-300 group select-none overflow-hidden bg-primary text-white font-bold shadow-lg shadow-primary/20';
            publicLabel.className = 'relative flex flex-col md:flex-row items-center justify-center gap-2 rounded-xl py-3.5 px-4 cursor-pointer transition-all duration-300 group select-none overflow-hidden text-slate-400 hover:text-white hover:bg-white/5';
        }
    }
</script>

<style>
    /* Styling cho khung cắt hình tròn (Avatar) */
    .circle-crop .cropper-view-box,
    .circle-crop .cropper-face {
        border-radius: 50%;
    }

    /* Tối ưu hóa giao diện Cropper theo phong cách Dark Mode */
    .cropper-bg {
        background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 25%, transparent 25%, transparent 75%, rgba(255,255,255,0.02) 75%, rgba(255,255,255,0.02)) !important;
        background-color: #020617 !important;
    }
    
    .cropper-line, .cropper-point {
        background-color: #38bdf8 !important;
    }
    
    .cropper-view-box {
        outline: 2px solid #38bdf8 !important;
        outline-color: #38bdf8 !important;
    }

    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.3s ease-out forwards;
    }
</style>
@endsection