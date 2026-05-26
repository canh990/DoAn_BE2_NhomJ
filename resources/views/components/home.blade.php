@extends('layouts.app')

@section('title', 'Bảng tin')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 max-w-7xl mx-auto p-4 md:p-6 pb-24">
    <!-- ===== CỘT CHÍNH (BẢNG TIN) ===== -->
    <div class="lg:col-span-8 space-y-6">
        <!-- ===== FORM ĐĂNG BÀI ===== -->
        <section class="glass-panel rounded-2xl p-4 shadow-sm relative z-40">
            <div class="flex gap-4">
                <a href="{{ route('profile') }}" class="shrink-0 hover:opacity-80 transition-opacity" title="Xem trang cá nhân">
                    <img class="w-12 h-12 rounded-full border border-sky-400/20 object-cover" alt="Avatar" src="{{ Auth::user()->anh_dai_dien ? asset('storage/' . Auth::user()->anh_dai_dien) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}">
                </a>
                <div class="w-full">
                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Textarea không viền, tối ưu không gian -->
                        <textarea id="post-content" name="noi_dung" maxlength="280" class="w-full bg-transparent border-none focus:ring-0 text-slate-100 placeholder-slate-500 resize-none text-lg leading-relaxed p-0 min-h-[120px]" placeholder="Bạn đang nghĩ gì?" rows="4">{{ old('noi_dung') }}</textarea>
                        
                        @error('noi_dung')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror

                        <!-- Hiển thị cảm xúc / hoạt động đang chọn -->
                        <div id="feeling-display-container" class="mt-2 hidden items-center gap-2 text-sm text-slate-300 bg-white/5 w-fit px-3 py-1.5 rounded-full border border-white/10">
                            <span class="material-symbols-outlined text-yellow-400 text-sm">mood</span>
                            <span id="feeling-text">Đang cảm thấy vui</span>
                            <button type="button" id="remove-feeling-btn" class="hover:text-red-400 transition-colors ml-1 flex items-center">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </div>

                        <!-- Input ẩn để lưu dữ liệu -->
                        <input type="hidden" name="cam_xuc" id="input-cam_xuc">
                        <input type="hidden" name="hoat_dong" id="input-hoat_dong">

                        <!-- Nút chọn file ẩn -->
                        <input type="file" id="post-image" name="anh[]" accept="image/*,video/*" multiple class="hidden">
                        
                        <!-- Vùng hiển thị ảnh/video xem trước -->
                        <div id="image-preview-container" class="mt-3 hidden">
                            <div id="preview-grid" class="grid gap-2"></div>
                            <button type="button" id="remove-all-images" class="mt-2 text-sm text-red-400 hover:text-red-300 hidden items-center gap-1">
                                <span class="material-symbols-outlined text-sm">delete</span> Xóa tất cả tệp
                            </button>
                        </div>

                        <!-- Đường kẻ ngang phân cách -->
                        <div class="h-px bg-white/10 my-4"></div>

                        <!-- Thanh công cụ và nút đăng bài -->
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <!-- Danh sách công cụ -->
                            <div class="flex items-center gap-1 -ml-2 flex-wrap">
                                <button type="button" id="image-btn" class="p-2 text-sky-400 hover:bg-sky-400/10 rounded-full transition-colors" title="Ảnh/Video">
                                    <span class="material-symbols-outlined" data-icon="image">image</span>
                                </button>
                                <button type="button" class="p-2 text-purple-400 hover:bg-purple-400/10 rounded-full transition-colors" title="Ảnh GIF">
                                    <span class="material-symbols-outlined" data-icon="gif_box">gif_box</span>
                                </button>
                                <button type="button" class="p-2 text-emerald-400 hover:bg-emerald-400/10 rounded-full transition-colors" title="Gắn thẻ">
                                    <span class="material-symbols-outlined" data-icon="label">label</span>
                                </button>
                                <div class="relative z-50">
                                    <button type="button" id="btn-feeling" class="p-2 text-yellow-400 hover:bg-yellow-400/10 rounded-full transition-colors" title="Cảm xúc/Hoạt động">
                                        <span class="material-symbols-outlined" data-icon="mood">mood</span>
                                    </button>
                                    <!-- Dropdown cảm xúc -->
                                    <div id="feeling-dropdown" class="hidden absolute top-full left-0 mt-2 w-48 bg-slate-800 border border-white/10 rounded-xl shadow-2xl overflow-hidden flex-col py-1 text-sm text-left">
                                        <div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Cảm xúc</div>
                                        <button type="button" class="feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="cam_xuc" data-val="vui_ve" data-label="Vui vẻ"><span class="text-xl">😀</span> Vui vẻ</button>
                                        <button type="button" class="feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="cam_xuc" data-val="phan_no" data-label="Phẫn nộ"><span class="text-xl">😡</span> Phẫn nộ</button>
                                        <button type="button" class="feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="cam_xuc" data-val="buon" data-label="Buồn"><span class="text-xl">😢</span> Buồn</button>
                                        <button type="button" class="feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="cam_xuc" data-val="wow" data-label="Wow"><span class="text-xl">😮</span> Wow</button>
                                        <div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider border-t border-white/10 mt-1">Hoạt động</div>
                                        <button type="button" class="feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="hoat_dong" data-val="Đang xem phim" data-label="Đang xem phim"><span class="text-xl">🎬</span> Đang xem phim</button>
                                        <button type="button" class="feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="hoat_dong" data-val="Đang nghe nhạc" data-label="Đang nghe nhạc"><span class="text-xl">🎵</span> Đang nghe nhạc</button>
                                        <button type="button" class="feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="hoat_dong" data-val="Đang đi chơi" data-label="Đang đi chơi"><span class="text-xl">✈️</span> Đang đi chơi</button>
                                    </div>
                                </div>
                                <button type="button" class="p-2 text-red-400 hover:bg-red-400/10 rounded-full transition-colors" title="Vị trí">
                                    <span class="material-symbols-outlined" data-icon="location_on">location_on</span>
                                </button>
                                <button type="button" class="p-2 text-sky-300 hover:bg-sky-300/10 rounded-full transition-colors" title="Thăm dò ý kiến">
                                    <span class="material-symbols-outlined" data-icon="poll">poll</span>
                                </button>
                            </div>

                        <!-- Bộ đếm ký tự và nút Submit -->
                            <div class="flex items-center justify-end gap-4 border-t border-white/5 pt-3 sm:border-t-0 sm:pt-0">
                                <span id="post-char-count" class="text-xs font-mono text-slate-500">0/280</span>
                                <button id="post-submit-button" type="submit" class="bg-sky-500 text-white px-8 py-2 rounded-full font-bold hover:bg-sky-600 transition-all shadow-lg shadow-sky-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                    {{ __('messages.home_post') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        {{-- ===== STORIES BAR ===== --}}
        @include('components.stories-bar', ['stories' => $stories ?? collect()])

        <!-- ===== DANH SÁCH BÀI VIẾT ===== -->
        <div id="post-list-container" class="space-y-6">
            @forelse($posts as $post)
                <x-post-card :post="$post" class="mb-6" />
            @empty
                <div class="glass-panel rounded-2xl p-6 text-center text-slate-300">
                    <p class="text-sm">Chưa có bài viết nào. Hãy là người đầu tiên đăng trạng thái!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ===== CỘT PHỤ (SIDEBAR) ===== -->
    <div class="hidden lg:block lg:col-span-4 space-y-6">
        <!-- Phần Phương tiện mới nhất -->
        <section class="glass-panel rounded-2xl p-5 sticky top-24">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-sky-300 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sky-400">perm_media</span>
                    Phương tiện mới nhất
                </h3>
                <a href="{{ route('explore') }}" class="text-xs text-slate-500 hover:text-sky-300 transition-colors">Khám phá</a>
            </div>
            
            <div class="grid grid-cols-3 gap-2">
                @forelse($recentMedia ?? collect() as $media)
                    @php
                        $mediaSrc = \Illuminate\Support\Str::startsWith($media->duong_dan, ['http://', 'https://'])
                            ? $media->duong_dan
                            : asset('storage/' . ltrim($media->duong_dan, '/'));
                        $isVideo = $media->loai === 'video' || \Illuminate\Support\Str::endsWith($media->duong_dan, ['.mp4', '.webm', '.mov']);
                    @endphp
                    <a href="{{ route('posts.show', $media->bai_viet_id) }}" class="aspect-square overflow-hidden rounded-xl bg-slate-800 relative group block border border-white/5">
                        @if($isVideo)
                            <video src="{{ $mediaSrc }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" muted playsinline></video>
                            <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/40 transition-colors">
                                <span class="material-symbols-outlined text-white text-xl drop-shadow-md">play_circle</span>
                            </div>
                        @else
                            <img class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                 src="{{ $mediaSrc }}" 
                                 alt="Media" />
                        @endif
                    </a>
                @empty
                    <div class="col-span-3 py-10 text-center text-xs text-slate-500 bg-slate-900/50 rounded-xl border border-dashed border-white/10">
                        Chưa có phương tiện nào.
                    </div>
                @endforelse
            </div>
            
            <div class="mt-4 pt-4 border-t border-white/5">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-sky-400/5 border border-sky-400/10">
                    <div class="h-8 w-8 rounded-full bg-sky-400/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-sky-400 text-lg">trending_up</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-sky-300">Xu hướng NHOMJ</p>
                        <p class="text-[10px] text-slate-500">Xem những gì đang diễn ra</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Có thể thêm phần Gợi ý theo dõi ở đây -->
    </div>
