@foreach($savedPosts as $saved)
    @if($saved->post)
        @php
            $firstMedia = $saved->post->media->first();
            $mediaUrl = $firstMedia ? (\Illuminate\Support\Str::startsWith($firstMedia->duong_dan, ['http://', 'https://']) ? $firstMedia->duong_dan : asset('storage/' . ltrim($firstMedia->duong_dan, '/'))) : null;
            $isVideo = $firstMedia && ($firstMedia->loai === 'video' || \Illuminate\Support\Str::endsWith($firstMedia->duong_dan, ['.mp4', '.webm', '.mov']));
        @endphp
        <a href="{{ route('posts.show', $saved->bai_viet_id) }}" class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/5 transition-all group">
            <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">bookmark</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">Bài viết đã lưu của {{ $saved->post->user->name ?? 'Người dùng' }}</p>
                <p class="text-xs text-slate-400 mt-1 line-clamp-1 italic">"{{ $saved->post->noi_dung }}"</p>
                <span class="text-[10px] text-slate-500 block mt-1">
                    {{ $saved->ngay_tao ? \Carbon\Carbon::parse($saved->ngay_tao, 'Asia/Ho_Chi_Minh')->diffForHumans() : '' }}
                </span>
            </div>
            @if($firstMedia)
                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 border border-white/10">
                    @if($isVideo)
                        <video src="{{ $mediaUrl }}" class="w-full h-full object-cover" muted></video>
                    @else
                        <img src="{{ $mediaUrl }}" class="w-full h-full object-cover">
                    @endif
                </div>
            @endif
        </a>
    @endif
@endforeach
