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

    $authorName = data_get($author, 'name')
        ?? data_get($author, 'ten_hien_thi')
        ?? data_get($author, 'ten_dang_nhap')
        ?? data_get($post, 'user.name')
        ?? data_get($post, 'author.name')
        ?? 'Người dùng';

    $authorUsername = data_get($author, 'ten_dang_nhap')
        ?? data_get($author, 'username')
        ?? data_get($post, 'user.ten_dang_nhap')
        ?? data_get($post, 'author.ten_dang_nhap');

    $avatarPath = data_get($author, 'anh_dai_dien')
        ?? data_get($author, 'avatar')
        ?? data_get($author, 'avatar_url');

    $avatar = $avatarPath
        ? (\Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://'])
            ? $avatarPath
            : asset('storage/' . ltrim($avatarPath, '/')))
        : asset('storage/avatars/avtmacdinh.png');

    $body = $content
        ?? data_get($post, 'noi_dung')
        ?? data_get($post, 'content')
        ?? data_get($post, 'caption');

    $postImage = $image;

    if (! $postImage) {
        $mediaPath = data_get($post, 'media.0.duong_dan')
            ?? data_get($post, 'media.0.path')
            ?? data_get($post, 'media.0.url')
            ?? data_get($post, 'duong_dan_media')
            ?? data_get($post, 'anh')
            ?? data_get($post, 'image')
            ?? data_get($post, 'image_url');

        if ($mediaPath) {
            $postImage = \Illuminate\Support\Str::startsWith($mediaPath, ['http://', 'https://'])
                ? $mediaPath
                : asset('storage/' . ltrim($mediaPath, '/'));
        }
    }

    $createdAt = $timestamp ?? data_get($post, 'created_at') ?? data_get($post, 'thoi_gian_tao');
    $displayTime = 'Vừa xong';

    if ($createdAt instanceof \Carbon\CarbonInterface) {
        $displayTime = $createdAt->diffForHumans();
    } elseif (is_string($createdAt) && filled($createdAt)) {
        try {
            $displayTime = \Illuminate\Support\Carbon::parse($createdAt)->diffForHumans();
        } catch (\Throwable $e) {
            $displayTime = $createdAt;
        }
    }

    $commentCount = (int) (data_get($post, 'comments_count')
        ?? data_get($post, 'binh_luan_count')
        ?? 0);

    $reactionCount = (int) (data_get($post, 'reactions_count')
        ?? data_get($post, 'likes_count')
        ?? data_get($post, 'luot_thich')
        ?? 0);

    $shareCount = (int) (data_get($post, 'shares_count')
        ?? data_get($post, 'luot_chia_se')
        ?? 0);

    $viewCount = (int) (data_get($post, 'views_count')
        ?? data_get($post, 'luot_xem')
        ?? 0);

    $isVerified = (bool) (data_get($author, 'da_xac_thuc') ?? false);
@endphp

<article {{ $attributes->merge(['class' => 'glass-panel group rounded-2xl p-6 transition-all hover:border-sky-400/30']) }}>
    <div class="flex gap-4">
        <img
            class="h-12 w-12 shrink-0 rounded-full border border-sky-400/20 object-cover"
            src="{{ $avatar }}"
            alt="{{ $authorName }}"
        >

        <div class="min-w-0 flex-1 space-y-3">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="truncate font-bold text-on-surface hover:text-sky-300">
                            {{ $authorName }}
                        </span>

                        @if ($isVerified)
                            <span class="material-symbols-outlined text-base text-sky-400" data-icon="verified" style="font-variation-settings: 'FILL' 1;">
                                verified
                            </span>
                        @endif

                        <span class="text-sm text-slate-500">
                            @if ($authorUsername)
                                {{ '@' . $authorUsername }} ·
                            @endif
                            {{ $displayTime }}
                        </span>
                    </div>
                </div>

                <button class="shrink-0 text-slate-500 transition-colors hover:text-sky-300" type="button" aria-label="Tùy chọn bài viết">
                    <span class="material-symbols-outlined" data-icon="more_horiz">more_horiz</span>
                </button>
            </div>

            @if (filled($body))
                <p class="whitespace-pre-line leading-relaxed text-on-surface-variant">
                    {{ $body }}
                </p>
            @endif

            @if ($postImage)
                <div class="mt-3 aspect-video overflow-hidden rounded-2xl border border-sky-400/10 bg-slate-900">
                    <img class="h-full w-full object-cover" src="{{ $postImage }}" alt="Hình ảnh bài viết">
                </div>
            @endif

            <div class="flex max-w-sm items-center justify-between pt-3 text-slate-400">
                <button class="group/btn flex items-center gap-2 transition-colors hover:text-sky-300" type="button" aria-label="Bình luận">
                    <span class="material-symbols-outlined rounded-full p-2 text-xl group-hover/btn:bg-sky-400/10" data-icon="chat_bubble">chat_bubble</span>
                    <span class="text-sm">{{ $commentCount }}</span>
                </button>

                <button class="group/btn flex items-center gap-2 transition-colors hover:text-emerald-400" type="button" aria-label="Chia sẻ">
                    <span class="material-symbols-outlined rounded-full p-2 text-xl group-hover/btn:bg-emerald-400/10" data-icon="share">share</span>
                    <span class="text-sm">{{ $shareCount }}</span>
                </button>

                <button class="group/btn flex items-center gap-2 transition-colors hover:text-rose-400" type="button" aria-label="Yêu thích">
                    <span class="material-symbols-outlined rounded-full p-2 text-xl group-hover/btn:bg-rose-400/10" data-icon="favorite">favorite</span>
                    <span class="text-sm">{{ $reactionCount }}</span>
                </button>

                <button class="group/btn flex items-center gap-2 transition-colors hover:text-sky-300" type="button" aria-label="Lượt xem">
                    <span class="material-symbols-outlined rounded-full p-2 text-xl group-hover/btn:bg-sky-400/10" data-icon="bar_chart">bar_chart</span>
                    <span class="text-sm">{{ $viewCount }}</span>
                </button>
            </div>
        </div>
    </div>
</article>