</div>

</div>

<!-- ===== SCRIPTS ===== -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('post-content');
        const counter = document.getElementById('post-char-count');
        const submitButton = document.getElementById('post-submit-button');
        const imageBtn = document.getElementById('image-btn');
        const imageInput = document.getElementById('post-image');
        const imagePreviewContainer = document.getElementById('image-preview-container');
        const previewGrid = document.getElementById('preview-grid');
        const removeAllBtn = document.getElementById('remove-all-images');
        let selectedFiles = [];

        if (!textarea || !counter || !submitButton) {
            return;
        }

        const maxLength = Number(textarea.getAttribute('maxlength')) || 280;

        const updateCount = function() {
            const length = textarea.value.length;
            counter.textContent = `${length}/${maxLength}`;
            updateSubmitButton();
        };

        const updateSubmitButton = function() {
            const hasText = textarea.value.trim().length > 0;
            const hasImage = selectedFiles.length > 0;
            submitButton.disabled = !hasText && !hasImage;
        };

        textarea.addEventListener('input', updateCount);

        // Image upload handling
        if(imageBtn) {
            imageBtn.addEventListener('click', function() {
                imageInput.click();
            });
        }

        if(imageInput) {
            imageInput.addEventListener('change', async function(e) {
                const files = Array.from(e.target.files);
                const validFiles = [];
                
                for (const file of files) {
                    if (file.type.startsWith('video/')) {
                        const isValidDuration = await checkVideoDuration(file, 120); // 120 giây = 2 phút
                        if (!isValidDuration) {
                            alert(`Video "${file.name}" vượt quá thời lượng cho phép (tối đa 2 phút).`);
                            continue;
                        }
                    }
                    validFiles.push(file);
                }

                if (validFiles.length > 0) {
                    selectedFiles = selectedFiles.concat(validFiles);
                    updateFileInput();
                    renderPreviews();
                    updateSubmitButton();
                } else {
                    updateFileInput(); // Clear input if all files are invalid so they can be selected again
                }
            });
        }

        function checkVideoDuration(file, maxSeconds) {
            return new Promise((resolve) => {
                const video = document.createElement('video');
                video.preload = 'metadata';
                video.onloadedmetadata = function() {
                    window.URL.revokeObjectURL(video.src);
                    resolve(video.duration <= maxSeconds);
                };
                video.onerror = function() {
                    resolve(false); // Lỗi không đọc được
                };
                video.src = URL.createObjectURL(file);
            });
        }

        function updateFileInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            imageInput.files = dt.files;
        }

        function renderPreviews() {
            if (!previewGrid) return;
            previewGrid.innerHTML = '';
            
            if (selectedFiles.length === 0) {
                imagePreviewContainer.classList.add('hidden');
                if (removeAllBtn) removeAllBtn.style.display = 'none';
                return;
            }
            
            imagePreviewContainer.classList.remove('hidden');
            if (removeAllBtn) removeAllBtn.style.display = 'inline-flex';
            
            previewGrid.className = 'grid gap-2 ' + (selectedFiles.length > 1 ? 'grid-cols-2 sm:grid-cols-3' : 'grid-cols-1');

            selectedFiles.forEach((file, index) => {
                const isVideo = file.type.startsWith('video/');
                const objectUrl = URL.createObjectURL(file);
                
                const div = document.createElement('div');
                div.className = 'relative group rounded-xl overflow-hidden border border-white/10 bg-slate-900/50 ' + (selectedFiles.length > 1 ? 'aspect-square' : '');
                
                let mediaElement = '';
                if (isVideo) {
                    mediaElement = `<video src="${objectUrl}" class="w-full h-full ${selectedFiles.length > 1 ? 'object-cover' : 'max-h-64 object-contain'}" controls controlsList="nodownload"></video>`;
                } else {
                    mediaElement = `<img src="${objectUrl}" class="w-full h-full ${selectedFiles.length > 1 ? 'object-cover' : 'max-h-64 object-contain'}">`;
                }
                
                div.innerHTML = `
                    ${mediaElement}
                    <button type="button" class="remove-single-image absolute top-2 right-2 bg-slate-900/80 hover:bg-red-500 text-white rounded-full p-1.5 transition-colors backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 z-10" data-index="${index}" title="Xóa tệp này">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                `;
                previewGrid.appendChild(div);
            });
        }

        if (previewGrid) {
            previewGrid.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-single-image');
                if (removeBtn) {
                    const index = parseInt(removeBtn.getAttribute('data-index'));
                    selectedFiles.splice(index, 1);
                    updateFileInput();
                    renderPreviews();
                    updateSubmitButton();
                }
            });
        }

        if (removeAllBtn) {
            removeAllBtn.addEventListener('click', function() {
                selectedFiles = [];
                updateFileInput();
                renderPreviews();
                updateSubmitButton();
            });
        }

        updateCount();

        // Feeling dropdown logic
        const btnFeeling = document.getElementById('btn-feeling');
        const feelingDropdown = document.getElementById('feeling-dropdown');
        const feelingOptions = document.querySelectorAll('.feeling-option');
        const feelingDisplayContainer = document.getElementById('feeling-display-container');
        const feelingText = document.getElementById('feeling-text');
        const removeFeelingBtn = document.getElementById('remove-feeling-btn');
        const inputCamXuc = document.getElementById('input-cam_xuc');
        const inputHoatDong = document.getElementById('input-hoat_dong');

        if (btnFeeling) {
            btnFeeling.addEventListener('click', function(e) {
                feelingDropdown.classList.toggle('hidden');
                feelingDropdown.classList.toggle('flex');
            });
        }

        document.addEventListener('click', function(e) {
            if (btnFeeling && feelingDropdown && !btnFeeling.contains(e.target) && !feelingDropdown.contains(e.target)) {
                feelingDropdown.classList.add('hidden');
                feelingDropdown.classList.remove('flex');
            }
        });

        feelingOptions.forEach(btn => {
            btn.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                const val = this.getAttribute('data-val');
                const label = this.getAttribute('data-label') || val;
                
                inputCamXuc.value = '';
                inputHoatDong.value = '';

                if (type === 'cam_xuc') {
                    inputCamXuc.value = val;
                    feelingText.textContent = `Đang cảm thấy ${label.toLowerCase()}`;
                } else if (type === 'hoat_dong') {
                    inputHoatDong.value = val;
                    feelingText.textContent = label;
                }

                feelingDisplayContainer.classList.remove('hidden');
                feelingDisplayContainer.classList.add('flex');
                feelingDropdown.classList.add('hidden');
                feelingDropdown.classList.remove('flex');
                updateSubmitButton();
            });
        });

        if (removeFeelingBtn) {
            removeFeelingBtn.addEventListener('click', function() {
                inputCamXuc.value = '';
                inputHoatDong.value = '';
                feelingDisplayContainer.classList.add('hidden');
                feelingDisplayContainer.classList.remove('flex');
                updateSubmitButton();
            });
        }

        const originalUpdateSubmitButton = updateSubmitButton;
        updateSubmitButton = function() {
            const hasFeeling = inputCamXuc.value || inputHoatDong.value;
            const textLength = contentTextarea.value.trim().length;
            if (textLength > 0 || selectedFiles.length > 0 || hasFeeling) {
                submitButton.removeAttribute('disabled');
            } else {
                submitButton.setAttribute('disabled', 'true');
            }
        };

    });
</script>
@endsection