@props(['post'])

@php
    $author = $post->user ?? $post->author ?? auth()->user();
    $avatar = !empty($author->avatar)
        ? asset('storage/' . $author->avatar)
        : 'https://lh3.googleusercontent.com/aida-public/AB6AXuAE8EPzz-gX79DnqAhi0_StOHC91uLm5YDBZwVLWbndwUQ6uK_rUjvdGCmWgdMz8vhDT_KZFa7NE8T8ihfKelL_dO6jLGlJ8sd5AE6svxEDyG59LqoA7KF1QD7pTUv6D9M81ss6aD-J7fp3RxaxKdLt7IZjLiJaECpsYmxZooT54hRgR9bp_99vkrKdEiEEJLPZHCE2LjfSk9G8-idX4qneAxORxh9pxv-y-X3poNr_QVPvLMaqwrEV3YZPUe4RW_tg-_TiSfiyeV4';
    $authorName = $author->name ?? ($author->username ?? 'Người dùng');
    $authorHandle = '@' . ($author->username ?? strtolower(str_replace(' ', '', $authorName)));
    $text = $post->content ?? $post->body ?? $post->excerpt ?? $post->title ?? 'Bài viết chưa có nội dung.';
    $image = $post->image ?? $post->thumbnail ?? null;
    $timeAgo = isset($post->created_at) ? $post->created_at->diffForHumans() : 'vừa xong';
@endphp

<article class="glass-panel rounded-2xl p-6 hover:border-sky-400/30 transition-all group">
    <div class="flex gap-4">
        <img class="w-12 h-12 rounded-full border border-sky-400/20 shrink-0"
             src="{{ $avatar }}"
             alt="{{ $authorName }}" />

        <div class="flex-1 space-y-3">
            <div class="flex justify-between items-start">
                <div>
                    <span class="font-bold text-on-surface hover:text-sky-300 cursor-pointer">{{ $authorName }}</span>
                    <span class="text-slate-500 text-sm ml-2">{{ $authorHandle }} · {{ $timeAgo }}</span>
                </div>
                <button class="text-slate-500 hover:text-sky-300"><span class="material-symbols-outlined" data-icon="more_horiz">more_horiz</span></button>
            </div>

            <p class="text-on-surface-variant leading-relaxed">{{ $text }}</p>

            @if($image)
                <div class="rounded-2xl overflow-hidden border border-sky-400/10 mt-3 aspect-video bg-slate-900">
                    <img class="w-full h-full object-cover" src="{{ strpos($image, 'http') === 0 ? $image : asset('storage/' . $image) }}" alt="Post media" />
                </div>
            @endif

            <div class="flex items-center justify-between pt-3 text-slate-400 max-w-sm">
                <button class="flex items-center gap-2 hover:text-sky-300 transition-colors group/btn"><span class="material-symbols-outlined text-xl group-hover/btn:bg-sky-400/10 p-2 rounded-full" data-icon="chat_bubble">chat_bubble</span><span class="text-sm">{{ $post->comments_count ?? 0 }}</span></button>
                <button class="flex items-center gap-2 hover:text-emerald-400 transition-colors group/btn"><span class="material-symbols-outlined text-xl group-hover/btn:bg-emerald-400/10 p-2 rounded-full" data-icon="repost">retweet</span><span class="text-sm">{{ $post->shares_count ?? 0 }}</span></button>
                <button class="flex items-center gap-2 hover:text-rose-400 transition-colors group/btn"><span class="material-symbols-outlined text-xl group-hover/btn:bg-rose-400/10 p-2 rounded-full" data-icon="favorite">favorite</span><span class="text-sm">{{ $post->likes_count ?? 0 }}</span></button>
                <button class="flex items-center gap-2 hover:text-sky-300 transition-colors group/btn"><span class="material-symbols-outlined text-xl group-hover/btn:bg-sky-400/10 p-2 rounded-full" data-icon="bar_chart">bar_chart</span><span class="text-sm">{{ $post->views_count ?? 0 }}</span></button>
            </div>
        </div>
    </div>
</article>
