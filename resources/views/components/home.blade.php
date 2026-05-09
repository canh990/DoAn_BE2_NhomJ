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
    @forelse($posts as $post)
    <article class="glass-panel rounded-2xl overflow-hidden mb-6"> <!-- Thêm mb-6 để tạo khoảng cách giữa các bài -->
        
        <!-- 1. Phần Header (Avatar & Thông tin) -->
        <div class="p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img class="w-10 h-10 rounded-full border border-sky-400/20 object-cover" alt="{{ $post->user?->name ?? 'Người dùng' }}" src="{{ $post->user && $post->user->anh_dai_dien ? asset('storage/' . $post->user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="font-bold text-sm text-on-surface">{{ $post->user?->name ?? 'Người dùng' }}</h3>
                        @if ($post->cam_xuc || $post->hoat_dong)
                            @php
                            $camXucLabels = [
                                'vui_ve' => 'vui vẻ',
                                'phan_no' => 'phẫn nộ',
                                'buon' => 'buồn',
                                'wow' => 'wow',
                            ];
                            @endphp
                            <span class="text-sm text-slate-400 flex items-center gap-1">
                                @if ($post->cam_xuc)
                                    đang cảm thấy <span class="font-medium text-slate-300">{{ $camXucLabels[$post->cam_xuc] ?? strtolower($post->cam_xuc) }}</span>
                                @endif
                                @if ($post->hoat_dong)
                                    {{ strtolower($post->hoat_dong) }}
                                @endif
                            </span>
                        @endif
                    </div>
                    <p class="text-[10px] text-slate-400">{{ $post->created_at ? $post->created_at->diffForHumans() : 'Không xác định' }}</p>
                </div>
            </div>
                @if(auth()->id() === $post->nguoi_dung_id)
                    <div class="relative">
                        <button type="button" class="post-dropdown-trigger p-2 text-slate-400 hover:bg-white/5 hover:text-slate-300 rounded-full transition-colors">
                            <span class="material-symbols-outlined">more_horiz</span>
                        </button>
                        <div class="post-dropdown-menu hidden absolute right-0 top-full mt-1 w-40 bg-slate-900 border border-white/10 rounded-xl shadow-2xl overflow-hidden z-20">
                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này? Các ảnh/video đính kèm cũng sẽ bị xóa vĩnh viễn.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-400 hover:bg-white/5 flex items-center gap-2 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                    Xóa bài viết
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <button class="p-2 text-slate-400 hover:bg-white/5 hover:text-slate-300 rounded-full transition-colors">
                        <span class="material-symbols-outlined">more_horiz</span>
                    </button>
                @endif
            </div>

    <!-- 2. Phần Nội dung bài viết -->
<div class="px-4 pb-3">
    <p class="text-sm leading-relaxed text-on-surface-variant whitespace-pre-line">{{ $post->noi_dung }}</p>

    <!-- Hiển thị danh sách ảnh từ quan hệ media -->
    @if($post->media && $post->media->count() > 0)
        @php
            $mediaCount = $post->media->count();
        @endphp
        <div class="mt-3 grid gap-2 {{ $mediaCount == 1 ? 'grid-cols-1' : ($mediaCount == 2 ? 'grid-cols-2' : 'grid-cols-2 sm:grid-cols-3') }}">
            @foreach($post->media as $media)
                <div class="overflow-hidden rounded-xl border border-white/10 bg-slate-900/50 {{ $mediaCount > 1 ? 'aspect-square' : '' }}">
                    @if($media->loai === 'video' || \Illuminate\Support\Str::endsWith($media->duong_dan, ['.mp4', '.webm', '.mov']))
                        <video src="{{ asset('storage/' . $media->duong_dan) }}" 
                               controls controlsList="nodownload" muted playsinline loop
                               class="w-full h-full {{ $mediaCount == 1 ? 'max-h-[500px] object-contain block mx-auto' : 'object-cover' }}"></video>
                    @else
                        <img src="{{ asset('storage/' . $media->duong_dan) }}" 
                             alt="Post image" 
                             data-post-id="{{ $post->id }}"
                             class="post-image-item cursor-pointer hover:opacity-90 transition-opacity w-full h-full {{ $mediaCount == 1 ? 'max-h-[500px] object-contain block mx-auto' : 'object-cover' }}">
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

        
        <!-- 3. Phần Thanh tương tác (Like, Comment, Share) -->
        <div class="p-4 border-t border-white/5" data-reaction-area>
            @php
                $reactionButtons = [
                    'thich' => ['icon' => 'thumb_up', 'label' => 'Thích', 'color' => 'text-sky-400'],
                    'tim' => ['icon' => 'favorite', 'label' => 'Yêu thích', 'color' => 'text-rose-400'],
                    'haha' => ['icon' => 'mood', 'label' => 'Haha', 'color' => 'text-yellow-300'],
                    'buon' => ['icon' => 'sentiment_dissatisfied', 'label' => 'Buồn', 'color' => 'text-slate-400'],
                    'phan_no' => ['icon' => 'mood_bad', 'label' => 'Phẫn nộ', 'color' => 'text-orange-400'],
                    'wow' => ['icon' => 'emoji_objects', 'label' => 'Wow', 'color' => 'text-emerald-400'],
                ];
                $userReaction = optional($post->reactions ?? collect())->first()->loai_cam_xuc ?? null;
                $selected = $userReaction ? ($reactionButtons[$userReaction] ?? null) : null;
                $selectedIcon = $selected['icon'] ?? 'thumb_up';
                $selectedLabel = $selected['label'] ?? 'Thích';
                $selectedColor = $selected['color'] ?? 'text-sky-400';
            @endphp

            <div class="relative">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1 sm:gap-2">
                        <button type="button" data-reaction-trigger class="group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 transition-all duration-300 {{ $selected ? 'bg-sky-400/10 text-sky-400' : 'text-slate-400 hover:bg-slate-800/60 hover:text-sky-300' }}">
                            <div class="relative flex items-center justify-center transition-transform group-hover:scale-110 group-active:scale-95">
                                <span class="material-symbols-outlined text-[20px] sm:text-[22px] {{ $selectedColor }}" data-reaction-trigger-icon style="{{ $selected ? 'font-variation-settings: \'FILL\' 1;' : '' }}">{{ $selectedIcon }}</span>
                            </div>
                            <span class="text-[13px] sm:text-sm font-semibold tracking-wide" data-reaction-trigger-label>{{ $selectedLabel }}</span>
                        </button>

                        <button type="button" data-comment-toggle class="group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 text-slate-400 transition-all duration-300 hover:bg-slate-800/60 hover:text-sky-300">
                            <div class="relative flex items-center justify-center transition-transform group-hover:scale-110 group-active:scale-95">
                                <span class="material-symbols-outlined text-[20px] sm:text-[22px]" data-icon="chat_bubble_outline">chat_bubble</span>
                            </div>
                            <span class="text-[13px] sm:text-sm font-semibold tracking-wide hidden sm:block">Bình luận</span>
                            <span class="text-[13px] sm:text-sm font-bold text-slate-500 group-hover:text-sky-400/80" data-comment-count>{{ $post->comments_count > 0 ? '('.$post->comments_count.')' : '' }}</span>
                        </button>

                        <button class="group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 text-slate-400 transition-all duration-300 hover:bg-slate-800/60 hover:text-emerald-400">
                            <div class="relative flex items-center justify-center transition-transform group-hover:scale-110 group-active:scale-95">
                                <span class="material-symbols-outlined text-[20px] sm:text-[22px]" data-icon="share">share</span>
                            </div>
                            <span class="text-[13px] sm:text-sm font-semibold tracking-wide hidden sm:block">Chia sẻ</span>
                        </button>
                    </div>

                    <!-- Số lượng cảm xúc -->
                    <div class="flex items-center gap-1.5 pl-2">
                        <span class="text-[13px] sm:text-sm text-slate-400 font-medium" data-reaction-count>{{ $post->reactions_count ?? 0 }} cảm xúc</span>
                    </div>
                </div>

                <!-- ... Phần ẩn chọn cảm xúc & bình luận giữ nguyên ... -->
                <div data-reaction-picker class="hidden absolute left-0 bottom-full z-10 mb-2 w-auto rounded-[32px] border border-white/10 bg-slate-950/95 p-3 shadow-[0_12px_35px_rgba(0,0,0,0.25)] backdrop-blur-sm transition-all duration-200">
                    <div class="flex items-center gap-2">
                        @foreach($reactionButtons as $type => $button)
                            <button type="button" data-reaction-option data-reaction="{{ $type }}" data-reaction-label="{{ $button['label'] }}" data-reaction-color="{{ $button['color'] }}" data-reaction-icon="{{ $button['icon'] }}" class="flex flex-col items-center justify-center rounded-3xl bg-slate-900 px-3 py-2 text-center text-slate-300 transition duration-200 hover:-translate-y-1 hover:bg-sky-400/10 hover:text-sky-300">
                                <span class="material-symbols-outlined {{ $button['color'] }} text-xl">{{ $button['icon'] }}</span>
                                <span class="text-[10px]">{{ $button['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <form class="reaction-submit-form hidden" method="POST" action="{{ route('posts.react', $post) }}">
                @csrf
                <input type="hidden" name="loai_cam_xuc" value="">
            </form>

            <div data-comment-box class="hidden mt-3 rounded-3xl border border-white/10 bg-slate-950/80 p-3">
                <form class="comment-submit-form" method="POST" action="{{ route('posts.comment', $post) }}">
                    @csrf
                    <input type="hidden" name="binh_luan_cha_id" value="">
                    <textarea name="noi_dung" rows="2" required class="w-full bg-transparent border border-white/10 focus:border-sky-400 focus:ring-0 rounded-3xl p-3 text-sm text-slate-100 placeholder:text-slate-500" placeholder="Viết bình luận..."></textarea>
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <span class="text-xs text-slate-500" data-comment-action>Viết bình luận mới</span>
                        <button type="button" data-comment-cancel class="hidden text-xs text-slate-400 hover:text-white">Hủy trả lời</button>
                        <button type="submit" class="rounded-full bg-sky-400/10 text-sky-300 px-4 py-2 text-sm font-semibold hover:bg-sky-400/20">Gửi</button>
                    </div>
                </form>

                <div data-comment-list class="mt-4 space-y-3 text-slate-300">
                    @php
                        $rootComments = $post->comments->whereNull('binh_luan_cha_id');
                    @endphp
                    @if($post->comments->isEmpty())
                        <div data-no-comments class="text-sm text-slate-500">Chưa có bình luận nào. Hãy là người đầu tiên bình luận.</div>
                    @else
                        @foreach($rootComments as $comment)
                            <div class="rounded-2xl border border-white/10 bg-slate-950 p-3" data-comment-id="{{ $comment->id }}">
                                <div class="flex gap-3 items-start">
                                    <img class="w-8 h-8 rounded-full object-cover border border-slate-700" src="{{ $comment->user && $comment->user->anh_dai_dien ? asset('storage/' . $comment->user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}" alt="{{ $comment->user?->name ?? 'Người dùng' }}">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between gap-2 text-sm text-slate-200">
                                            <span class="font-semibold">{{ $comment->user?->name ?? 'Người dùng' }}</span>
                                            <span class="text-xs text-slate-500">{{ $comment->ngay_tao?->diffForHumans() ?? '' }}</span>
                                        </div>
                                        <p class="mt-1 text-sm leading-relaxed text-slate-300">{{ $comment->noi_dung }}</p>
                                        <div class="mt-3 flex items-center gap-3 text-xs text-slate-400">
                                            <button type="button" data-comment-reply-button data-comment-id="{{ $comment->id }}" data-comment-user="{{ $comment->user?->name ?? 'Người dùng' }}" class="hover:text-sky-300">Trả lời</button>
                                        </div>
                                        <div class="mt-3 space-y-3 pl-10" data-comment-replies>
                                            @foreach($comment->children as $reply)
                                                <div class="rounded-2xl border border-white/10 bg-slate-950 p-3" data-comment-id="{{ $reply->id }}">
                                                    <div class="flex gap-3 items-start">
                                                        <img class="w-8 h-8 rounded-full object-cover border border-slate-700" src="{{ $reply->user && $reply->user->anh_dai_dien ? asset('storage/' . $reply->user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}" alt="{{ $reply->user?->name ?? 'Người dùng' }}">
                                                        <div class="flex-1">
                                                            <div class="flex items-center justify-between gap-2 text-sm text-slate-200">
                                                                <span class="font-semibold">{{ $reply->user?->name ?? 'Người dùng' }}</span>
                                                                <span class="text-xs text-slate-500">{{ $reply->ngay_tao?->diffForHumans() ?? '' }}</span>
                                                            </div>
                                                            <p class="mt-1 text-sm leading-relaxed text-slate-300">{{ $reply->noi_dung }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </article>
    @empty
    <div class="glass-panel rounded-2xl p-6 text-center text-slate-300">
        <p class="text-sm">Chưa có bài viết nào. Hãy là người đầu tiên đăng trạng thái!</p>
    </div>
    @endforelse
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