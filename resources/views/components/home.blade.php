@extends('layouts.app')

@section('title', 'Bảng tin')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="glass-panel rounded-2xl p-4 border border-emerald-400/20 bg-emerald-500/10 text-emerald-200">
        {{ session('success') }}
    </div>
    @endif

    <!-- ===== FORM ĐĂNG BÀI ===== -->
    <section class="glass-panel rounded-2xl p-4 shadow-sm relative z-40">
        <div class="flex gap-4">
            <img class="w-12 h-12 rounded-full border border-sky-400/20 shrink-0 object-cover" alt="Avatar" src="{{ Auth::user()->anh_dai_dien ? asset('storage/' . Auth::user()->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}">
            <div class="w-full">
                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <textarea id="post-content" name="noi_dung" maxlength="280" class="w-full bg-transparent border-none focus:ring-0 text-slate-100 placeholder-slate-500 resize-none text-lg leading-relaxed p-0 min-h-[120px]" placeholder="Bạn đang nghĩ gì?" rows="4">{{ old('noi_dung') }}</textarea>
                    
                    @error('noi_dung')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror

                    <div id="feeling-display-container" class="mt-2 hidden items-center gap-2 text-sm text-slate-300 bg-white/5 w-fit px-3 py-1.5 rounded-full border border-white/10">
                        <span class="material-symbols-outlined text-yellow-400 text-sm">mood</span>
                        <span id="feeling-text">Đang cảm thấy vui</span>
                        <button type="button" id="remove-feeling-btn" class="hover:text-red-400 transition-colors ml-1 flex items-center">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>

                    <input type="hidden" name="cam_xuc" id="input-cam_xuc">
                    <input type="hidden" name="hoat_dong" id="input-hoat_dong">
                    <input type="file" id="post-image" name="anh[]" accept="image/*,video/*" multiple class="hidden">
                    
                    <div id="image-preview-container" class="mt-3 hidden">
                        <div id="preview-grid" class="grid gap-2"></div>
                        <button type="button" id="remove-all-images" class="mt-2 text-sm text-red-400 hover:text-red-300 hidden items-center gap-1">
                            <span class="material-symbols-outlined text-sm">delete</span> Xóa tất cả tệp
                        </button>
                    </div>

                    <div class="h-px bg-white/10 my-4"></div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
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

                        <div class="flex items-center justify-end gap-4 border-t border-white/5 pt-3 sm:border-t-0 sm:pt-0">
                            <span id="post-char-count" class="text-xs font-mono text-slate-500">0/280</span>
                            <button id="post-submit-button" type="submit" class="bg-sky-500 text-white px-8 py-2 rounded-full font-bold hover:bg-sky-600 transition-all shadow-lg shadow-sky-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                Đăng
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
            <x-post-card :post="$post" />
        @empty
            <div class="glass-panel rounded-2xl p-6 text-center text-slate-300">
                <p class="text-sm">Chưa có bài viết nào. Hãy là người đầu tiên đăng trạng thái!</p>
            </div>
        @endforelse
    </div>
</div>

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

        if (!textarea || !counter || !submitButton) return;

        const maxLength = Number(textarea.getAttribute('maxlength')) || 280;

        const updateCount = function() {
            const length = textarea.value.length;
            counter.textContent = `${length}/${maxLength}`;
            updateSubmitButton();
        };

        const updateSubmitButton = function() {
            const hasText = textarea.value.trim().length > 0;
            const hasImage = selectedFiles.length > 0;
            const hasFeeling = document.getElementById('input-cam_xuc').value || document.getElementById('input-hoat_dong').value;
            submitButton.disabled = !hasText && !hasImage && !hasFeeling;
        };

        textarea.addEventListener('input', updateCount);

        if(imageBtn) imageBtn.addEventListener('click', () => imageInput.click());

        if(imageInput) {
            imageInput.addEventListener('change', async function(e) {
                const files = Array.from(e.target.files);
                selectedFiles = selectedFiles.concat(files);
                updateFileInput();
                renderPreviews();
                updateSubmitButton();
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
                return;
            }
            imagePreviewContainer.classList.remove('hidden');
            previewGrid.className = 'grid gap-2 ' + (selectedFiles.length > 1 ? 'grid-cols-2 sm:grid-cols-3' : 'grid-cols-1');
            selectedFiles.forEach((file, index) => {
                const objectUrl = URL.createObjectURL(file);
                const div = document.createElement('div');
                div.className = 'relative group rounded-xl overflow-hidden border border-white/10 bg-slate-900/50 ' + (selectedFiles.length > 1 ? 'aspect-square' : '');
                div.innerHTML = `
                    <img src="${objectUrl}" class="w-full h-full object-cover">
                    <button type="button" class="remove-single-image absolute top-2 right-2 bg-slate-900/80 hover:bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity" data-index="${index}">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                `;
                previewGrid.appendChild(div);
            });
        }

        if (previewGrid) {
            previewGrid.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-single-image');
                if (btn) {
                    selectedFiles.splice(btn.dataset.index, 1);
                    updateFileInput();
                    renderPreviews();
                    updateSubmitButton();
                }
            });
        }

        // Feeling logic simplified
        const btnFeeling = document.getElementById('btn-feeling');
        const feelingDropdown = document.getElementById('feeling-dropdown');
        if (btnFeeling) {
            btnFeeling.addEventListener('click', () => feelingDropdown.classList.toggle('hidden'));
        }
        
        document.querySelectorAll('.feeling-option').forEach(btn => {
            btn.addEventListener('click', function() {
                const type = this.dataset.type;
                const val = this.dataset.val;
                const label = this.dataset.label;
                
                document.getElementById('input-cam_xuc').value = (type === 'cam_xuc' ? val : '');
                document.getElementById('input-hoat_dong').value = (type === 'hoat_dong' ? val : '');
                
                const display = document.getElementById('feeling-display-container');
                document.getElementById('feeling-text').textContent = (type === 'cam_xuc' ? `Đang cảm thấy ${label.toLowerCase()}` : label);
                display.classList.remove('hidden');
                display.classList.add('flex');
                feelingDropdown.classList.add('hidden');
                updateSubmitButton();
            });
        });

        document.getElementById('remove-feeling-btn')?.addEventListener('click', () => {
            document.getElementById('input-cam_xuc').value = '';
            document.getElementById('input-hoat_dong').value = '';
            document.getElementById('feeling-display-container').classList.add('hidden');
            updateSubmitButton();
        });
    });
</script>
@endsection