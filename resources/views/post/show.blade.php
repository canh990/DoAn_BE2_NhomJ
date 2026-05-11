@extends('layouts.app')

@section('title', 'Bài viết của ' . ($post->user->name ?? 'Người dùng'))

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="mb-6 flex items-center gap-4">
        <a href="javascript:history.back()" class="flex h-10 w-10 items-center justify-center rounded-full glass-panel hover:bg-white/10 transition-colors">
            <span class="material-symbols-outlined text-sky-400">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-on-surface">Bài viết</h1>
    </div>

    <x-post-card :post="$post" />
</div>
@endsection
