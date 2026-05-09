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
    $postId = data_get($post, 'id');
    $hasPersistedPost = filled($postId);
    $comments = collect(data_get($post, 'comments', []));

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

                        @if (data_get($post, 'cam_xuc') || data_get($post, 'hoat_dong'))
                            @php
                            $camXucLabels = [
                                'thich' => 'thích',
                                'tim' => 'yêu thích',
                                'haha' => 'haha',
                                'buon' => 'buồn',
                                'phan_no' => 'phẫn nộ',
                                'wow' => 'wow',
                            ];
                            @endphp
                            <span class="text-sm text-slate-400 flex items-center gap-1">
                                @if (data_get($post, 'cam_xuc'))
                                    đang cảm thấy <span class="font-medium text-slate-300">{{ $camXucLabels[data_get($post, 'cam_xuc')] ?? strtolower(data_get($post, 'cam_xuc')) }}</span>
                                @endif
                                @if (data_get($post, 'hoat_dong'))
                                    {{ strtolower(data_get($post, 'hoat_dong')) }}
                                @endif
                            </span>
                        @endif

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

                @if(auth()->id() === data_get($post, 'nguoi_dung_id') || (isset($user) && auth()->id() === data_get($user, 'id')))
                    <div class="relative shrink-0">
                        <button type="button" class="post-dropdown-trigger text-slate-500 transition-colors hover:text-sky-300 p-2 rounded-full hover:bg-white/5" aria-label="Tùy chọn bài viết">
                            <span class="material-symbols-outlined" data-icon="more_horiz">more_horiz</span>
                        </button>
                        <div class="post-dropdown-menu hidden absolute right-0 top-full mt-1 w-40 bg-slate-900 border border-white/10 rounded-xl shadow-2xl overflow-hidden z-20">
                            @if($postId)
                            <form action="{{ route('posts.destroy', $postId) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này? Các ảnh/video đính kèm cũng sẽ bị xóa vĩnh viễn.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-400 hover:bg-white/5 flex items-center gap-2 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                    Xóa bài viết
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                @else
                    <button class="shrink-0 text-slate-500 transition-colors hover:text-sky-300 p-2 rounded-full hover:bg-white/5" type="button" aria-label="Tùy chọn bài viết">
                        <span class="material-symbols-outlined" data-icon="more_horiz">more_horiz</span>
                    </button>
                @endif
            </div>

            @if (filled($body))
                <p class="whitespace-pre-line leading-relaxed text-on-surface-variant">
                    {{ $body }}
                </p>
            @endif

            @php
                $mediaItems = data_get($post, 'media', collect());
                if (!($mediaItems instanceof \Illuminate\Support\Collection)) {
                    $mediaItems = collect($mediaItems);
                }
            @endphp

            @if ($mediaItems->count() > 0)
                @php
                    $mediaCount = $mediaItems->count();
                @endphp
                <div class="mt-3 grid gap-2 {{ $mediaCount == 1 ? 'grid-cols-1' : ($mediaCount == 2 ? 'grid-cols-2' : 'grid-cols-2 sm:grid-cols-3') }}">
                    @foreach($mediaItems as $media)
                        @php
                            $mediaPath = data_get($media, 'duong_dan') ?? data_get($media, 'path') ?? data_get($media, 'url');
                            if (!$mediaPath) continue;
                            
                            $mediaSrc = \Illuminate\Support\Str::startsWith($mediaPath, ['http://', 'https://'])
                                ? $mediaPath
                                : asset('storage/' . ltrim($mediaPath, '/'));
                            $mediaLoai = data_get($media, 'loai');
                            $isVideo = $mediaLoai === 'video' || \Illuminate\Support\Str::endsWith($mediaPath, ['.mp4', '.webm', '.mov']);
                        @endphp
                        <div class="overflow-hidden rounded-xl border border-white/10 bg-slate-900/50 {{ $mediaCount > 1 ? 'aspect-square' : '' }}">
                            @if($isVideo)
                                <video src="{{ $mediaSrc }}" controls controlsList="nodownload" muted playsinline loop class="w-full h-full {{ $mediaCount == 1 ? 'max-h-[500px] object-contain block mx-auto' : 'object-cover' }}"></video>
                            @else
                                <img src="{{ $mediaSrc }}" 
                                     alt="Post image" 
                                     data-post-id="{{ $postId }}"
                                     class="post-image-item cursor-pointer hover:opacity-90 transition-opacity w-full h-full {{ $mediaCount == 1 ? 'max-h-[500px] object-contain block mx-auto' : 'object-cover' }}">
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif ($postImage)
                @php
                    $isVideo = \Illuminate\Support\Str::endsWith($postImage, ['.mp4', '.webm', '.mov']);
                @endphp
                <div class="mt-3 overflow-hidden rounded-2xl border border-white/10 bg-slate-900/50">
                    @if($isVideo)
                        <video class="w-full h-auto max-h-[500px] object-contain block mx-auto" controls controlsList="nodownload" muted playsinline loop src="{{ $postImage }}"></video>
                    @else
                        <img class="post-image-item cursor-pointer hover:opacity-90 transition-opacity w-full h-auto max-h-[500px] object-contain block mx-auto" data-post-id="{{ $postId }}" src="{{ $postImage }}" alt="Hình ảnh bài viết">
                    @endif
                </div>
            @endif

            <div class="flex flex-col gap-3 pt-4 border-t border-white/5 mt-4 text-slate-400">
                <div class="relative" data-reaction-area>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1 sm:gap-2">
                            <button type="button" data-reaction-trigger class="group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 transition-all duration-300 {{ $selected ? 'bg-sky-400/10 text-sky-400' : 'text-slate-400 hover:bg-slate-800/60 hover:text-sky-300' }}">
                                <div class="relative flex items-center justify-center transition-transform group-hover:scale-110 group-active:scale-95">
                                    <span class="material-symbols-outlined text-[20px] sm:text-[22px] {{ $selectedColor }}" data-reaction-trigger-icon style="{{ $selected ? 'font-variation-settings: \'FILL\' 1;' : '' }}">{{ $selectedIcon }}</span>
                                </div>
                                <span class="text-[13px] sm:text-sm font-semibold tracking-wide" data-reaction-trigger-label>{{ $selectedLabel }}</span>
                            </button>

                            <button type="button" data-comment-toggle class="group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 text-slate-400 transition-all duration-300 hover:bg-slate-800/60 hover:text-sky-300">
                                <div class="relative flex items-center justify-center transition-transform group-hover:scale-110 group-active:scale-95">
                                    <span class="material-symbols-outlined text-[20px] sm:text-[22px]" data-icon="chat_bubble_outline">chat_bubble</span>
                                </div>
                                <span class="text-[13px] sm:text-sm font-semibold tracking-wide hidden sm:block">Bình luận</span>
                                <span class="text-[13px] sm:text-sm font-bold text-slate-500 group-hover:text-sky-400/80" data-comment-count>{{ $commentCount > 0 ? '('.$commentCount.')' : '' }}</span>
                            </button>

                            <button class="group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 text-slate-400 transition-all duration-300 hover:bg-slate-800/60 hover:text-emerald-400">
                                <div class="relative flex items-center justify-center transition-transform group-hover:scale-110 group-active:scale-95">
                                    <span class="material-symbols-outlined text-[20px] sm:text-[22px]" data-icon="share">share</span>
                                </div>
                                <span class="text-[13px] sm:text-sm font-semibold tracking-wide hidden sm:block">Chia sẻ</span>
                            </button>
                        </div>

                        <div class="flex items-center gap-1.5 pl-2">
                            <span class="text-[13px] sm:text-sm text-slate-400 font-medium" data-reaction-count>{{ $reactionCount }} cảm xúc</span>
                        </div>
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

                    @if($hasPersistedPost)
                        <form class="reaction-submit-form hidden" method="POST" action="{{ route('posts.react', ['post' => $postId]) }}">
                            @csrf
                            <input type="hidden" name="loai_cam_xuc" value="">
                        </form>
                    @endif

                    <div data-comment-box class="hidden mt-3 rounded-3xl border border-white/10 bg-slate-950/80 p-3">
                        @if($hasPersistedPost)
                            <form class="comment-submit-form" method="POST" action="{{ route('posts.comment', ['post' => $postId]) }}">
                                @csrf
                                <input type="hidden" name="binh_luan_cha_id" value="">
                                <textarea name="noi_dung" rows="2" required class="w-full bg-transparent border border-white/10 focus:border-sky-400 focus:ring-0 rounded-3xl p-3 text-sm text-slate-100 placeholder:text-slate-500" placeholder="Viết bình luận..."></textarea>
                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <span class="text-xs text-slate-500" data-comment-action>Viết bình luận mới</span>
                                    <button type="button" data-comment-cancel class="hidden text-xs text-slate-400 hover:text-white">Hủy trả lời</button>
                                    <button type="submit" class="rounded-full bg-sky-400/10 text-sky-300 px-4 py-2 text-sm font-semibold hover:bg-sky-400/20">Gửi</button>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-dashed border-white/10 bg-slate-950/60 p-3 text-sm text-slate-500">
                                Bài viết mẫu không hỗ trợ cảm xúc hoặc bình luận.
                            </div>
                        @endif

                        <div data-comment-list class="mt-4 space-y-3 text-slate-300">
                            @php
                                $rootComments = $comments->whereNull('binh_luan_cha_id');
                            @endphp
                            @if($comments->isEmpty())
                                <div data-no-comments class="text-sm text-slate-500">Chưa có bình luận nào. Hãy là người đầu tiên bình luận.</div>
                            @else
                                @foreach($rootComments as $comment)
                                    <div class="rounded-2xl border border-white/10 bg-slate-950 p-3" data-comment-id="{{ $comment->id }}">
                                        <div class="flex gap-3 items-start">
                                            <img class="w-8 h-8 rounded-full object-cover border border-slate-700" src="{{ $comment->user && $comment->user->anh_dai_dien ? asset('storage/' . $comment->user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}" alt="{{ $comment->user?->name ?? 'Người dùng' }}">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between gap-2 text-sm text-slate-200">
                                                    <span class="font-semibold">{{ $comment->user?->name ?? 'Người dùng' }}</span>
                                                    <span class="text-xs text-slate-500">{{ $comment->ngay_tao?->diffForHumans() ?? '' }}</span>
                                                </div>
                                                <p class="mt-1 text-sm leading-relaxed text-slate-300">{{ $comment->noi_dung }}</p>
                                                <div class="mt-3 flex items-center gap-3 text-xs text-slate-400">
                                                    <button type="button" data-comment-reply-button data-comment-id="{{ $comment->id }}" data-comment-user="{{ $comment->user?->name ?? 'Người dùng' }}" class="hover:text-sky-300">Trả lời</button>
                                                </div>
                                                <div class="mt-3 space-y-3 pl-10" data-comment-replies>
                                                    @foreach($comment->children as $reply)
                                                        <div class="rounded-2xl border border-white/10 bg-slate-950 p-3" data-comment-id="{{ $reply->id }}">
                                                            <div class="flex gap-3 items-start">
                                                                <img class="w-8 h-8 rounded-full object-cover border border-slate-700" src="{{ $reply->user && $reply->user->anh_dai_dien ? asset('storage/' . $reply->user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}" alt="{{ $reply->user?->name ?? 'Người dùng' }}">
                                                                <div class="flex-1">
                                                                    <div class="flex items-center justify-between gap-2 text-sm text-slate-200">
                                                                        <span class="font-semibold">{{ $reply->user?->name ?? 'Người dùng' }}</span>
                                                                        <span class="text-xs text-slate-500">{{ $reply->ngay_tao?->diffForHumans() ?? '' }}</span>
                                                                    </div>
                                                                    <p class="mt-1 text-sm leading-relaxed text-slate-300">{{ $reply->noi_dung }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
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
