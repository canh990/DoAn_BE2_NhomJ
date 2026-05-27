{{--
    Component: Stories Bar
    Cách dùng: @include('components.stories-bar', ['stories' => $stories])
    $stories: Collection<Tin24h> (đã eager-load 'user')
--}}
<section class="glass-panel rounded-2xl p-4 shadow-sm">
    <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide">

        {{-- Ô "Thêm tin" của chính mình --}}
        <a href="{{ route('stories.create') }}"
           class="shrink-0 relative w-24 h-40 md:w-28 md:h-44 rounded-2xl overflow-hidden bg-slate-800 border border-white/10 shadow-lg group hover:scale-[1.03] active:scale-[0.98] transition-all duration-300">
            {{-- Top half is avatar --}}
            <div class="h-[70%] w-full overflow-hidden relative">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80"
                     src="{{ Auth::user()->avatar_url }}"
                     alt="Thêm tin">
            </div>
            {{-- Bottom half is card label and button --}}
            <div class="h-[30%] bg-[#121824] flex flex-col items-center justify-end pb-3 relative">
                {{-- Circle + Button floating on the boundary --}}
                <div class="absolute -top-4 w-8 h-8 rounded-full bg-sky-500 flex items-center justify-center border-4 border-[#0f1524] shadow-md group-hover:bg-sky-400 transition-colors">
                    <span class="material-symbols-outlined text-white text-sm font-bold">add</span>
                </div>
                <span class="text-[11px] font-bold text-slate-300">Tạo tin</span>
            </div>
        </a>

        {{-- Danh sách story của bạn bè / tất cả --}}
        @forelse($stories as $story)
            @php
                $isVideo = $story->loai_media === 'video';
                $mediaSrc = \Illuminate\Support\Str::startsWith($story->duong_dan_media, ['http://', 'https://'])
                    ? $story->duong_dan_media
                    : asset('storage/' . $story->duong_dan_media);
                $isOwn = $story->nguoi_dung_id === auth()->id();
                $avatarSrc = $story->user ? $story->user->avatar_url : 'https://ui-avatars.com/api/?name=NguoiDung&background=random';
            @endphp

            <div class="shrink-0 relative w-24 h-40 md:w-28 md:h-44 rounded-2xl overflow-hidden bg-slate-800 border border-white/10 shadow-lg group hover:scale-[1.03] active:scale-[0.98] transition-all duration-300" data-story-id="{{ $story->id }}">
                <button type="button"
                        class="story-thumb w-full h-full text-left relative overflow-hidden"
                        data-src="{{ $mediaSrc }}"
                        data-type="{{ $isVideo ? 'video' : 'image' }}"
                        data-username="{{ $story->user?->ten_dang_nhap ?? 'Người dùng' }}"
                        data-avatar="{{ $avatarSrc }}">
                    {{-- Thumbnail --}}
                    @if($isVideo)
                        <video src="{{ $mediaSrc }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" muted playsinline preload="metadata"></video>
                    @else
                        <img src="{{ $mediaSrc }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Story">
                    @endif

                    {{-- Gradient overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-black/40 pointer-events-none"></div>

                    {{-- Avatar top left --}}
                    <div class="absolute top-2.5 left-2.5 z-10 w-9 h-9 rounded-full p-[2px] bg-[#001f2e] border-2 border-sky-400 shadow-md">
                        <img class="w-full h-full rounded-full object-cover" src="{{ $avatarSrc }}" alt="">
                    </div>

                    {{-- Username bottom left --}}
                    <span class="absolute bottom-2.5 left-2.5 right-2.5 text-[11px] font-bold text-white drop-shadow truncate">
                        {{ $story->user?->ten_dang_nhap ?? 'Người dùng' }}
                    </span>
                </button>

                {{-- Nút xóa (chỉ hiện với chủ story) --}}
                @if($isOwn)
                    <form action="{{ route('stories.destroy', $story->id) }}" method="POST"
                          class="absolute top-2.5 right-2.5 z-20"
                          onsubmit="return confirm('Xóa tin này?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-6 h-6 bg-black/60 hover:bg-red-500 rounded-full text-white flex items-center justify-center transition-colors shadow-lg"
                                title="Xóa tin">
                            <span class="material-symbols-outlined text-[12px]">close</span>
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
<div id="story-viewer" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/95 backdrop-blur-md">
    {{-- Nút đóng modal góc trên phải --}}
    <button id="story-viewer-close" class="absolute top-4 right-4 z-30 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all hover:scale-105 active:scale-95">
        <span class="material-symbols-outlined text-2xl">close</span>
    </button>

    <div class="relative w-full max-w-sm" style="aspect-ratio:9/16; max-height:90vh;">
        {{-- Progress bar --}}
        <div class="absolute top-4 left-3 right-3 z-20">
            <div class="h-1 bg-white/20 rounded-full w-full overflow-hidden shadow-sm">
                <div id="story-progress-bar" class="h-full bg-sky-400 rounded-full transition-none" style="width:0%"></div>
            </div>
        </div>

        {{-- User info --}}
        <div class="absolute top-7 left-4 right-4 z-20 flex items-center gap-3">
            <img id="story-viewer-avatar" class="w-9 h-9 rounded-full border-2 border-sky-400 object-cover shadow" src="" alt="">
            <div class="flex flex-col">
                <span id="story-viewer-username" class="text-white text-xs font-bold drop-shadow-md"></span>
                <span class="text-white/60 text-[9px] drop-shadow-md font-medium tracking-wide">Tin 24h</span>
            </div>
        </div>

        {{-- Transparent navigation touch-zones --}}
        <div id="story-tap-left" class="absolute left-0 top-0 bottom-0 w-1/3 z-10 cursor-pointer" title="Tin trước"></div>
        <div id="story-tap-right" class="absolute right-0 top-0 bottom-0 w-2/3 z-10 cursor-pointer" title="Tin tiếp theo"></div>

        {{-- Media --}}
        <img id="story-viewer-img" class="absolute inset-0 w-full h-full object-contain rounded-2xl hidden bg-[#090b10] border border-white/5 shadow-2xl" alt="">
        <video id="story-viewer-video" class="absolute inset-0 w-full h-full object-contain rounded-2xl hidden bg-[#090b10] border border-white/5 shadow-2xl" playsinline controls></video>

        <!-- Navigation Buttons (Desktop only, floats outside the container) -->
        <button id="story-viewer-prev" class="absolute -left-16 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 hover:scale-105 active:scale-95 text-white hidden md:flex items-center justify-center transition-all z-20 shadow-lg border border-white/5" title="Tin trước">
            <span class="material-symbols-outlined text-3xl">chevron_left</span>
        </button>
        <button id="story-viewer-next" class="absolute -right-16 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 hover:scale-105 active:scale-95 text-white hidden md:flex items-center justify-center transition-all z-20 shadow-lg border border-white/5" title="Tin tiếp theo">
            <span class="material-symbols-outlined text-3xl">chevron_right</span>
        </button>
    </div>
</div>

<script>
(function () {
    const thumbs   = Array.from(document.querySelectorAll('.story-thumb'));
    const viewer   = document.getElementById('story-viewer');
    const closeBtn = document.getElementById('story-viewer-close');
    const prevBtn  = document.getElementById('story-viewer-prev');
    const nextBtn  = document.getElementById('story-viewer-next');
    const tapLeft  = document.getElementById('story-tap-left');
    const tapRight = document.getElementById('story-tap-right');
    const vImg     = document.getElementById('story-viewer-img');
    const vVideo   = document.getElementById('story-viewer-video');
    const vAvatar  = document.getElementById('story-viewer-avatar');
    const vUser    = document.getElementById('story-viewer-username');
    const progress = document.getElementById('story-progress-bar');

    let timer = null;
    let currentIndex = -1;

    function openStory(btn) {
        if (!btn) return;
        currentIndex = thumbs.indexOf(btn);
        updateNavigationButtons();

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

    function openStoryByIndex(index) {
        if (index >= 0 && index < thumbs.length) {
            openStory(thumbs[index]);
        }
    }

    function updateNavigationButtons() {
        if (!prevBtn || !nextBtn) return;
        if (currentIndex <= 0) {
            prevBtn.classList.add('invisible');
        } else {
            prevBtn.classList.remove('invisible');
        }

        if (currentIndex >= thumbs.length - 1) {
            nextBtn.classList.add('invisible');
        } else {
            nextBtn.classList.remove('invisible');
        }
    }

    function closeStory() {
        viewer.classList.add('hidden');
        viewer.classList.remove('flex');
        vVideo.pause(); vVideo.src = '';
        clearTimeout(timer);
        document.body.style.overflow = '';
        progress.style.width = '0%';
        currentIndex = -1;
    }

    function handleStoryAutoNext() {
        if (currentIndex < thumbs.length - 1) {
            openStoryByIndex(currentIndex + 1);
        } else {
            closeStory();
        }
    }

    function startProgress(duration) {
        clearTimeout(timer);
        requestAnimationFrame(() => {
            progress.style.transition = `width ${duration}s linear`;
            progress.style.width = '100%';
        });
        timer = setTimeout(handleStoryAutoNext, duration * 1000);
    }

    thumbs.forEach(btn => btn.addEventListener('click', () => openStory(btn)));
    closeBtn.addEventListener('click', closeStory);

    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (currentIndex > 0) openStoryByIndex(currentIndex - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (currentIndex < thumbs.length - 1) openStoryByIndex(currentIndex + 1);
        });
    }

    if (tapLeft) {
        tapLeft.addEventListener('click', (e) => {
            e.stopPropagation();
            if (currentIndex > 0) openStoryByIndex(currentIndex - 1);
        });
    }

    if (tapRight) {
        tapRight.addEventListener('click', (e) => {
            e.stopPropagation();
            if (currentIndex < thumbs.length - 1) {
                openStoryByIndex(currentIndex + 1);
            } else {
                closeStory();
            }
        });
    }

    viewer.addEventListener('click', e => { if (e.target === viewer) closeStory(); });
    
    document.addEventListener('keydown', e => { 
        if (viewer.classList.contains('hidden')) return;
        if (e.key === 'Escape') closeStory(); 
        if (e.key === 'ArrowLeft' && currentIndex > 0) openStoryByIndex(currentIndex - 1);
        if (e.key === 'ArrowRight' && currentIndex < thumbs.length - 1) openStoryByIndex(currentIndex + 1);
    });
})();
</script>
