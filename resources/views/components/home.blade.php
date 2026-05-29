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
                    <img class="w-12 h-12 rounded-full border border-sky-400/20 object-cover" alt="Avatar" src="{{ Auth::user()->avatar_url }}">
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

                        <!-- Hiển thị địa điểm đang chọn -->
                        <div id="location-display-container" class="mt-2 hidden items-center gap-2 text-sm text-slate-300 bg-white/5 w-fit px-3 py-1.5 rounded-full border border-white/10">
                            <span class="material-symbols-outlined text-red-400 text-sm">location_on</span>
                            <span id="location-text">Check-in tại: </span>
                            <button type="button" id="remove-location-btn" class="hover:text-red-400 transition-colors ml-1 flex items-center" title="Xóa địa điểm">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </div>

                        <!-- Input ẩn để lưu dữ liệu -->
                        <input type="hidden" name="cam_xuc" id="input-cam_xuc">
                        <input type="hidden" name="hoat_dong" id="input-hoat_dong">
                        <input type="hidden" name="ten_dia_diem" id="input-ten_dia_diem">
                        <input type="hidden" name="vi_do" id="input-vi_do">
                        <input type="hidden" name="kinh_do" id="input-kinh_do">

                        <!-- Nút chọn file ẩn -->
                        <input type="file" id="post-image" name="anh[]" accept="image/*,video/*" multiple class="hidden">
                        
                        <!-- Vùng hiển thị ảnh/video xem trước -->
                        <div id="image-preview-container" class="mt-3 hidden">
                            <div id="preview-grid" class="grid gap-2"></div>
                            <button type="button" id="remove-all-images" class="mt-2 text-sm text-red-400 hover:text-red-300 hidden items-center gap-1">
                                <span class="material-symbols-outlined text-sm">delete</span> Xóa tất cả tệp
                            </button>
                        </div>

                        <div id="poll-creator-container" class="mt-3 hidden p-4 bg-slate-800/80 border border-white/10 rounded-2xl">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-slate-200">Tạo cuộc bình chọn</h4>
                                <button type="button" id="close-poll-creator" class="text-slate-400 hover:text-red-400 transition-colors">
                                    <span class="material-symbols-outlined text-sm">close</span>
                                </button>
                            </div>

                            <input
                                type="text"
                                id="poll-question-input"
                                name="poll_question"
                                placeholder="Nhập câu hỏi bình chọn..."
                                class="w-full bg-slate-900/60 border border-white/10 focus:border-sky-400/50 rounded-xl px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:ring-0 transition-colors"
                            >

                            <div id="poll-options-list" class="mt-3 space-y-2">
                                <div class="option-item">
                                    <input type="text" name="poll_options[]" placeholder="Lựa chọn 1" class="w-full bg-slate-900/40 border border-white/5 focus:border-sky-400/30 rounded-xl px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:ring-0 transition-colors">
                                </div>
                                <div class="option-item">
                                    <input type="text" name="poll_options[]" placeholder="Lựa chọn 2" class="w-full bg-slate-900/40 border border-white/5 focus:border-sky-400/30 rounded-xl px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:ring-0 transition-colors">
                                </div>
                            </div>

                            <button type="button" id="btn-add-poll-option" class="mt-3 text-xs font-semibold text-sky-400 hover:text-sky-300 transition-colors">
                                + Thêm lựa chọn
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
                                <div class="relative z-50">
                                    <button type="button" id="btn-location" class="p-2 text-red-400 hover:bg-red-400/10 rounded-full transition-colors" title="Vị trí">
                                        <span class="material-symbols-outlined" data-icon="location_on">location_on</span>
                                    </button>
                                    <!-- Dropdown tìm kiếm địa điểm check-in -->
                                    <div id="location-dropdown" class="hidden absolute top-full left-0 mt-2 w-72 bg-slate-800 border border-white/10 rounded-2xl shadow-2xl overflow-hidden flex-col p-3 text-sm text-left">
                                        <div class="px-1 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Check-in địa điểm</div>
                                        
                                        <!-- Ô tìm kiếm -->
                                        <div class="flex items-center bg-slate-900/60 border border-white/10 rounded-xl px-3 py-1.5 mb-2 focus-within:border-sky-400/50 transition-all">
                                            <span class="material-symbols-outlined text-slate-500 text-sm mr-2">search</span>
                                            <input id="location-search-input" type="text" placeholder="Tìm kiếm địa điểm..." autocomplete="off" class="bg-transparent border-none focus:ring-0 p-0 text-xs text-slate-100 placeholder:text-slate-500 w-full">
                                        </div>
                                        
                                        <!-- Nút GPS vị trí hiện tại -->
                                        <button type="button" id="btn-gps-location" class="w-full text-left px-3 py-2 rounded-xl hover:bg-white/5 flex items-center gap-2 transition-colors text-xs text-sky-400 font-medium border border-sky-400/10 bg-sky-400/5 mb-2">
                                            <span class="material-symbols-outlined text-sm">my_location</span>
                                            <span id="gps-btn-text">Sử dụng vị trí hiện tại</span>
                                        </button>
                                        
                                        <!-- Vùng kết quả tìm kiếm -->
                                        <div id="location-results-container" class="max-h-40 overflow-y-auto space-y-1 custom-scrollbar">
                                            <div class="text-[11px] text-slate-500 text-center py-4">Nhập tên để tìm kiếm hoặc dùng GPS...</div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="btn-poll" class="p-2 text-sky-300 hover:bg-sky-300/10 rounded-full transition-colors" title="Thăm dò ý kiến">
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

        // Location check-in dropdown & API logic
        const btnLocation = document.getElementById('btn-location');
        const locationDropdown = document.getElementById('location-dropdown');
        const locationSearchInput = document.getElementById('location-search-input');
        const btnGpsLocation = document.getElementById('btn-gps-location');
        const gpsBtnText = document.getElementById('gps-btn-text');
        const locationResultsContainer = document.getElementById('location-results-container');
        const locationDisplayContainer = document.getElementById('location-display-container');
        const locationText = document.getElementById('location-text');
        const removeLocationBtn = document.getElementById('remove-location-btn');
        const inputTenDiaDiem = document.getElementById('input-ten_dia_diem');
        const inputViDo = document.getElementById('input-vi_do');
        const inputKinhDo = document.getElementById('input-kinh_do');

        if (btnLocation) {
            btnLocation.addEventListener('click', function(e) {
                e.stopPropagation();
                locationDropdown.classList.toggle('hidden');
                if (!locationDropdown.classList.contains('hidden')) {
                    locationSearchInput.focus();
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (btnLocation && locationDropdown && !btnLocation.contains(e.target) && !locationDropdown.contains(e.target)) {
                locationDropdown.classList.add('hidden');
            }
        });

        let searchDebounceTimeout = null;

        if (locationSearchInput) {
            locationSearchInput.addEventListener('input', function() {
                clearTimeout(searchDebounceTimeout);
                const query = this.value.trim();
                if (!query) {
                    locationResultsContainer.innerHTML = '<div class="text-[11px] text-slate-500 text-center py-4">Nhập tên để tìm kiếm hoặc dùng GPS...</div>';
                    return;
                }

                locationResultsContainer.innerHTML = `
                    <div class="flex items-center justify-center py-4">
                        <div class="w-4 h-4 border-2 border-sky-400 border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-[11px] text-slate-400 ml-2">Đang tìm kiếm...</span>
                    </div>
                `;

                searchDebounceTimeout = setTimeout(() => {
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&addressdetails=1`, {
                        headers: {
                            'Accept-Language': 'vi,en;q=0.9'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.length === 0) {
                            locationResultsContainer.innerHTML = '<div class="text-[11px] text-slate-500 text-center py-4">Không tìm thấy địa điểm nào.</div>';
                            return;
                        }

                        locationResultsContainer.innerHTML = '';
                        data.forEach(item => {
                            const name = item.display_name;
                            const lat = item.lat;
                            const lon = item.lon;
                            const shortName = name.split(',').slice(0, 3).join(',').trim();

                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'w-full text-left px-3 py-2 rounded-xl hover:bg-white/5 flex items-start gap-2 transition-colors text-xs text-slate-300 border border-transparent hover:border-white/5';
                            btn.innerHTML = `
                                <span class="material-symbols-outlined text-sm text-red-400 mt-0.5">location_on</span>
                                <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-slate-200 truncate">${shortName}</div>
                                    <div class="text-[10px] text-slate-500 truncate">${name}</div>
                                </div>
                            `;

                            btn.addEventListener('click', function() {
                                selectLocation(shortName, lat, lon);
                            });

                            locationResultsContainer.appendChild(btn);
                        });
                    })
                    .catch(err => {
                        console.error('Lỗi tìm kiếm địa điểm:', err);
                        locationResultsContainer.innerHTML = '<div class="text-[11px] text-rose-400 text-center py-4">Lỗi kết nối bản đồ. Hãy thử lại.</div>';
                    });
                }, 500);
            });
        }

        if (btnGpsLocation) {
            btnGpsLocation.addEventListener('click', function(e) {
                e.stopPropagation();
                if (!navigator.geolocation) {
                    alert('Trình duyệt của bạn không hỗ trợ định vị GPS.');
                    return;
                }

                gpsBtnText.textContent = 'Đang định vị GPS...';
                btnGpsLocation.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;

                        gpsBtnText.textContent = 'Đang giải mã tọa độ...';

                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`, {
                            headers: {
                                'Accept-Language': 'vi,en;q=0.9'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            let locName = 'Vị trí của bạn';
                            if (data && data.address) {
                                const addr = data.address;
                                const parts = [];
                                if (addr.road) parts.push(addr.road);
                                else if (addr.suburb) parts.push(addr.suburb);
                                else if (addr.neighbourhood) parts.push(addr.neighbourhood);

                                if (addr.district) parts.push(addr.district);
                                else if (addr.quarter) parts.push(addr.quarter);

                                if (addr.city) parts.push(addr.city);
                                else if (addr.town) parts.push(addr.town);
                                else if (addr.province) parts.push(addr.province);
                                else if (addr.state) parts.push(addr.state);

                                if (parts.length > 0) {
                                    locName = parts.slice(0, 3).join(', ');
                                } else if (data.display_name) {
                                    locName = data.display_name.split(',').slice(0, 2).join(', ').trim();
                                }
                            }

                            selectLocation(locName, lat, lon);
                            gpsBtnText.textContent = 'Sử dụng vị trí hiện tại';
                            btnGpsLocation.disabled = false;
                        })
                        .catch(err => {
                            console.error('Lỗi giải mã tọa độ:', err);
                            selectLocation('Vị trí GPS của bạn', lat, lon);
                            gpsBtnText.textContent = 'Sử dụng vị trí hiện tại';
                            btnGpsLocation.disabled = false;
                        });
                    },
                    function(error) {
                        console.error('Lỗi GPS:', error);
                        let msg = 'Không thể lấy vị trí hiện tại của bạn.';
                        if (error.code === error.PERMISSION_DENIED) {
                            msg = 'Vui lòng cho phép quyền truy cập GPS trên trình duyệt.';
                        }
                        alert(msg);
                        gpsBtnText.textContent = 'Sử dụng vị trí hiện tại';
                        btnGpsLocation.disabled = false;
                    },
                    { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                );
            });
        }

        function selectLocation(name, lat, lon) {
            inputTenDiaDiem.value = name;
            inputViDo.value = lat;
            inputKinhDo.value = lon;

            locationText.textContent = `Check-in tại: ${name}`;
            locationDisplayContainer.classList.remove('hidden');
            locationDisplayContainer.classList.add('flex');

            locationDropdown.classList.add('hidden');
            updateSubmitButton();
        }

        if (removeLocationBtn) {
            removeLocationBtn.addEventListener('click', function() {
                inputTenDiaDiem.value = '';
                inputViDo.value = '';
                inputKinhDo.value = '';

                locationDisplayContainer.classList.add('hidden');
                locationDisplayContainer.classList.remove('flex');
                updateSubmitButton();
            });
        }

        const btnPoll = document.getElementById('btn-poll');
        const pollCreatorContainer = document.getElementById('poll-creator-container');
        const closePollCreator = document.getElementById('close-poll-creator');
        const btnAddPollOption = document.getElementById('btn-add-poll-option');
        const pollOptionsList = document.getElementById('poll-options-list');
        const pollQuestionInput = document.getElementById('poll-question-input');

        function resetPollFields() {
            if (!pollQuestionInput || !pollOptionsList) return;
            pollQuestionInput.value = '';
            pollOptionsList.innerHTML = `
                <div class="option-item">
                    <input type="text" name="poll_options[]" placeholder="Lựa chọn 1" class="w-full bg-slate-900/40 border border-white/5 focus:border-sky-400/30 rounded-xl px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:ring-0 transition-colors">
                </div>
                <div class="option-item">
                    <input type="text" name="poll_options[]" placeholder="Lựa chọn 2" class="w-full bg-slate-900/40 border border-white/5 focus:border-sky-400/30 rounded-xl px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:ring-0 transition-colors">
                </div>
            `;
        }

        function bindPollInputListeners() {
            if (!pollOptionsList) return;
            pollOptionsList.querySelectorAll('input[name="poll_options[]"]').forEach(input => {
                input.addEventListener('input', updateSubmitButton);
            });
        }

        if (btnPoll && pollCreatorContainer) {
            btnPoll.addEventListener('click', function() {
                pollCreatorContainer.classList.toggle('hidden');
                if (!pollCreatorContainer.classList.contains('hidden') && pollQuestionInput) {
                    pollQuestionInput.focus();
                }
                updateSubmitButton();
            });
        }

        if (closePollCreator && pollCreatorContainer) {
            closePollCreator.addEventListener('click', function() {
                resetPollFields();
                pollCreatorContainer.classList.add('hidden');
                bindPollInputListeners();
                updateSubmitButton();
            });
        }

        if (btnAddPollOption && pollOptionsList) {
            btnAddPollOption.addEventListener('click', function() {
                const currentCount = pollOptionsList.querySelectorAll('input[name="poll_options[]"]').length;
                if (currentCount >= 6) {
                    alert('Poll tối đa 6 lựa chọn.');
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'option-item flex items-center gap-2';
                wrapper.innerHTML = `
                    <input type="text" name="poll_options[]" placeholder="Lựa chọn ${currentCount + 1}" class="w-full bg-slate-900/40 border border-white/5 focus:border-sky-400/30 rounded-xl px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:ring-0 transition-colors">
                    <button type="button" class="remove-poll-option text-slate-400 hover:text-red-400 transition-colors">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                `;

                pollOptionsList.appendChild(wrapper);
                wrapper.querySelector('input')?.addEventListener('input', updateSubmitButton);
                wrapper.querySelector('.remove-poll-option')?.addEventListener('click', function() {
                    wrapper.remove();
                    updateSubmitButton();
                });
                updateSubmitButton();
            });
        }

        if (pollQuestionInput) {
            pollQuestionInput.addEventListener('input', updateSubmitButton);
        }
        bindPollInputListeners();

        const originalUpdateSubmitButton = updateSubmitButton;
        updateSubmitButton = function() {
            const hasFeeling = inputCamXuc.value || inputHoatDong.value;
            const hasLocation = inputTenDiaDiem.value;
            const textLength = textarea.value.trim().length;
            const isPollActive = pollCreatorContainer && !pollCreatorContainer.classList.contains('hidden');
            let isPollValid = false;

            if (isPollActive) {
                const question = pollQuestionInput ? pollQuestionInput.value.trim() : '';
                const options = Array.from(pollOptionsList.querySelectorAll('input[name="poll_options[]"]'))
                    .map(input => input.value.trim())
                    .filter(Boolean);
                isPollValid = question.length > 0 && options.length >= 2 && options.length <= 6;
            }

            if (textLength > 0 || selectedFiles.length > 0 || hasFeeling || hasLocation || isPollValid) {
                submitButton.removeAttribute('disabled');
            } else {
                submitButton.setAttribute('disabled', 'true');
            }
        };

    });
</script>
@endsection