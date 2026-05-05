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

            <div class="flex items-center justify-between pt-3 text-slate-400 max-w-sm">
                <button class="flex items-center gap-2 hover:text-sky-300 transition-colors group/btn" type="button">
                    <span class="material-symbols-outlined text-xl group-hover/btn:bg-sky-400/10 p-2 rounded-full" data-icon="chat_bubble">chat_bubble</span>
                    <span class="text-sm">{{ $commentCount }}</span>
                </button>

                <button class="flex items-center gap-2 hover:text-emerald-400 transition-colors group/btn" type="button">
                    <span class="material-symbols-outlined text-xl group-hover/btn:bg-emerald-400/10 p-2 rounded-full" data-icon="share">share</span>
                    <span class="text-sm">{{ $shareCount }}</span>
                </button>

                <button class="flex items-center gap-2 hover:text-rose-400 transition-colors group/btn" type="button">
                    <span class="material-symbols-outlined text-xl group-hover/btn:bg-rose-400/10 p-2 rounded-full" data-icon="favorite">favorite</span>
                    <span class="text-sm">{{ $reactionCount }}</span>
                </button>

                <button class="flex items-center gap-2 hover:text-sky-300 transition-colors group/btn" type="button">
                    <span class="material-symbols-outlined text-xl group-hover/btn:bg-sky-400/10 p-2 rounded-full" data-icon="bar_chart">bar_chart</span>
                    <span class="text-sm">{{ $viewCount }}</span>
                </button>
            </div>
        </div>
    </div>
</article>
