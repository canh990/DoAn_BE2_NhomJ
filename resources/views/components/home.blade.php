@extends('layouts.app')

@section('title', 'Bảng tin')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="glass-panel rounded-2xl p-4 border border-emerald-400/20 bg-emerald-500/10 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <section class="glass-panel rounded-2xl p-4">
        <div class="flex gap-4">
            <img class="w-12 h-12 rounded-full border border-sky-400/20 shrink-0" alt="Avatar" src="{{ Auth::user()->anh_dai_dien ? asset('storage/' . Auth::user()->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}">
            <div class="w-full">
                <form action="{{ route('posts.store') }}" method="POST">
                    @csrf
                    <textarea id="post-content" name="noi_dung" maxlength="280" class="w-full bg-transparent border border-white/10 focus:border-sky-400 focus:ring-0 text-on-surface placeholder-slate-500 resize-none text-lg leading-relaxed rounded-3xl p-4 min-h-[140px]" placeholder="Bạn đang nghĩ gì?" rows="4">{{ old('noi_dung') }}</textarea>
                    @error('noi_dung')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="h-px bg-white/5 my-3"></div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-1 flex-wrap">
                            <button type="button" class="p-2 text-sky-300 hover:bg-sky-400/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined" data-icon="image">image</span>
                            </button>
                            <button type="button" class="p-2 text-tertiary hover:bg-tertiary/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined" data-icon="gif_box">gif_box</span>
                            </button>
                            <button type="button" class="p-2 text-green-400 hover:bg-green-400/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined" data-icon="label">label</span>
                            </button>
                            <button type="button" class="p-2 text-yellow-400 hover:bg-yellow-400/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined" data-icon="mood">mood</span>
                            </button>
                            <button type="button" class="p-2 text-red-400 hover:bg-red-400/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined" data-icon="location_on">location_on</span>
                            </button>
                            <button type="button" class="p-2 text-primary hover:bg-sky-400/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined" data-icon="poll">poll</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span id="post-char-count" class="text-sm text-slate-400">0/280</span>
                            <button id="post-submit-button" type="submit" class="bg-primary/20 text-primary border border-primary/30 px-6 py-1.5 rounded-full font-semibold hover:bg-primary/30 transition-all disabled:cursor-not-allowed disabled:opacity-50">
                                Đăng
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @forelse($posts as $post)
        <article class="glass-panel rounded-2xl overflow-hidden">
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img class="w-10 h-10 rounded-full border border-sky-400/20 object-cover" alt="{{ $post->user?->name ?? 'Người dùng' }}" src="{{ $post->user && $post->user->anh_dai_dien ? asset('storage/' . $post->user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}">
                    <div>
                        <h3 class="font-bold text-sm text-on-surface">{{ $post->user?->name ?? 'Người dùng' }}</h3>
                        <p class="text-[10px] text-slate-400">{{ $post->created_at ? $post->created_at->diffForHumans() : 'Không xác định' }}</p>
                    </div>
                </div>
            </div>
            <div class="px-4 pb-3">
                <p class="text-sm leading-relaxed text-on-surface-variant whitespace-pre-line">{{ $post->noi_dung }}</p>
            </div>
            <div class="p-4 border-t border-white/5" data-reaction-area>
                @php
                    $reactionButtons = [
                        'thich' => ['icon' => 'thumb_up', 'label' => 'Thích', 'color' => 'text-sky-400'],
                        'tim' => ['icon' => 'favorite', 'label' => 'Yêu thích', 'color' => 'text-rose-400'],
                        'haha' => ['icon' => 'mood', 'label' => 'Haha', 'color' => 'text-yellow-300'],
                        'buon' => ['icon' => 'sentiment_dissatisfied', 'label' => 'Buồn', 'color' => 'text-slate-400'],
                        'phan_no' => ['icon' => 'mood_bad', 'label' => 'Phẫn nộ', 'color' => 'text-orange-400'],
                        'wow' => ['icon' => 'emoji_objects', 'label' => 'Wow', 'color' => 'text-emerald-400'],
                    ];
                    $userReaction = optional($post->reactions ?? collect())->first()->loai_cam_xuc ?? null;
                    $selected = $userReaction ? ($reactionButtons[$userReaction] ?? null) : null;
                    $selectedIcon = $selected['icon'] ?? 'thumb_up';
                    $selectedLabel = $selected['label'] ?? 'Thích';
                    $selectedColor = $selected['color'] ?? 'text-sky-400';
                @endphp

                <div class="relative">
                    <div class="flex items-center gap-2">
                        <button type="button" data-reaction-trigger class="flex items-center gap-2 rounded-full border px-3 py-2 text-sm font-medium transition-all duration-200 {{ $selected ? 'border-sky-400/20 bg-sky-400/10 text-sky-300' : 'border-white/10 bg-slate-950/80 text-slate-300 hover:border-sky-400/20 hover:bg-sky-400/10 hover:text-sky-300' }}">
                            <span class="material-symbols-outlined {{ $selectedColor }}" data-reaction-trigger-icon>{{ $selectedIcon }}</span>
                            <span data-reaction-trigger-label>{{ $selectedLabel }}</span>
                        </button>

                        <button type="button" data-comment-toggle class="flex items-center gap-2 text-slate-400 hover:text-sky-300 transition-colors py-1.5 px-4 rounded-full hover:bg-sky-400/10">
                            <span class="material-symbols-outlined" data-icon="chat_bubble">chat_bubble</span>
                            <span class="text-sm font-medium">Bình luận</span>
                            <span class="text-sm text-slate-400" data-comment-count>({{ $post->comments_count }})</span>
                        </button>

                        <button class="flex items-center gap-2 text-slate-400 hover:text-sky-300 transition-colors py-1.5 px-4 rounded-full hover:bg-sky-400/10">
                            <span class="material-symbols-outlined" data-icon="share">share</span>
                            <span class="text-sm font-medium">Chia sẻ</span>
                        </button>

                        <span class="ml-auto text-xs text-slate-400" data-reaction-count>{{ $post->reactions_count }} cảm xúc</span>
                    </div>

                    <div data-reaction-picker class="hidden absolute left-0 bottom-full z-10 mb-2 w-auto rounded-[32px] border border-white/10 bg-slate-950/95 p-3 shadow-[0_12px_35px_rgba(0,0,0,0.25)] backdrop-blur-sm transition-all duration-200">
                        <div class="flex items-center gap-2">
                            @foreach($reactionButtons as $type => $button)
                                <button type="button" data-reaction-option data-reaction="{{ $type }}" data-reaction-label="{{ $button['label'] }}" data-reaction-color="{{ $button['color'] }}" data-reaction-icon="{{ $button['icon'] }}" class="flex flex-col items-center justify-center rounded-3xl bg-slate-900 px-3 py-2 text-center text-slate-300 transition duration-200 hover:-translate-y-1 hover:bg-sky-400/10 hover:text-sky-300">
                                    <span class="material-symbols-outlined {{ $button['color'] }} text-xl">{{ $button['icon'] }}</span>
                                    <span class="text-[10px]">{{ $button['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <form class="reaction-submit-form hidden" method="POST" action="{{ route('posts.react', $post) }}">
                    @csrf
                    <input type="hidden" name="loai_cam_xuc" value="">
                </form>

                <div data-comment-box class="hidden mt-3 rounded-3xl border border-white/10 bg-slate-950/80 p-3">
                    <form class="comment-submit-form" method="POST" action="{{ route('posts.comment', $post) }}">
                        @csrf
                        <textarea name="noi_dung" rows="2" required class="w-full bg-transparent border border-white/10 focus:border-sky-400 focus:ring-0 rounded-3xl p-3 text-sm text-slate-100 placeholder:text-slate-500" placeholder="Viết bình luận..."></textarea>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-xs text-slate-500">Viết bình luận mới</span>
                            <button type="submit" class="rounded-full bg-sky-400/10 text-sky-300 px-4 py-2 text-sm font-semibold hover:bg-sky-400/20">Gửi</button>
                        </div>
                    </form>

                    <div data-comment-list class="mt-4 space-y-3 text-slate-300">
                        @if($post->comments->isEmpty())
                            <div data-no-comments class="text-sm text-slate-500">Chưa có bình luận nào. Hãy là người đầu tiên bình luận.</div>
                        @else
                            @foreach($post->comments as $comment)
                                <div class="rounded-2xl border border-white/10 bg-slate-950 p-3">
                                    <div class="flex gap-3 items-start">
                                        <img class="w-8 h-8 rounded-full object-cover border border-slate-700" src="{{ $comment->user && $comment->user->anh_dai_dien ? asset('storage/' . $comment->user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}" alt="{{ $comment->user?->name ?? 'Người dùng' }}">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between gap-2 text-sm text-slate-200">
                                                <span class="font-semibold">{{ $comment->user?->name ?? 'Người dùng' }}</span>
                                                <span class="text-xs text-slate-500">{{ $comment->ngay_tao?->diffForHumans() ?? '' }}</span>
                                            </div>
                                            <p class="mt-1 text-sm leading-relaxed text-slate-300">{{ $comment->noi_dung }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </article>
    @empty
        <div class="glass-panel rounded-2xl p-6 text-center text-slate-300">
            <p class="text-sm">Chưa có bài viết nào. Hãy là người đầu tiên đăng trạng thái!</p>
        </div>
    @endforelse
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textarea = document.getElementById('post-content');
        const counter = document.getElementById('post-char-count');
        const submitButton = document.getElementById('post-submit-button');

        if (!textarea || !counter || !submitButton) {
            return;
        }

        const maxLength = Number(textarea.getAttribute('maxlength')) || 280;

        const updateCount = function () {
            const length = textarea.value.length;
            counter.textContent = `${length}/${maxLength}`;
            submitButton.disabled = textarea.value.trim().length === 0;
        };

        textarea.addEventListener('input', updateCount);
        updateCount();
    });
</script>
@endsection