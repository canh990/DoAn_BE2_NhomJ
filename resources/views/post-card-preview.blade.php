@extends('layouts.auth')

@section('title', 'Post Card Preview')

@section('content')
    <div class="relative z-10 w-full max-w-3xl space-y-6">
        <div class="space-y-2 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-300/70">Component Preview</p>
            <h1 class="text-3xl font-extrabold text-on-surface">Post Card</h1>
            <p class="mx-auto max-w-2xl text-sm text-on-surface-variant">
                Route này dùng để xem nhanh giao diện của component <code class="rounded bg-white/5 px-2 py-1 text-sky-300">x-post-card</code>.
            </p>
        </div>

        <x-post-card :post="$post" :user="$user" class="shadow-2xl shadow-sky-950/30" />
    </div>
@endsection
