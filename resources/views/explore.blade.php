@extends('layouts.app')

@section('title', __('messages.explore_title', 'Khám phá'))

@section('content')
<div class="max-w-6xl mx-auto p-4 md:p-8 pb-24">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-on-surface">Khám phá</h1>
            <p class="text-slate-400 mt-1">Khám phá những khoảnh khắc mới nhất từ cộng đồng NHOMJ</p>
        </div>
        <div class="hidden sm:flex items-center gap-2">
            <button class="p-2 bg-sky-400/10 text-sky-400 rounded-lg border border-sky-400/20">
                <span class="material-symbols-outlined">grid_view</span>
            </button>
            <button class="p-2 text-slate-500 hover:bg-white/5 rounded-lg transition-colors">
                <span class="material-symbols-outlined">list</span>
            </button>
        </div>
    </div>

    @if($media->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($media as $item)
                @php
                    $mediaSrc = \Illuminate\Support\Str::startsWith($item->duong_dan, ['http://', 'https://'])
                        ? $item->duong_dan
                        : asset('storage/' . ltrim($item->duong_dan, '/'));
                    $isVideo = $item->loai === 'video' || \Illuminate\Support\Str::endsWith($item->duong_dan, ['.mp4', '.webm', '.mov']);
                    $author = $item->baiViet->user;
                @endphp
                <div class="group relative aspect-square overflow-hidden rounded-2xl bg-slate-900 border border-white/5 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-sky-500/10 hover:border-sky-400/30">
                    <a href="{{ route('posts.show', $item->bai_viet_id) }}" class="block h-full w-full">
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

                    <!-- Overlay thông tin khi hover -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end p-4 pointer-events-none">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="h-6 w-6 rounded-full overflow-hidden border border-white/30">
                                <img src="{{ $author->anh_dai_dien ? asset('storage/' . $author->anh_dai_dien) : 'https://ui-avatars.com/api/?name='.urlencode($author->name).'&background=random' }}" class="h-full w-full object-cover">
                            </div>
                            <span class="text-xs font-bold text-white truncate">{{ $author->name }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-white/90 text-[10px]">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">favorite</span>
                                {{ $item->baiViet->reactions_count ?? 0 }}
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">chat_bubble</span>
                                {{ $item->baiViet->comments_count ?? 0 }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $media->links() }}
        </div>
    @else
        <div class="glass-panel flex flex-col items-center justify-center rounded-3xl p-20 text-center">
            <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-slate-800/50 text-slate-500 border border-dashed border-slate-700">
                <span class="material-symbols-outlined text-5xl">auto_awesome_motion</span>
            </div>
            <h3 class="text-2xl font-bold text-on-surface">Chưa có phương tiện nào</h3>
            <p class="mt-2 text-slate-400 max-w-md mx-auto">Cộng đồng vẫn đang chuẩn bị những nội dung tuyệt vời. Hãy là người đầu tiên chia sẻ khoảnh khắc của bạn!</p>
            <a href="{{ route('home') }}" class="mt-8 px-8 py-3 bg-sky-500 text-white font-bold rounded-xl hover:bg-sky-600 transition-all shadow-lg shadow-sky-500/20">
                Đăng bài ngay
            </a>
        </div>
    @endif
</div>
@endsection
