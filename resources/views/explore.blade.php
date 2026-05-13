@extends('layouts.app')

@section('title', __('messages.explore_title'))

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
                <x-explore-card :item="$item" />
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
