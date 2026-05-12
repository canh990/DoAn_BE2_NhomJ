@extends('layouts.app')

@section('title', 'Bài viết của ' . $post->user->name)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ url()->previous() == url()->current() ? route('home') : url()->previous() }}" class="flex items-center gap-2 text-slate-400 hover:text-sky-400 transition-colors group">
            <span class="material-symbols-outlined transition-transform group-hover:-translate-x-1">arrow_back</span>
            <span class="font-medium">Quay lại</span>
        </a>
    </div>

    <x-post-card :post="$post" />
</div>
@endsection
