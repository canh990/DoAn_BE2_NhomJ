@props(['item'])

@php
    $mediaSrc = \Illuminate\Support\Str::startsWith($item->duong_dan, ['http://', 'https://'])
        ? $item->duong_dan
        : asset('storage/' . ltrim($item->duong_dan, '/'));
    $isVideo = $item->loai === 'video' || \Illuminate\Support\Str::endsWith($item->duong_dan, ['.mp4', '.webm', '.mov']);
    $author = $item->baiViet->user;
    $baiViet = $item->baiViet;
@endphp

<div {{ $attributes->merge(['class' => 'group relative aspect-square overflow-hidden rounded-2xl bg-slate-900 border border-white/5 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-sky-500/10 hover:border-sky-400/30']) }}>
    <!-- Main post link (covers the background) -->
    <a href="{{ route('posts.show', $item->bai_viet_id) }}" 
       onclick="event.preventDefault(); window.openPostModal('{{ $item->bai_viet_id }}')"
       class="absolute inset-0 z-0 block h-full w-full"
       title="Xem chi tiết bài viết">
        @if($isVideo)
            <video src="{{ $mediaSrc }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" muted playsinline></video>
            <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/40 transition-colors">
                <span class="material-symbols-outlined text-white text-4xl drop-shadow-md">play_circle</span>
            </div>
        @else
            <img class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" 
                 src="{{ $mediaSrc }}" 
                 alt="Media" />
        @endif
    </a>

    <!-- Interactive Overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end p-4 pointer-events-none z-10">
        <div class="flex flex-col gap-2 pointer-events-auto">
            <a href="{{ route('profile.public', $author->ten_dang_nhap ?? $author->id) }}" class="flex items-center gap-2 group/author w-fit">
                <div class="h-6 w-6 rounded-full overflow-hidden border border-white/30 group-hover/author:border-sky-400 transition-colors">
                    <img src="{{ $author->anh_dai_dien ? asset('storage/' . $author->anh_dai_dien) : 'https://ui-avatars.com/api/?name='.urlencode($author->name).'&background=random' }}" class="h-full w-full object-cover">
                </div>
                <span class="text-xs font-bold text-white truncate group-hover/author:text-sky-300 transition-colors">{{ $author->name }}</span>
            </a>
            
            <div class="flex items-center gap-4 text-white/90 text-[10px]">
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    {{ $baiViet->reactions_count ?? 0 }}
                </span>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">chat_bubble</span>
                    {{ $baiViet->comments_count ?? 0 }}
                </span>
            </div>
        </div>
    </div>
</div>
