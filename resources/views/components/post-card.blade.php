@props([
    'post' => null,
    'user' => null,
    'content' => null,
    'image' => null,
    'timestamp' => null,
    'isShared' => false,
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

    $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($authorName) . '&background=random';
    if ($author) {
        if ($author instanceof \App\Models\User) {
            $avatar = $author->avatar_url;
        } else {
            $avatarPath = data_get($author, 'anh_dai_dien')
                ?? data_get($author, 'avatar')
                ?? data_get($author, 'avatar_url');
            $authorUsername = data_get($author, 'ten_dang_nhap')
                ?? data_get($author, 'username')
                ?? 'NguoiDung';

            if ($avatarPath) {
                if (\Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://'])) {
                    $avatar = $avatarPath;
                } else {
                    $avatar = asset('storage/' . ltrim($avatarPath, '/'));
                }
            } else {
                $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($authorUsername) . '&background=random';
            }
        }
    }

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
    $displayTime = app()->getLocale() === 'en' ? 'Just now' : 'Vừa xong';

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
        'thich' => ['icon' => 'thumb_up', 'label' => __('messages.react_thich'), 'color' => 'text-sky-400'],
        'tim' => ['icon' => 'favorite', 'label' => __('messages.react_tim'), 'color' => 'text-rose-400'],
        'haha' => ['icon' => 'mood', 'label' => __('messages.react_haha'), 'color' => 'text-yellow-300'],
        'buon' => ['icon' => 'sentiment_dissatisfied', 'label' => __('messages.react_buon'), 'color' => 'text-slate-400'],
        'phan_no' => ['icon' => 'mood_bad', 'label' => __('messages.react_phan_no'), 'color' => 'text-orange-400'],
        'wow' => ['icon' => 'emoji_objects', 'label' => __('messages.react_wow'), 'color' => 'text-emerald-400'],
    ];

    $userReaction = optional(data_get($post, 'reactions'))->first()->loai_cam_xuc ?? null;
    $selected = $userReaction ? ($reactionButtons[$userReaction] ?? null) : null;
    $selectedIcon = $selected['icon'] ?? 'thumb_up';
    $selectedLabel = $selected['label'] ?? __('messages.post_like');
    $selectedColor = $selected ? ($selected['color'] ?? 'text-sky-400') : '';

    $isProfileUpdate = \Illuminate\Support\Str::startsWith($body, 'vừa cập nhật ảnh');

    $isBookmarked = false;
    if (auth()->check() && $postId && isset($post->bookmarks)) {
        $isBookmarked = $post->bookmarks->contains('nguoi_dung_id', auth()->id());
    }

    $poll = data_get($post, 'poll');
    $pollOptions = collect(data_get($poll, 'options', []));
    $pollVotes = collect(data_get($poll, 'votes', []));
    $userVote = auth()->check() ? $pollVotes->firstWhere('nguoi_dung_id', auth()->id()) : null;
    $userVotedOptionId = data_get($userVote, 'lua_chon_id');
    $hasVotedPoll = filled($userVotedOptionId);
    $totalPollVotes = $pollVotes->count();
@endphp

<article {{ $attributes->merge(['class' => ($isShared ? 'bg-slate-900/40 p-4 border border-white/5 rounded-xl' : 'glass-panel p-6') . ' group rounded-2xl transition-all hover:border-sky-400/30']) }}>
    <div class="flex gap-4">
        <a href="{{ $authorUsername ? route('profile.public', $authorUsername) : '#' }}" class="shrink-0 hover:opacity-80 transition-opacity" title="Xem trang cá nhân của {{ $authorName }}">
            <img
                class="h-12 w-12 rounded-full border border-sky-400/20 object-cover"
                src="{{ $avatar }}"
                alt="{{ $authorName }}"
            >
        </a>

        <div class="min-w-0 flex-1 space-y-3">
            <!-- Pinned Post Indicator Container -->
            <div class="pinned-indicator-container">
                @if(data_get($post, 'da_ghim'))
                    <div class="pinned-indicator flex items-center gap-1 text-xs font-bold text-sky-300 mb-1 select-none">
                        <span class="mr-0.5">📌</span>
                        <span>{{ app()->getLocale() === 'en' ? 'Pinned post' : 'Bài viết đã ghim' }}</span>
                    </div>
                @endif
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <!-- Dòng 1: Tên hiển thị + Tích xanh + Tag -->
                    <div class="flex flex-wrap items-center gap-1 text-[15px]">
                        <a href="{{ $authorUsername ? route('profile.public', $authorUsername) : '#' }}" class="font-bold text-on-surface hover:text-sky-300 transition-colors truncate max-w-[200px]">
                            {{ $authorName }}
                        </a>
                        @if ($isVerified)
                            <span class="material-symbols-outlined text-[16px] text-sky-400 shrink-0" data-icon="verified" style="font-variation-settings: 'FILL' 1;" title="Tài khoản đã xác thực">
                                verified
                            </span>
                        @endif

                        @php
                            $taggedUsers = data_get($post, 'taggedUsers', collect());
                            $taggedCount = $taggedUsers->count();
                        @endphp
                        @if($taggedCount > 0)
                            <span class="text-slate-400 mx-0.5 text-[14px]">cùng với</span>
                            @if($taggedCount === 1)
                                <a href="{{ route('profile.public', $taggedUsers[0]->ten_dang_nhap) }}" class="font-bold text-on-surface hover:text-sky-300 transition-colors truncate max-w-[150px]">
                                    {{ $taggedUsers[0]->ten_dang_nhap }}
                                </a>
                            @elseif($taggedCount === 2)
                                <a href="{{ route('profile.public', $taggedUsers[0]->ten_dang_nhap) }}" class="font-bold text-on-surface hover:text-sky-300 transition-colors truncate max-w-[100px]">
                                    {{ $taggedUsers[0]->ten_dang_nhap }}
                                </a>
                                <span class="text-slate-400 text-[14px]">và</span>
                                <a href="{{ route('profile.public', $taggedUsers[1]->ten_dang_nhap) }}" class="font-bold text-on-surface hover:text-sky-300 transition-colors truncate max-w-[100px]">
                                    {{ $taggedUsers[1]->ten_dang_nhap }}
                                </a>
                            @else
                                <a href="{{ route('profile.public', $taggedUsers[0]->ten_dang_nhap) }}" class="font-bold text-on-surface hover:text-sky-300 transition-colors truncate max-w-[100px]">
                                    {{ $taggedUsers[0]->ten_dang_nhap }}
                                </a>
                                <span class="text-slate-400 text-[14px]">và</span>
                                <span class="font-bold text-on-surface hover:text-sky-300 transition-colors cursor-pointer" title="{{ $taggedUsers->skip(1)->pluck('ten_dang_nhap')->implode(', ') }}">
                                    {{ $taggedCount - 1 }} người khác
                                </span>
                            @endif
                        @endif
                    </div>

                    <!-- Dòng 2: Username + Thời gian (Nằm trực tiếp dưới Tên hiển thị) -->
                    <div class="flex items-center gap-1.5 mt-0.5 text-xs text-slate-500">
                        @if ($authorUsername)
                            <a href="{{ route('profile.public', $authorUsername) }}" class="hover:underline text-slate-400 font-medium">
                                {{ '@' . $authorUsername }}
                            </a>
                            <span>·</span>
                        @endif
                        <span>{{ $displayTime }}</span>
                        @if (data_get($post, 'da_chinh_sua'))
                            <span>·</span>
                            <span class="text-slate-500/70">Đã chỉnh sửa</span>
                        @endif
                    </div>

                    <!-- Dòng 3 (Nếu có): Trạng thái cảm xúc, hoạt động, địa điểm, hành động chia sẻ -->
                    @if($isProfileUpdate || data_get($post, 'cam_xuc') || data_get($post, 'hoat_dong') || data_get($post, 'ten_dia_diem') || data_get($post, 'loai') === 'chia_se')
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1.5 text-sm text-slate-400">
                            @if($isProfileUpdate)
                                <span>{{ $body }}</span>
                            @endif

                            @if (data_get($post, 'cam_xuc') || data_get($post, 'hoat_dong'))
                                @php
                                $camXucLabels = [
                                    'vui_ve' => 'vui vẻ',
                                    'phan_no' => 'phẫn nộ',
                                    'buon' => 'buồn',
                                    'wow' => 'wow',
                                ];
                                @endphp
                                <span>
                                    @if (data_get($post, 'cam_xuc'))
                                        đang cảm thấy <span class="font-medium text-slate-300">{{ $camXucLabels[data_get($post, 'cam_xuc')] ?? strtolower(data_get($post, 'cam_xuc')) }}</span>
                                    @endif
                                    @if (data_get($post, 'hoat_dong'))
                                        {{ strtolower(data_get($post, 'hoat_dong')) }}
                                    @endif
                                </span>
                            @endif

                            @if (data_get($post, 'ten_dia_diem'))
                                <span class="inline-flex items-center gap-0.5">
                                    đang ở <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(data_get($post, 'ten_dia_diem')) }}{{ data_get($post, 'vi_do') && data_get($post, 'kinh_do') ? ',' . data_get($post, 'vi_do') . ',' . data_get($post, 'kinh_do') : '' }}" target="_blank" rel="noopener noreferrer" class="font-medium text-red-400 hover:text-red-300 hover:underline inline-flex items-center gap-0.5 transition-colors">
                                        <span class="material-symbols-outlined text-[16px] text-red-400" style="font-variation-settings: 'FILL' 1;">location_on</span>
                                        {{ data_get($post, 'ten_dia_diem') }}
                                    </a>
                                </span>
                            @endif

                            @if (data_get($post, 'loai') === 'chia_se')
                                <span>
                                    đã chia sẻ một bài viết
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                @if (!$isShared)
                    @if(auth()->id() === data_get($post, 'nguoi_dung_id') || (isset($user) && auth()->id() === data_get($user, 'id')))
                        <div class="relative shrink-0">
                            <button type="button" class="post-dropdown-trigger text-slate-500 transition-colors hover:text-sky-300 p-2 rounded-full hover:bg-white/5" aria-label="Tùy chọn bài viết">
                                <span class="material-symbols-outlined" data-icon="more_horiz">more_horiz</span>
                            </button>
                            <div class="post-dropdown-menu hidden absolute right-0 top-full mt-1 w-40 bg-slate-900 border border-white/10 rounded-xl shadow-2xl overflow-hidden z-20">
                                @if($postId)
                                @if(auth()->id() === (int) data_get($post, 'nguoi_dung_id'))
                                <button type="button" 
                                        class="w-full text-left px-4 py-3 text-sm text-sky-400 hover:bg-white/5 flex items-center gap-2 transition-colors border-b border-white/5 btn-toggle-pin" 
                                        data-post-id="{{ $postId }}"
                                        data-pinned="{{ data_get($post, 'da_ghim') ? '1' : '0' }}">
                                    <span class="material-symbols-outlined text-[18px] pin-icon" style="{{ data_get($post, 'da_ghim') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">
                                        push_pin
                                    </span>
                                    <span class="pin-text">
                                        {{ data_get($post, 'da_ghim') ? (app()->getLocale() === 'en' ? 'Unpin post' : 'Bỏ ghim bài viết') : (app()->getLocale() === 'en' ? 'Pin post' : 'Ghim bài viết') }}
                                    </span>
                                </button>
                                @endif
                                <button type="button" class="w-full text-left px-4 py-3 text-sm text-sky-400 hover:bg-white/5 flex items-center gap-2 transition-colors border-b border-white/5" onclick="window.openEditModal('{{ $postId }}', this.getAttribute('data-post-content'))" data-post-content="{{ data_get($post, 'noi_dung') }}">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                    Chỉnh sửa bài viết
                                </button>
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
                @endif
            </div>

            @if (filled($body) && data_get($post, 'loai') !== 'chia_se' && !$isProfileUpdate)
                @php
                    $formattedContent = data_get($post, 'formatted_content') ?? $body;
                    $plainContent = trim(strip_tags((string) $formattedContent));
                    $plainContentLength = mb_strlen($plainContent);
                    $shouldCollapseContent = $plainContentLength > 60;
                    $contentElementId = 'post-content-' . ($postId ?? uniqid());
                    $previewContent = \Illuminate\Support\Str::limit($plainContent, 60, '...');
                @endphp
                @if($shouldCollapseContent)
                    <p id="{{ $contentElementId }}-preview" class="whitespace-pre-line break-words leading-relaxed text-on-surface-variant">{{ $previewContent }}</p>
                    <p id="{{ $contentElementId }}-full" class="hidden whitespace-pre-line break-words leading-relaxed text-on-surface-variant">{!! $formattedContent !!}</p>
                @else
                    <p id="{{ $contentElementId }}" class="whitespace-pre-line break-words leading-relaxed text-on-surface-variant">{!! $formattedContent !!}</p>
                @endif
                @if($shouldCollapseContent)
                    <button
                        type="button"
                        class="mt-1 text-sm font-semibold text-sky-300 hover:text-sky-200 transition-colors"
                        data-read-more-toggle="1"
                        data-preview-id="{{ $contentElementId }}-preview"
                        data-full-id="{{ $contentElementId }}-full"
                        data-expanded="0"
                    >
                        Xem thêm
                    </button>
                @endif
            @endif

            @if($poll && $pollOptions->isNotEmpty())
                <div class="poll-container mt-4 border border-white/10 rounded-2xl p-4 bg-slate-900/30" data-has-voted="{{ $hasVotedPoll ? '1' : '0' }}">
                    <h3 class="text-base font-semibold mb-3 text-slate-100">{{ data_get($poll, 'cau_hoi') }}</h3>

                    @foreach($pollOptions as $option)
                        @php
                            $optionVotes = collect(data_get($option, 'votes', []))->count();
                            $percentage = $totalPollVotes > 0 ? round(($optionVotes / $totalPollVotes) * 100) : 0;
                            $isSelectedOption = (int) $userVotedOptionId === (int) data_get($option, 'id');
                        @endphp
                        <button
                            type="button"
                            class="poll-option-btn relative overflow-hidden w-full text-left px-3 py-2 mb-2 rounded-xl transition-colors flex justify-between items-center group {{ $hasVotedPoll ? 'bg-slate-800/30 hover:bg-slate-700/60' : 'bg-slate-800/60 hover:bg-slate-700' }}"
                            data-poll-id="{{ data_get($poll, 'id') }}"
                            data-option-id="{{ data_get($option, 'id') }}"
                        >
                            <div class="poll-progress absolute left-0 top-0 bottom-0 {{ $isSelectedOption ? 'bg-sky-500/30' : 'bg-slate-600/40' }} transition-all duration-500 ease-out" style="width: {{ $hasVotedPoll ? $percentage : 0 }}%;"></div>
                            <span class="relative z-10 font-medium {{ $isSelectedOption ? 'text-sky-300' : 'text-slate-200' }}">{{ data_get($option, 'noi_dung') }}</span>
                            @if($hasVotedPoll)
                                <span class="relative z-10 text-sm {{ $isSelectedOption ? 'text-sky-300 font-bold' : 'text-slate-400' }} poll-percentage">{{ $percentage }}%</span>
                            @else
                                <span class="relative z-10 text-xs text-slate-400">Vote</span>
                            @endif
                        </button>
                    @endforeach

                    @if($hasVotedPoll)
                        <div class="text-sm text-slate-400 mt-2 poll-total">{{ $totalPollVotes }} lượt bình chọn</div>
                    @else
                        <div class="text-xs text-slate-500 mt-2">Bình chọn để xem kết quả chi tiết.</div>
                    @endif
                </div>
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
                            $isVideo = $mediaLoai === 'video' || \Illuminate\Support\Str::endsWith(strtolower($mediaPath), ['.mp4', '.webm', '.mov']);
                            $isPdf = \Illuminate\Support\Str::endsWith(strtolower($mediaPath), ['.pdf']);
                        @endphp
                        <div class="overflow-hidden rounded-xl border border-white/10 bg-slate-900/50 {{ $mediaCount > 1 ? 'aspect-square' : '' }}">
                            @if($isVideo)
                                <video src="{{ $mediaSrc }}" controls controlsList="nodownload" muted playsinline loop class="w-full h-full {{ $mediaCount == 1 ? 'max-h-[500px] object-contain block mx-auto' : 'object-cover' }}"></video>
                            @elseif($isPdf)
                                <embed src="{{ $mediaSrc }}" type="application/pdf" class="w-full h-full {{ $mediaCount == 1 ? 'min-h-[500px]' : 'object-cover' }}">
                            @else
                                <img src="{{ $mediaSrc }}" 
                                     alt="Post image" 
                                     data-post-id="{{ $postId }}"
                                     class="post-image-item cursor-pointer hover:opacity-90 transition-opacity w-full h-full {{ $mediaCount == 1 ? 'max-h-[500px] object-contain block mx-auto' : 'object-cover' }}"
                                     onerror="this.outerHTML='<div class=\\'w-full h-full min-h-[200px] flex flex-col items-center justify-center bg-slate-800 text-slate-400 p-4 border border-dashed border-white/20\\'><span class=\\'material-symbols-outlined text-4xl mb-2\\'>image_not_supported</span><span class=\\'text-sm mb-2\\'>Không thể hiển thị định dạng này</span><a href=\\'{{ $mediaSrc }}\\' download target=\\'_blank\\' class=\\'text-emerald-400 hover:underline text-xs\\'>Tải xuống để xem</a></div>'">
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif ($postImage)
                @php
                    $isVideo = \Illuminate\Support\Str::endsWith(strtolower($postImage), ['.mp4', '.webm', '.mov']);
                    $isPdf = \Illuminate\Support\Str::endsWith(strtolower($postImage), ['.pdf']);
                @endphp
                <div class="mt-3 overflow-hidden rounded-2xl border border-white/10 bg-slate-900/50">
                    @if($isVideo)
                        <video class="w-full h-auto max-h-[500px] object-contain block mx-auto" controls controlsList="nodownload" muted playsinline loop src="{{ $postImage }}"></video>
                    @elseif($isPdf)
                        <embed src="{{ $postImage }}" type="application/pdf" class="w-full h-[500px] block mx-auto">
                    @else
                        <img class="post-image-item cursor-pointer hover:opacity-90 transition-opacity w-full h-auto max-h-[500px] object-contain block mx-auto" data-post-id="{{ $postId }}" src="{{ $postImage }}" alt="Hình ảnh bài viết"
                             onerror="this.outerHTML='<div class=\\'w-full h-[300px] flex flex-col items-center justify-center bg-slate-800 text-slate-400 p-4 border border-dashed border-white/20\\'><span class=\\'material-symbols-outlined text-4xl mb-2\\'>image_not_supported</span><span class=\\'text-sm mb-2\\'>Không thể hiển thị định dạng này</span><a href=\\'{{ $postImage }}\\' download target=\\'_blank\\' class=\\'text-emerald-400 hover:underline text-xs\\'>Tải xuống để xem</a></div>'">
                    @endif
                </div>
            @endif

            @php
                $actualOriginal = data_get($post, 'originalPost');
                // Nếu bài gốc vẫn là một bài chia sẻ, ta tìm đến bài gốc cuối cùng (bài viết thực sự)
                while($actualOriginal && $actualOriginal->loai === 'chia_se' && $actualOriginal->bai_goc_id) {
                    $actualOriginal = \App\Models\BaiViet::find($actualOriginal->bai_goc_id);
                }
            @endphp

            @if(data_get($post, 'loai') === 'chia_se' && $actualOriginal)
                <div class="mt-4 border border-white/10 rounded-2xl overflow-hidden relative">
                    <div class="absolute inset-0 bg-slate-900/50 pointer-events-none"></div>
                    <div class="relative z-10 p-1 pointer-events-none">
                        <div class="pointer-events-auto">
                            @include('components.post-card', ['post' => $actualOriginal, 'isShared' => true])
                        </div>
                    </div>
                </div>
            @endif

            @if (!$isShared)
            <div data-reaction-area>
                <!-- Dòng số liệu tương tác (Reactions, Comments, Shares) kiểu Facebook nằm phía TRÊN thanh nút bấm -->
                <div class="flex items-center justify-between mt-4 pb-3 border-b border-white/5 text-xs sm:text-sm text-slate-400 select-none">
                    <button type="button" onclick="window.openReactionsModal('{{ $postId }}')" class="flex items-center gap-1.5 hover:underline cursor-pointer group">
                        <div class="flex -space-x-1 items-center">
                            <span class="material-symbols-outlined text-[15px] sm:text-[16px] text-sky-400 bg-sky-500/20 rounded-full p-0.5" style="font-variation-settings: 'FILL' 1;">thumb_up</span>
                            <span class="material-symbols-outlined text-[15px] sm:text-[16px] text-rose-400 bg-rose-500/20 rounded-full p-0.5" style="font-variation-settings: 'FILL' 1;">favorite</span>
                            <span class="material-symbols-outlined text-[15px] sm:text-[16px] text-yellow-300 bg-yellow-500/20 rounded-full p-0.5" style="font-variation-settings: 'FILL' 1;">mood</span>
                        </div>
                        <span class="font-semibold text-slate-300 group-hover:text-sky-400 transition-colors" data-reaction-count-display="{{ $postId }}"><span data-reaction-count>{{ $reactionCount }}</span> {{ app()->getLocale() === 'en' ? ($reactionCount <= 1 ? 'reaction' : 'reactions') : 'cảm xúc' }}</span>
                    </button>

                    <div class="flex items-center gap-2 text-slate-500">
                        <button type="button" data-comment-toggle class="hover:underline hover:text-sky-300 font-medium text-slate-400 transition-colors">
                            <span data-comment-count-text>{{ $commentCount }}</span> {{ app()->getLocale() === 'en' ? ($commentCount <= 1 ? 'comment' : 'comments') : 'bình luận' }}
                        </button>
                        @if($shareCount > 0)
                            <span>·</span>
                            <span class="font-medium text-slate-400"><span data-share-count-text>{{ $shareCount }}</span> {{ app()->getLocale() === 'en' ? ($shareCount <= 1 ? 'share' : 'shares') : 'lượt chia sẻ' }}</span>
                        @endif
                    </div>
                </div>

                <!-- Thanh nút bấm hành động (Like, Comment, Share, Bookmark) -->
                <div class="flex flex-col gap-3 pt-3 mt-1 text-slate-400">
                    <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1 sm:gap-2">
                            <button type="button" data-reaction-trigger class="group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 transition-all duration-300 {{ $selected ? 'bg-sky-400/10 text-sky-400' : 'text-slate-400 hover:bg-slate-800/60 hover:text-sky-300' }}">
                                <div class="relative flex items-center justify-center transition-transform group-hover:scale-110 group-active:scale-95">
                                    <span class="material-symbols-outlined text-[20px] sm:text-[22px] {{ $selectedColor }}" data-reaction-trigger-icon style="{{ $selected ? 'font-variation-settings: \'FILL\' 1;' : '' }}">{{ $selectedIcon }}</span>
                                </div>
                                <span class="text-[13px] sm:text-sm font-semibold tracking-wide {{ $selected ? 'hidden' : '' }}" data-reaction-trigger-label>{{ $selected ? '' : $selectedLabel }}</span>
                            </button>

                            <button type="button" data-comment-toggle class="group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 text-slate-400 transition-all duration-300 hover:bg-slate-800/60 hover:text-sky-300">
                                <div class="relative flex items-center justify-center transition-transform group-hover:scale-110 group-active:scale-95">
                                    <span class="material-symbols-outlined text-[20px] sm:text-[22px]" data-icon="chat_bubble_outline">chat_bubble</span>
                                </div>
                                <span class="text-[13px] sm:text-sm font-semibold tracking-wide hidden sm:block">{{ __('messages.post_comment') }}</span>
                            </button>

                            @if($hasPersistedPost)
                                <button type="button" data-share-button data-share-url="{{ route('posts.share', ['post' => $postId]) }}" class="group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 text-slate-400 transition-all duration-300 hover:bg-slate-800/60 hover:text-emerald-400">
                                    <div class="relative flex items-center justify-center transition-transform group-hover:scale-110 group-active:scale-95">
                                        <span class="material-symbols-outlined text-[20px] sm:text-[22px]" data-icon="share">share</span>
                                    </div>
                                    <span class="text-[13px] sm:text-sm font-semibold tracking-wide hidden sm:block">{{ __('messages.post_share') }}</span>
                                </button>
                            @endif

                            <button type="button" data-bookmark-button data-post-id="{{ $postId }}" class="group flex items-center gap-1.5 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 text-slate-400 transition-all duration-300 hover:bg-slate-800/60 hover:text-yellow-400">
                                <div class="relative flex items-center justify-center transition-transform group-hover:scale-110 group-active:scale-95">
                                    <span class="material-symbols-outlined text-[20px] sm:text-[22px] {{ $isBookmarked ? 'text-yellow-400' : '' }}" data-bookmark-icon style="{{ $isBookmarked ? 'font-variation-settings: \'FILL\' 1;' : '' }}">bookmark</span>
                                </div>
                                <span class="text-[13px] sm:text-sm font-semibold tracking-wide hidden sm:block" data-bookmark-text>{{ $isBookmarked ? __('messages.post_saved') : __('messages.post_save') }}</span>
                            </button>
                        </div>
                    </div>

                    <div data-reaction-picker class="hidden absolute left-0 bottom-full z-10 mb-2 w-auto rounded-[32px] border border-white/10 bg-slate-950/95 p-2 shadow-[0_12px_35px_rgba(0,0,0,0.25)] backdrop-blur-sm transition-all duration-200">
                        <div class="flex items-center gap-2">
                            @foreach($reactionButtons as $type => $button)
                                <button type="button" data-reaction-option data-reaction="{{ $type }}" data-reaction-label="{{ $button['label'] }}" data-reaction-color="{{ $button['color'] }}" data-reaction-icon="{{ $button['icon'] }}" class="flex items-center justify-center rounded-full bg-slate-900 p-2.5 text-center text-slate-300 transition duration-200 hover:-translate-y-1 hover:bg-sky-400/10 hover:text-sky-300" title="{{ $button['label'] }}">
                                    <span class="material-symbols-outlined {{ $button['color'] }} text-xl">{{ $button['icon'] }}</span>
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
                            <form class="comment-submit-form" method="POST" action="{{ route('posts.comment', ['post' => $postId]) }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="binh_luan_cha_id" value="">
                                <div class="relative">
                                    <textarea name="noi_dung" rows="2" class="w-full bg-transparent border border-white/10 focus:border-sky-400 focus:ring-0 rounded-3xl p-3 pr-12 text-sm text-slate-100 placeholder:text-slate-500" placeholder="{{ __('messages.post_comment_placeholder') }}"></textarea>
                                    <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-sky-400 transition-colors cursor-pointer z-10" onclick="this.closest('form').querySelector('.comment-media-input').click()">
                                        <span class="material-symbols-outlined">image</span>
                                    </button>
                                </div>
                                <input type="file" name="media[]" multiple accept="image/*,video/*,.gif,.webp,.bmp,.svg,.heic,.heif,.pdf,.tiff,.tif" class="hidden comment-media-input" onchange="window.handleCommentMediaSelect(this)">
                                <div class="comment-media-preview hidden mt-3 flex flex-wrap gap-3"></div>
                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <span class="text-xs text-slate-500" data-comment-action>{{ __('messages.post_comment_new') }}</span>
                                    <button type="button" data-comment-cancel class="hidden text-xs text-slate-400 hover:text-white">{{ __('messages.post_comment_cancel') }}</button>
                                    <button type="submit" class="rounded-full bg-sky-400/10 text-sky-300 px-4 py-2 text-sm font-semibold hover:bg-sky-400/20">{{ __('messages.post_comment_send') }}</button>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-dashed border-white/10 bg-slate-950/60 p-3 text-sm text-slate-500">
                                {{ __('messages.post_comment_sample_warning') }}
                            </div>
                        @endif

                        <div data-comment-list class="mt-4 space-y-3 text-slate-300">
                            @php
                                $rootComments = $comments->whereNull('binh_luan_cha_id');
                            @endphp
                            @if($comments->isEmpty())
                                <div data-no-comments class="text-sm text-slate-500">{{ __('messages.post_comment_no_comments') }}</div>
                            @else
                                @foreach($rootComments as $comment)
                                    @include('components.comment-node', ['comment' => $comment])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</article>
