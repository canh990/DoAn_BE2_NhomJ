@extends('layouts.app')

@section('title', __('messages.explore_title', 'Khám phá'))

@section('content')
<div class="max-w-4xl mx-auto px-4 md:px-8 pt-6">
    <h1 class="text-2xl font-bold">{{ __('messages.explore_title', 'Khám phá') }}</h1>
    <p class="text-on-surface-variant mt-2">{{ __('messages.explore_desc', 'Nội dung khám phá sẽ hiển thị ở đây.') }}</p>
</div>
@endsection
