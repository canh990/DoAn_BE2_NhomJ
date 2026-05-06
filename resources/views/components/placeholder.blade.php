@extends('layouts.app')

@section('title', $title ?? 'NHOMJ')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="glass-panel rounded-3xl p-8 text-center">
        <h1 class="text-3xl font-bold text-on-surface mb-4">{{ $title ?? 'Nhóm J' }}</h1>
        <p class="text-on-surface-variant text-base leading-relaxed">{{ $message ?? 'Nội dung sẽ được cập nhật trong thời gian sớm nhất.' }}</p>
    </div>
</div>
@endsection