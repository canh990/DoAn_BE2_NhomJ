@extends('layouts.app')
@section('title', 'Tạo nội dung - NHOMJ')

@section('content')
<div class="fixed inset-0 z-[100] bg-background flex flex-col overflow-y-auto">
    <!-- Nav -->
    <nav class="bg-[#0a0e1a]/80 backdrop-blur-xl border-b border-sky-400/10 fixed top-0 w-full z-50 flex justify-between items-center px-6 h-16">
        <div class="flex items-center gap-4">
            <a href="{{ route('home') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-sky-400/10 transition-all text-slate-400">
                <span class="material-symbols-outlined">close</span>
            </a>
            <span class="text-xl font-bold bg-gradient-to-r from-sky-400 to-purple-400 bg-clip-text text-transparent">NHOMJ</span>
        </div>
        <!-- Tab switcher -->
        <div class="flex items-center bg-slate-800/60 rounded-full p-1 gap-1">
            <button id="tab-story" onclick="switchTab('story')"
                class="tab-btn px-4 py-1.5 rounded-full text-sm font-semibold transition-all bg-sky-500 text-white">
                📖 Tin (24h)
            </button>
            <button id="tab-post" onclick="switchTab('post')"
                class="tab-btn px-4 py-1.5 rounded-full text-sm font-semibold transition-all text-slate-400 hover:text-white">
                📝 Bài viết
            </button>
        </div>
        <div class="w-32"></div>
    </nav>

    <main class="pt-20 pb-24 px-4 flex-1 flex items-start justify-center relative">
        <div class="absolute top-0 -left-20 w-80 h-80 bg-sky-400/5 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-0 -right-20 w-80 h-80 bg-purple-400/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="max-w-5xl w-full pt-6">

            {{-- ============ TAB: TIN 24H ============ --}}
            <div id="panel-story" class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <!-- Preview trái -->
                <div class="md:col-span-5 flex flex-col items-center gap-4">
                    <div id="story-canvas"
                         class="w-full max-w-[260px] rounded-2xl overflow-hidden relative shadow-2xl bg-slate-900 border border-white/10 flex items-center justify-center cursor-pointer group"
                         style="aspect-ratio:9/16; max-height:65vh;">
                        <div id="story-placeholder" class="flex flex-col items-center gap-3 text-slate-500 text-center px-6">
                            <span class="material-symbols-outlined text-5xl text-slate-600">add_photo_alternate</span>
                            <p class="text-xs">Bấm hoặc kéo thả ảnh / video</p>
                        </div>
                        <img id="preview-img" class="hidden absolute inset-0 w-full h-full object-cover" alt="">
                        <video id="preview-video" class="hidden absolute inset-0 w-full h-full object-cover" muted playsinline loop></video>
                        <div id="text-overlay" class="absolute inset-0 hidden">
                            <div id="draggable-text"
                                 class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 px-3 py-1.5 bg-black/50 backdrop-blur-sm rounded-lg text-white font-bold text-xl cursor-move select-none border border-white/20">
                                Nhấn đúp để sửa ✨
                            </div>
                        </div>
                        <!-- Progress bars -->
                        <div class="absolute top-3 left-3 right-3 flex gap-1 pointer-events-none">
                            <div class="flex-1 h-0.5 bg-white/60 rounded-full"></div>
                            <div class="flex-1 h-0.5 bg-white/20 rounded-full"></div>
                        </div>
                        <!-- Avatar -->
                        <div class="absolute bottom-3 left-3 flex items-center gap-2 pointer-events-none">
                            <img class="w-7 h-7 rounded-full border-2 border-sky-400 object-cover"
                                 src="{{ Auth::user()->avatar_url }}" alt="">
                            <span class="text-white text-[11px] font-medium drop-shadow">{{ Auth::user()->ten_dang_nhap }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tools + Form phải -->
                <div class="md:col-span-7 flex flex-col gap-4">
                    <!-- Công cụ text -->
                    <div class="glass-panel rounded-xl p-4 flex flex-col gap-4">
                        <h3 class="text-xs font-semibold text-sky-300 uppercase tracking-widest">Công cụ</h3>
                        <div class="flex flex-wrap gap-2">
                            <button id="tool-text" class="flex items-center gap-2 px-3 py-2 glass-panel rounded-lg hover:bg-sky-400/10 transition-all text-sm">
                                <span class="material-symbols-outlined text-base">text_fields</span> Văn bản
                            </button>
                        </div>
                        <div id="text-options" class="hidden flex flex-wrap gap-3">
                            <select id="text-font" class="bg-surface-container border border-outline-variant rounded-lg text-sm text-on-surface px-2 py-1.5 outline-none">
                                <option value="Inter">Inter</option>
                                <option value="Georgia">Serif</option>
                                <option value="cursive">Script</option>
                            </select>
                            <div class="flex gap-2 items-center">
                                @foreach(['#ffffff','#7dd3fc','#c084fc','#f87171','#34d399','#facc15'] as $color)
                                <div class="color-swatch w-5 h-5 rounded-full cursor-pointer border-2 border-transparent hover:border-white transition-all" style="background:{{ $color }}" data-color="{{ $color }}"></div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Form đăng story -->
                    <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data" id="story-form" class="flex flex-col gap-4">
                        @csrf
                        <input type="file" id="story-file-input" name="media" accept="image/*,video/*" class="hidden">

                        <button type="button" id="choose-media-btn"
                                class="w-full py-3 glass-panel rounded-xl hover:bg-sky-400/10 transition-all flex items-center justify-center gap-2 text-sky-300 font-medium">
                            <span class="material-symbols-outlined">photo_camera</span> Chọn ảnh / video
                        </button>

                        <div class="glass-panel rounded-xl p-4 flex flex-col gap-4">
                            <p class="text-xs font-semibold text-sky-300 uppercase tracking-widest flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">schedule</span>
                                Tin tự xóa sau 24 giờ
                            </p>
                            <div class="flex gap-3">
                                <label class="flex-1 flex items-center justify-between p-2.5 rounded-lg border border-primary/20 bg-primary/5 cursor-pointer text-sm">
                                    <span class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">public</span>Công khai</span>
                                    <input checked name="quyen_rieng_tu" type="radio" value="cong_khai" class="text-primary">
                                </label>
                                <label class="flex-1 flex items-center justify-between p-2.5 rounded-lg border border-outline-variant cursor-pointer hover:bg-white/5 transition-all text-sm">
                                    <span class="flex items-center gap-2"><span class="material-symbols-outlined text-slate-400 text-base">group</span>Bạn bè</span>
                                    <input name="quyen_rieng_tu" type="radio" value="ban_be" class="text-primary">
                                </label>
                            </div>
                            @if($errors->any())
                                <p class="text-red-400 text-sm">{{ $errors->first() }}</p>
                            @endif
                            <button type="submit" id="share-story-btn" disabled
                                class="w-full py-3 font-bold rounded-xl transition-all flex items-center justify-center gap-2 bg-sky-400/30 text-sky-300/50 cursor-not-allowed">
                                <span class="material-symbols-outlined">send</span> Chia sẻ Tin
                            </button>
                        </div>
                    </form>


                </div>
            </div>

            {{-- ============ TAB: BÀI VIẾT ============ --}}
            <div id="panel-post" class="hidden max-w-2xl mx-auto">
                <div class="glass-panel rounded-2xl p-6 flex flex-col gap-5">
                    <div class="flex items-center gap-3">
                        <img class="w-11 h-11 rounded-full border border-sky-400/20 object-cover"
                             src="{{ Auth::user()->avatar_url }}" alt="">
                        <div>
                            <p class="font-semibold text-sm text-on-surface">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-400">@{{ Auth::user()->ten_dang_nhap }}</p>
                        </div>
                    </div>

                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" id="post-form" class="flex flex-col gap-4">
                        @csrf
                        <textarea id="post-content" name="noi_dung" rows="5"
                            class="w-full bg-transparent border-none focus:ring-0 text-slate-100 placeholder-slate-500 resize-none text-base leading-relaxed p-0"
                            placeholder="Bạn đang nghĩ gì?">{{ old('noi_dung') }}</textarea>
                            
                        @error('noi_dung')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Hiển thị cảm xúc / hoạt động đang chọn -->
                        <div id="story-feeling-display" class="mt-2 hidden items-center gap-2 text-sm text-slate-300 bg-white/5 w-fit px-3 py-1.5 rounded-full border border-white/10">
                            <span class="material-symbols-outlined text-yellow-400 text-sm">mood</span>
                            <span id="story-feeling-text">Đang cảm thấy vui</span>
                            <button type="button" id="story-remove-feeling" class="hover:text-red-400 transition-colors ml-1 flex items-center">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </div>

                        <!-- Input ẩn để lưu dữ liệu -->
                        <input type="hidden" name="cam_xuc" id="story-input-cam_xuc">
                        <input type="hidden" name="hoat_dong" id="story-input-hoat_dong">

                        <!-- Preview ảnh/video bài viết -->
                        <div id="post-preview-container" class="hidden">
                            <div id="post-preview-grid" class="grid gap-2"></div>
                            <button type="button" id="post-remove-all" class="mt-1 text-xs text-red-400 hover:text-red-300 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">delete</span> Xóa tất cả
                            </button>
                        </div>

                        <input type="file" id="post-file-input" name="anh[]" accept="image/*,video/*" multiple class="hidden">

                        <div class="h-px bg-white/10"></div>

                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-1 -ml-1">
                                <button type="button" id="post-image-btn" class="p-2 text-sky-400 hover:bg-sky-400/10 rounded-full transition-colors" title="Ảnh/Video">
                                    <span class="material-symbols-outlined">image</span>
                                </button>
                                <div class="relative z-50">
                                    <button type="button" id="story-btn-feeling" class="p-2 text-yellow-400 hover:bg-yellow-400/10 rounded-full transition-colors" title="Cảm xúc/Hoạt động">
                                        <span class="material-symbols-outlined">mood</span>
                                    </button>
                                    <!-- Dropdown cảm xúc -->
                                    <div id="story-feeling-dropdown" class="hidden absolute top-full left-0 mt-2 w-48 bg-slate-800 border border-white/10 rounded-xl shadow-2xl overflow-hidden flex-col py-1 text-sm text-left">
                                        <div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Cảm xúc</div>
                                        <button type="button" class="story-feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="cam_xuc" data-val="vui_ve" data-label="Vui vẻ"><span class="text-xl">😀</span> Vui vẻ</button>
                                        <button type="button" class="story-feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="cam_xuc" data-val="phan_no" data-label="Phẫn nộ"><span class="text-xl">😡</span> Phẫn nộ</button>
                                        <button type="button" class="story-feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="cam_xuc" data-val="buon" data-label="Buồn"><span class="text-xl">😢</span> Buồn</button>
                                        <button type="button" class="story-feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="cam_xuc" data-val="wow" data-label="Wow"><span class="text-xl">😮</span> Wow</button>
                                        <div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider border-t border-white/10 mt-1">Hoạt động</div>
                                        <button type="button" class="story-feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="hoat_dong" data-val="Đang xem phim"><span class="text-xl">🎬</span> Đang xem phim</button>
                                        <button type="button" class="story-feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="hoat_dong" data-val="Đang nghe nhạc"><span class="text-xl">🎵</span> Đang nghe nhạc</button>
                                        <button type="button" class="story-feeling-option w-full text-left px-4 py-2 hover:bg-white/5 flex items-center gap-2 transition-colors" data-type="hoat_dong" data-val="Đang đi chơi"><span class="text-xl">✈️</span> Đang đi chơi</button>
                                    </div>
                                </div>
                                <button type="button" class="p-2 text-red-400 hover:bg-red-400/10 rounded-full transition-colors" title="Vị trí">
                                    <span class="material-symbols-outlined">location_on</span>
                                </button>
                            </div>
                            <div class="flex items-center gap-3">
                                <span id="post-char-count" class="text-xs font-mono text-slate-500">0/1000</span>
                                <button id="post-submit-btn" type="submit"
                                    class="bg-sky-500 text-white px-6 py-2 rounded-full font-bold hover:bg-sky-600 transition-all shadow-lg shadow-sky-500/20 disabled:opacity-40 disabled:cursor-not-allowed">
                                    Đăng
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
// ===== Tab switching =====
function switchTab(tab) {
    const isStory = tab === 'story';
    document.getElementById('panel-story').classList.toggle('hidden', !isStory);
    document.getElementById('panel-post').classList.toggle('hidden', isStory);
    document.getElementById('tab-story').className = 'tab-btn px-4 py-1.5 rounded-full text-sm font-semibold transition-all ' + (isStory ? 'bg-sky-500 text-white' : 'text-slate-400 hover:text-white');
    document.getElementById('tab-post').className = 'tab-btn px-4 py-1.5 rounded-full text-sm font-semibold transition-all ' + (!isStory ? 'bg-sky-500 text-white' : 'text-slate-400 hover:text-white');
}

document.addEventListener('DOMContentLoaded', function () {

    // ===== STORY TAB =====
    const canvas        = document.getElementById('story-canvas');
    const placeholder   = document.getElementById('story-placeholder');
    const fileInput     = document.getElementById('story-file-input');
    const chooseBtn     = document.getElementById('choose-media-btn');
    const shareBtn      = document.getElementById('share-story-btn');
    const previewImg    = document.getElementById('preview-img');
    const previewVideo  = document.getElementById('preview-video');
    const textOverlay   = document.getElementById('text-overlay');
    const draggableText = document.getElementById('draggable-text');
    const toolText      = document.getElementById('tool-text');
    const fontSelect    = document.getElementById('text-font');
    const textOptions   = document.getElementById('text-options');

    chooseBtn.addEventListener('click', () => fileInput.click());
    canvas.addEventListener('click', () => { if (!placeholder.classList.contains('hidden') === false) return; fileInput.click(); });

    fileInput.addEventListener('change', function () {
        const file = this.files[0]; if (!file) return;
        const url = URL.createObjectURL(file);
        if (file.type.startsWith('video/')) {
            previewImg.classList.add('hidden');
            previewVideo.src = url; previewVideo.classList.remove('hidden');
        } else {
            previewVideo.classList.add('hidden');
            previewImg.src = url; previewImg.classList.remove('hidden');
        }
        placeholder.classList.add('hidden');
        shareBtn.disabled = false;
        shareBtn.className = 'w-full py-3 font-bold rounded-xl transition-all flex items-center justify-center gap-2 bg-sky-400 text-[#001f2e] shadow-[0_0_20px_rgba(125,211,252,0.3)] hover:bg-sky-300 cursor-pointer';
    });

    canvas.addEventListener('dragover', e => e.preventDefault());
    canvas.addEventListener('drop', function (e) {
        e.preventDefault();
        const file = e.dataTransfer.files[0]; if (!file) return;
        const dt = new DataTransfer(); dt.items.add(file);
        fileInput.files = dt.files; fileInput.dispatchEvent(new Event('change'));
    });

    // Text tool
    if (toolText) toolText.addEventListener('click', () => {
        textOverlay.classList.toggle('hidden');
        textOptions.classList.toggle('hidden');
    });
    draggableText.addEventListener('click', e => e.stopPropagation());
    draggableText.addEventListener('dblclick', function (e) {
        e.stopPropagation();
        this.contentEditable = "true";
        this.focus();
        // Bôi đen nội dung (tùy chọn)
        document.execCommand('selectAll', false, null);
    });
    draggableText.addEventListener('blur', function () {
        this.contentEditable = "false";
        if (this.textContent.trim() === '') {
            this.textContent = 'Nhấn đúp để sửa ✨';
        }
    });
    if (fontSelect) fontSelect.addEventListener('change', () => draggableText.style.fontFamily = fontSelect.value);
    document.querySelectorAll('.color-swatch').forEach(s => s.addEventListener('click', () => draggableText.style.color = s.dataset.color));

    // Drag text
    let dragging = false, sx, sy, ox, oy;
    draggableText.addEventListener('mousedown', e => {
        e.stopPropagation();
        if (draggableText.isContentEditable) return; // Không cho phép kéo khi đang sửa chữ

        dragging = true; sx = e.clientX; sy = e.clientY;
        const r = draggableText.getBoundingClientRect(), cr = canvas.getBoundingClientRect();
        ox = r.left - cr.left; oy = r.top - cr.top;
        draggableText.style.transform = 'none';
    });
    document.addEventListener('mousemove', e => {
        if (!dragging) return;
        draggableText.style.left = (ox + e.clientX - sx) + 'px';
        draggableText.style.top  = (oy + e.clientY - sy) + 'px';
    });
    document.addEventListener('mouseup', () => dragging = false);

    // ===== POST TAB =====
    const postFileInput    = document.getElementById('post-file-input');
    const postImageBtn     = document.getElementById('post-image-btn');
    const postPreviewCont  = document.getElementById('post-preview-container');
    const postPreviewGrid  = document.getElementById('post-preview-grid');
    const postRemoveAll    = document.getElementById('post-remove-all');
    const postCharCount    = document.getElementById('post-char-count');
    const postContent      = document.getElementById('post-content');
    const postSubmitBtn    = document.getElementById('post-submit-btn');
    let selectedFiles = [];

    postImageBtn.addEventListener('click', () => postFileInput.click());

    postFileInput.addEventListener('change', async function () {
        const files = Array.from(this.files);
        const valid = [];
        for (const f of files) {
            if (f.type.startsWith('video/')) {
                const ok = await checkDuration(f, 60);
                if (!ok) { alert(`Video "${f.name}" vượt quá 1 phút.`); continue; }
            }
            valid.push(f);
        }
        selectedFiles = selectedFiles.concat(valid);
        syncFileInput(); renderPostPreviews();
    });

    function checkDuration(file, max) {
        return new Promise(res => {
            const v = document.createElement('video');
            v.preload = 'metadata';
            v.onloadedmetadata = () => { URL.revokeObjectURL(v.src); res(v.duration <= max); };
            v.onerror = () => res(false);
            v.src = URL.createObjectURL(file);
        });
    }

    function syncFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        postFileInput.files = dt.files;
    }

    function renderPostPreviews() {
        postPreviewGrid.innerHTML = '';
        if (!selectedFiles.length) { postPreviewCont.classList.add('hidden'); updatePostBtn(); return; }
        postPreviewCont.classList.remove('hidden');
        postPreviewGrid.className = 'grid gap-2 ' + (selectedFiles.length > 1 ? 'grid-cols-2 sm:grid-cols-3' : 'grid-cols-1');
        selectedFiles.forEach((file, i) => {
            const isVid = file.type.startsWith('video/');
            const url = URL.createObjectURL(file);
            const wrap = document.createElement('div');
            wrap.className = 'relative group rounded-xl overflow-hidden border border-white/10 bg-slate-900/50' + (selectedFiles.length > 1 ? ' aspect-square' : '');
            wrap.innerHTML = (isVid
                ? `<video src="${url}" class="w-full h-full ${selectedFiles.length > 1 ? 'object-cover' : 'max-h-56 object-contain'}" controls controlsList="nodownload" muted playsinline></video>`
                : `<img src="${url}" class="w-full h-full ${selectedFiles.length > 1 ? 'object-cover' : 'max-h-56 object-contain'}">`)
                + `<button type="button" class="remove-item absolute top-2 right-2 bg-slate-900/80 hover:bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-all z-10" data-i="${i}">
                    <span class="material-symbols-outlined text-sm">close</span></button>`;
            postPreviewGrid.appendChild(wrap);
        });
        updatePostBtn();
    }

    postPreviewGrid.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-item');
        if (btn) { selectedFiles.splice(parseInt(btn.dataset.i), 1); syncFileInput(); renderPostPreviews(); }
    });
    postRemoveAll.addEventListener('click', () => { selectedFiles = []; syncFileInput(); renderPostPreviews(); });

    postContent.addEventListener('input', () => {
        let text = postContent.value;
        if (text.length > 1000) {
            alert('Bài viết không được vượt quá 1000 ký tự!');
            postContent.value = text.substring(0, 1000);
            text = postContent.value;
        }
        postCharCount.textContent = text.length + '/1000';
        updatePostBtn();
    });

    function updatePostBtn() {
        const hasContent = postContent.value.trim().length > 0 || selectedFiles.length > 0 || document.getElementById('story-input-cam_xuc').value || document.getElementById('story-input-hoat_dong').value;
        postSubmitBtn.disabled = !hasContent;
    }
    updatePostBtn();

    // Feeling dropdown logic
    const storyBtnFeeling = document.getElementById('story-btn-feeling');
    const storyFeelingDropdown = document.getElementById('story-feeling-dropdown');
    const storyFeelingOptions = document.querySelectorAll('.story-feeling-option');
    const storyFeelingDisplay = document.getElementById('story-feeling-display');
    const storyFeelingText = document.getElementById('story-feeling-text');
    const storyRemoveFeelingBtn = document.getElementById('story-remove-feeling');
    const storyInputCamXuc = document.getElementById('story-input-cam_xuc');
    const storyInputHoatDong = document.getElementById('story-input-hoat_dong');

    if (storyBtnFeeling) {
        storyBtnFeeling.addEventListener('click', function(e) {
            storyFeelingDropdown.classList.toggle('hidden');
            storyFeelingDropdown.classList.toggle('flex');
        });
    }

    document.addEventListener('click', function(e) {
        if (storyBtnFeeling && storyFeelingDropdown && !storyBtnFeeling.contains(e.target) && !storyFeelingDropdown.contains(e.target)) {
            storyFeelingDropdown.classList.add('hidden');
            storyFeelingDropdown.classList.remove('flex');
        }
    });

    storyFeelingOptions.forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            const val = this.getAttribute('data-val');
            const label = this.getAttribute('data-label') || val;
            
            storyInputCamXuc.value = '';
            storyInputHoatDong.value = '';

            if (type === 'cam_xuc') {
                storyInputCamXuc.value = val;
                storyFeelingText.textContent = `Đang cảm thấy ${label.toLowerCase()}`;
            } else if (type === 'hoat_dong') {
                storyInputHoatDong.value = val;
                storyFeelingText.textContent = label;
            }

            storyFeelingDisplay.classList.remove('hidden');
            storyFeelingDisplay.classList.add('flex');
            storyFeelingDropdown.classList.add('hidden');
            storyFeelingDropdown.classList.remove('flex');
            updatePostBtn();
        });
    });

    if (storyRemoveFeelingBtn) {
        storyRemoveFeelingBtn.addEventListener('click', function() {
            storyInputCamXuc.value = '';
            storyInputHoatDong.value = '';
            storyFeelingDisplay.classList.add('hidden');
            storyFeelingDisplay.classList.remove('flex');
            updatePostBtn();
        });
    }

});
</script>
@endsection
