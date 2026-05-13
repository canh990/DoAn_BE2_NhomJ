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
        
        <!-- Hidden inputs for removal flags -->
        <input type="hidden" name="remove_avatar" id="remove_avatar" value="0">
        <input type="hidden" name="remove_cover" id="remove_cover" value="0">

        <section class="relative mb-24">
            <div class="h-48 md:h-64 rounded-3xl overflow-hidden relative group bg-slate-800">
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
            </div>

            <div class="absolute -bottom-16 left-8 group">
                <div class="relative">
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-background overflow-hidden glass-panel-elevated shadow-2xl">
                        <img
                            id="avatar-preview"
                            class="w-full h-full object-cover"
                            alt="{{ $user->name }}"
                            src="{{ $user->anh_dai_dien ? asset('storage/' . $user->anh_dai_dien) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}" />
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

                <div class="space-y-2 md:col-span-2">
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
                        <input
                            name="noi_o"
                            type="text"
                            value="{{ old('noi_o', $user->noi_o) }}"
                            placeholder="Ví dụ: Hà Nội, Việt Nam"
                            class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-on-surface focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Quyền riêng tư hồ sơ</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">lock</span>
                    <select
                        name="quyen_rieng_tu"
                        class="w-full bg-slate-900/50 border border-white/10 rounded-2xl py-3.5 pl-12 pr-4 text-on-surface focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all outline-none appearance-none">
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
</script>
<script>
    /**
     * Hàm hiển thị ảnh xem trước ngay lập tức
     */
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];

        if (file) {
            // Kiểm tra xem file có phải là ảnh không
            if (!file.type.startsWith('image/')) {
                alert('Vui lòng chọn tệp hình ảnh!');
                return;
            }

            const reader = new FileReader();

            // Hiệu ứng bắt đầu load (làm mờ nhẹ ảnh cũ)
            preview.style.opacity = '0.5';

            reader.onload = function(e) {
                preview.src = e.target.result;

                // Reset flag xóa và hiện nút xóa nếu có ảnh mới
                if (previewId === 'avatar-preview') {
                    const removeAvatarInput = document.getElementById('remove_avatar');
                    if (removeAvatarInput) removeAvatarInput.value = '0';
                    const btn = document.getElementById('btn-remove-avatar');
                    if (btn) btn.classList.remove('hidden');
                } else if (previewId === 'cover-preview') {
                    const removeCoverInput = document.getElementById('remove_cover');
                    if (removeCoverInput) removeCoverInput.value = '0';
                    const btn = document.getElementById('btn-remove-cover');
                    if (btn) btn.classList.remove('hidden');
                }

                // Khi ảnh mới đã load xong
                preview.onload = function() {
                    preview.style.opacity = '1';
                    // Thêm hiệu ứng xuất hiện mượt mà
                    preview.animate([{
                            opacity: 0,
                            transform: 'scale(0.95)'
                        },
                        {
                            opacity: 1,
                            transform: 'scale(1)'
                        }
                    ], {
                        duration: 300,
                        easing: 'ease-out'
                    });
                };
            };

            reader.readAsDataURL(file);
        }
    }
</script>


<style>
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