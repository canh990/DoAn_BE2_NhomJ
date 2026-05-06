@props([
    'post' => null,
    'user' => null,
    'content' => null,
    'image' => null,
    'timestamp' => null,
])

@php
    $author = $user
        ?? data_get($post, 'user')
        ?? data_get($post, 'author')
        ?? data_get($post, 'nguoiDung');

    $authorName = $author->name
        ?? data_get($author, 'ten_dang_nhap')
        ?? data_get($post, 'user.name')
        ?? data_get($post, 'author.name')
        ?? 'Người dùng';

    $authorUsername = data_get($author, 'ten_dang_nhap')
        ?? data_get($post, 'user.ten_dang_nhap')
        ?? data_get($post, 'author.ten_dang_nhap');

    $avatar = data_get($author, 'anh_dai_dien')
        ? asset('storage/' . data_get($author, 'anh_dai_dien'))
        : 'https://via.placeholder.com/96x96.png?text=User';

    $body = $content
        ?? data_get($post, 'noi_dung')
        ?? data_get($post, 'content')
        ?? data_get($post, 'caption');

    $postImage = $image;

    if (! $postImage) {
        $mediaPath = data_get($post, 'media.0.duong_dan')
            ?? data_get($post, 'media.0.path')
            ?? data_get($post, 'media.0.url')
            ?? data_get($post, 'anh')
            ?? data_get($post, 'image')
            ?? data_get($post, 'image_url');

        if ($mediaPath) {
            $postImage = str_starts_with($mediaPath, 'http')
                ? $mediaPath
                : asset('storage/' . ltrim($mediaPath, '/'));
        }
    }

    $createdAt = $timestamp ?? data_get($post, 'created_at');
    $displayTime = 'Vừa xong';

    if ($createdAt instanceof \Illuminate\Support\Carbon) {
        $displayTime = $createdAt->diffForHumans();
    } elseif ($createdAt) {
        try {
            $displayTime = \Illuminate\Support\Carbon::parse($createdAt)->diffForHumans();
        } catch (\Throwable $e) {
            $displayTime = $createdAt;
        }
    }

    $commentCount = data_get($post, 'comments_count', 0);
    $reactionCount = data_get($post, 'reactions_count', data_get($post, 'likes_count', 0));
    $shareCount = data_get($post, 'shares_count', 0);
    $viewCount = data_get($post, 'views_count', 0);
    $isVerified = (bool) (data_get($author, 'da_xac_thuc') ?? false);
@endphp

<article {{ $attributes->merge(['class' => 'glass-panel rounded-2xl p-6 hover:border-sky-400/30 transition-all group']) }}>
    <div class="flex gap-4">
        <img
            class="w-12 h-12 rounded-full border border-sky-400/20 shrink-0 object-cover"
            src="{{ $avatar }}"
            alt="{{ $authorName }}"
        />

        <div class="flex-1 space-y-3 min-w-0">
            <div class="flex justify-between items-start gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-bold text-on-surface hover:text-sky-300 cursor-pointer truncate">
                            {{ $authorName }}
                        </span>

                        @if($isVerified)
                            <span class="material-symbols-outlined text-sky-400 text-base" data-icon="verified" style="font-variation-settings: 'FILL' 1;">
                                verified
                            </span>
                        @endif

                        <span class="text-slate-500 text-sm">
                            @if($authorUsername)
                                {{ '@' . $authorUsername }} ·
                            @endif
                            {{ $displayTime }}
                        </span>
                    </div>
                </div>

                <button class="text-slate-500 hover:text-sky-300 shrink-0" type="button">
                    <span class="material-symbols-outlined" data-icon="more_horiz">more_horiz</span>
                </button>
            </div>

            @if(filled($body))
                <p class="text-on-surface-variant leading-relaxed whitespace-pre-line">
                    {{ $body }}
                </p>
            @endif

            @if($postImage)
                <div class="rounded-2xl overflow-hidden border border-sky-400/10 mt-3 aspect-video bg-slate-900">
                    <img class="w-full h-full object-cover" src="{{ $postImage }}" alt="Hình ảnh bài viết">
                </div>
            @endif

            <div class="flex flex-col gap-3 pt-3 text-slate-400 max-w-sm">
                @php
                    $reactionButtons = [
                        'thich' => ['icon' => 'thumb_up', 'label' => 'Thích', 'color' => 'text-sky-400'],
                        'tim' => ['icon' => 'favorite', 'label' => 'Yêu thích', 'color' => 'text-rose-400'],
                        'haha' => ['icon' => 'mood', 'label' => 'Haha', 'color' => 'text-yellow-300'],
                        'buon' => ['icon' => 'sentiment_dissatisfied', 'label' => 'Buồn', 'color' => 'text-slate-400'],
                        'phan_no' => ['icon' => 'mood_bad', 'label' => 'Phẫn nộ', 'color' => 'text-orange-400'],
                        'wow' => ['icon' => 'emoji_objects', 'label' => 'Wow', 'color' => 'text-emerald-400'],
                    ];
                    $userReaction = optional(data_get($post, 'reactions'))->first()->loai_cam_xuc ?? null;
                    $selected = $userReaction ? ($reactionButtons[$userReaction] ?? null) : null;
                    $selectedIcon = $selected['icon'] ?? 'thumb_up';
                    $selectedLabel = $selected['label'] ?? 'Thích';
                    $selectedColor = $selected['color'] ?? 'text-sky-400';
                @endphp

                <div class="relative" data-reaction-area>
                    <div class="flex items-center gap-2">
                        <button type="button" data-reaction-trigger class="flex items-center gap-2 rounded-full border px-3 py-2 text-sm font-medium transition-all duration-200 {{ $selected ? 'border-sky-400/20 bg-sky-400/10 text-sky-300' : 'border-white/10 bg-slate-950 text-slate-300 hover:border-sky-400/20 hover:bg-sky-400/10 hover:text-sky-300' }}">
                            <span class="material-symbols-outlined {{ $selectedColor }}" data-reaction-trigger-icon>{{ $selectedIcon }}</span>
                            <span data-reaction-trigger-label>{{ $selectedLabel }}</span>
                        </button>

                        <button type="button" data-comment-toggle class="flex items-center gap-2 hover:text-sky-300 transition-colors group/btn px-3 py-2 rounded-full border border-white/10 bg-slate-950 text-slate-300 hover:bg-sky-400/10">
                            <span class="material-symbols-outlined text-xl group-hover/btn:bg-sky-400/10 p-2 rounded-full" data-icon="chat_bubble">chat_bubble</span>
                            <span class="text-sm">Bình luận</span>
                            <span class="text-sm text-slate-400" data-comment-count>({{ $commentCount }})</span>
                        </button>

                        <button class="flex items-center gap-2 hover:text-sky-300 transition-colors group/btn px-3 py-2 rounded-full border border-white/10 bg-slate-950 text-slate-300 hover:bg-sky-400/10">
                            <span class="material-symbols-outlined text-xl group-hover/btn:bg-emerald-400/10 p-2 rounded-full" data-icon="share">share</span>
                            <span class="text-sm">{{ $shareCount }}</span>
                        </button>

                        <span class="ml-auto text-xs text-slate-400" data-reaction-count>{{ $reactionCount }} cảm xúc</span>
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
            </div>
        </div>
    </div>
</article>
