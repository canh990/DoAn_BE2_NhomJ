{{--
    Component: Stories Bar
    Cách dùng: @include('components.stories-bar', ['stories' => $stories])
    $stories: Collection<Tin24h> (đã eager-load 'user')
--}}
<section class="glass-panel rounded-2xl p-4 shadow-sm">
    <div class="flex items-center gap-3 overflow-x-auto pb-1 scrollbar-hide">

        {{-- Ô "Thêm tin" của chính mình --}}
        <a href="{{ route('stories.create') }}"
           class="shrink-0 flex flex-col items-center gap-1.5 group">
            <div class="relative w-16 h-24 rounded-2xl overflow-hidden bg-slate-800 border-2 border-sky-400/30 group-hover:border-sky-400 transition-colors shadow-lg">
                {{-- Ảnh nền là avatar --}}
                <img class="w-full h-full object-cover opacity-60"
                     src="{{ Auth::user()->anh_dai_dien ? asset('storage/' . Auth::user()->anh_dai_dien) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}"
                     alt="Thêm tin">
                {{-- Nút + --}}
                <div class="absolute bottom-2 left-1/2 -translate-x-1/2 w-7 h-7 rounded-full bg-sky-400 flex items-center justify-center border-2 border-[#0f1524] shadow">
                    <span class="material-symbols-outlined text-[#001f2e] text-base font-bold">add</span>
                </div>
            </div>
            <span class="text-[10px] text-slate-400 text-center w-16 truncate group-hover:text-sky-300 transition-colors">Thêm tin</span>
        </a>

        {{-- Danh sách story của bạn bè / tất cả --}}
        @forelse($stories as $story)
            @php
                $isVideo = $story->loai_media === 'video';
                $mediaSrc = asset('storage/' . $story->duong_dan_media);
                $isOwn = $story->nguoi_dung_id === auth()->id();
                $avatarSrc = $story->user?->anh_dai_dien
                    ? asset('storage/' . $story->user->anh_dai_dien)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($story->user?->name ?? 'Người dùng') . '&background=random';
            @endphp

            <div class="shrink-0 flex flex-col items-center gap-1.5 group relative" data-story-id="{{ $story->id }}">
                <button type="button"
                        class="story-thumb relative w-16 h-24 rounded-2xl overflow-hidden border-2 border-sky-400 hover:border-sky-300 transition-colors shadow-lg"
                        data-src="{{ $mediaSrc }}"
                        data-type="{{ $isVideo ? 'video' : 'image' }}"
                        data-username="{{ $story->user?->ten_dang_nhap ?? 'Người dùng' }}"
                        data-avatar="{{ $avatarSrc }}">
                    {{-- Thumbnail --}}
                    @if($isVideo)
                        <video src="{{ $mediaSrc }}" class="w-full h-full object-cover" muted playsinline preload="metadata"></video>
                    @else
                        <img src="{{ $mediaSrc }}" class="w-full h-full object-cover" alt="Story">
                    @endif

                    {{-- Gradient overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/10 pointer-events-none"></div>

                    {{-- Avatar nhỏ góc trên --}}
                    <img class="absolute top-2 left-1/2 -translate-x-1/2 w-7 h-7 rounded-full border-2 border-sky-400 object-cover shadow"
                         src="{{ $avatarSrc }}" alt="">
                </button>

                <span class="text-[10px] text-slate-400 text-center w-16 truncate group-hover:text-sky-300 transition-colors">
                    {{ $story->user?->ten_dang_nhap ?? 'Người dùng' }}
                </span>

                {{-- Nút xóa (chỉ hiện với chủ story) --}}
                @if($isOwn)
                    <form action="{{ route('stories.destroy', $story->id) }}" method="POST"
                          class="absolute top-0 right-0"
                          onsubmit="return confirm('Xóa tin này?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-5 h-5 bg-red-500 rounded-full text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow"
                                title="Xóa tin">
                            <span class="material-symbols-outlined text-[11px]">close</span>
                        </button>
                    </form>
                @endif
            </div>
        @empty
            {{-- Không có story nào ngoài ô Thêm tin --}}
        @endforelse
    </div>
</section>

{{-- ===== MODAL XEM STORY ===== --}}
<div id="story-viewer" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/90 backdrop-blur-sm">
    <button id="story-viewer-close" class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
        <span class="material-symbols-outlined">close</span>
    </button>

    <div class="relative w-full max-w-sm" style="aspect-ratio:9/16; max-height:90vh;">
        {{-- Progress bar --}}
        <div class="absolute top-0 left-0 right-0 px-3 pt-3 z-10">
            <div class="h-0.5 bg-white/20 rounded-full w-full overflow-hidden">
                <div id="story-progress-bar" class="h-full bg-white rounded-full transition-none" style="width:0%"></div>
            </div>
        </div>

        {{-- User info --}}
        <div class="absolute top-5 left-3 right-3 z-10 flex items-center gap-2 mt-2">
            <img id="story-viewer-avatar" class="w-8 h-8 rounded-full border-2 border-sky-400 object-cover" src="" alt="">
            <span id="story-viewer-username" class="text-white text-xs font-semibold drop-shadow-md"></span>
        </div>

        {{-- Media --}}
        <img id="story-viewer-img" class="absolute inset-0 w-full h-full object-contain rounded-2xl hidden" alt="">
        <video id="story-viewer-video" class="absolute inset-0 w-full h-full object-contain rounded-2xl hidden" playsinline controls></video>
    </div>
</div>

<script>
(function () {
    const thumbs   = document.querySelectorAll('.story-thumb');
    const viewer   = document.getElementById('story-viewer');
    const closeBtn = document.getElementById('story-viewer-close');
    const vImg     = document.getElementById('story-viewer-img');
    const vVideo   = document.getElementById('story-viewer-video');
    const vAvatar  = document.getElementById('story-viewer-avatar');
    const vUser    = document.getElementById('story-viewer-username');
    const progress = document.getElementById('story-progress-bar');

    let timer = null;

    function openStory(btn) {
        const src      = btn.getAttribute('data-src');
        const type     = btn.getAttribute('data-type');
        const username = btn.getAttribute('data-username');
        const avatar   = btn.getAttribute('data-avatar');

        vAvatar.src = avatar;
        vUser.textContent = '@' + username;
        progress.style.width = '0%';
        progress.style.transition = 'none';

        if (type === 'video') {
            vImg.classList.add('hidden');
            vVideo.src = src;
            vVideo.classList.remove('hidden');
            vVideo.play();
            startProgress(vVideo.duration || 15);
            vVideo.onloadedmetadata = () => startProgress(vVideo.duration);
        } else {
            vVideo.pause(); vVideo.src = '';
            vVideo.classList.add('hidden');
            vImg.src = src;
            vImg.classList.remove('hidden');
            startProgress(5);
        }

        viewer.classList.remove('hidden');
        viewer.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeStory() {
        viewer.classList.add('hidden');
        viewer.classList.remove('flex');
        vVideo.pause(); vVideo.src = '';
        clearTimeout(timer);
        document.body.style.overflow = '';
        progress.style.width = '0%';
    }

    function startProgress(duration) {
        clearTimeout(timer);
        requestAnimationFrame(() => {
            progress.style.transition = `width ${duration}s linear`;
            progress.style.width = '100%';
        });
        timer = setTimeout(closeStory, duration * 1000);
    }

    thumbs.forEach(btn => btn.addEventListener('click', () => openStory(btn)));
    closeBtn.addEventListener('click', closeStory);
    viewer.addEventListener('click', e => { if (e.target === viewer) closeStory(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeStory(); });
})();
</script>
