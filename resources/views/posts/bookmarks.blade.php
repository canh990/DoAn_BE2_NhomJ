@extends('layouts.app')

@section('title', 'Bài viết đã lưu')

@section('content')
<div class="max-w-4xl mx-auto p-4 md:p-6 pb-24">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-on-surface flex items-center gap-3">
                <span class="material-symbols-outlined text-yellow-400 text-3xl" style="font-variation-settings: 'FILL' 1;">bookmark</span>
                Bài viết đã lưu
            </h1>
            <p class="text-slate-400 mt-1">Quản lý và xem lại những bài viết bạn đã lưu trữ</p>
        </div>
        <div class="text-sm text-slate-500 bg-white/5 border border-white/10 rounded-full px-4 py-1.5 font-medium">
            Tổng cộng: {{ $posts->total() }} bài viết
        </div>
    </div>

    @if($posts->count() > 0)
        <div class="space-y-6">
            @foreach($posts as $post)
                <x-post-card :post="$post" class="mb-6" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @else
        <div class="glass-panel flex flex-col items-center justify-center rounded-3xl p-16 text-center shadow-lg border border-white/5 bg-slate-900/40 backdrop-blur-md">
            <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-slate-800/50 text-slate-500 border border-dashed border-slate-700 transition-all duration-300 hover:scale-105">
                <span class="material-symbols-outlined text-5xl text-yellow-400/60" style="font-variation-settings: 'FILL' 0;">bookmark</span>
            </div>
            <h3 class="text-2xl font-bold text-on-surface">Chưa có bài viết nào được lưu</h3>
            <p class="mt-2 text-slate-400 max-w-md mx-auto leading-relaxed">
                Hãy lưu lại những bài đăng thú vị, bổ ích để có thể dễ dàng đọc lại và chia sẻ sau này.
            </p>
            <a href="{{ route('home') }}" class="mt-8 px-8 py-3 bg-gradient-to-r from-sky-400 to-blue-500 text-white font-bold rounded-xl hover:from-sky-500 hover:to-blue-600 transition-all duration-300 shadow-lg shadow-sky-500/25 hover:shadow-sky-500/40 hover:-translate-y-0.5 active:translate-y-0">
                Khám phá bảng tin ngay
            </a>
        </div>
    @endif
</div>
@endsection
