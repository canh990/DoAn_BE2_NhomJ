@php
    use Illuminate\Support\Arr;

    $comments = $comments ?? collect();
    $rootComments = $comments->whereNull('binh_luan_cha_id');
@endphp

<div data-comment-thread class="mt-4 space-y-3 text-slate-300">
    @if($comments->isEmpty())
        <div data-no-comments class="text-sm text-slate-500">Chưa có bình luận nào. Hãy là người đầu tiên bình luận.</div>
    @else
        @foreach($rootComments as $comment)
            <div class="rounded-2xl border border-white/10 bg-slate-950 p-3" data-comment-id="{{ $comment->id }}">
                <div class="flex gap-3 items-start">
                    <img class="w-8 h-8 rounded-full object-cover border border-slate-700"
                         src="{{ $comment->user && $comment->user->anh_dai_dien ? asset('storage/' . $comment->user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}"
                         alt="{{ $comment->user?->name ?? 'Người dùng' }}">

                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-2 text-sm text-slate-200">
                            <span class="font-semibold">{{ $comment->user?->name ?? 'Người dùng' }}</span>
                            <span class="text-xs text-slate-500">{{ $comment->ngay_tao?->diffForHumans() ?? '' }}</span>
                        </div>

                        <p class="mt-1 text-sm leading-relaxed text-slate-300">{{ $comment->noi_dung }}</p>

                        <div class="mt-3 flex items-center gap-3 text-xs text-slate-400">
                            <button type="button"
                                    data-comment-reply-button
                                    data-comment-id="{{ $comment->id }}"
                                    data-comment-user="{{ $comment->user?->name ?? 'Người dùng' }}"
                                    class="hover:text-sky-300">
                                Trả lời
                            </button>
                        </div>

                        <div class="mt-3 space-y-3 pl-10" data-comment-replies>
                            @foreach($comment->children as $reply)
                                <div class="rounded-2xl border border-white/10 bg-slate-950 p-3" data-comment-id="{{ $reply->id }}">
                                    <div class="flex gap-3 items-start">
                                        <img class="w-8 h-8 rounded-full object-cover border border-slate-700"
                                             src="{{ $reply->user && $reply->user->anh_dai_dien ? asset('storage/' . $reply->user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}"
                                             alt="{{ $reply->user?->name ?? 'Người dùng' }}">

                                        <div class="flex-1">
                                            <div class="flex items-center justify-between gap-2 text-sm text-slate-200">
                                                <span class="font-semibold">{{ $reply->user?->name ?? 'Người dùng' }}</span>
                                                <span class="text-xs text-slate-500">{{ $reply->ngay_tao?->diffForHumans() ?? '' }}</span>
                                            </div>

                                            <p class="mt-1 text-sm leading-relaxed text-slate-300">{{ $reply->noi_dung }}</p>

                                            <div class="mt-3 flex items-center gap-3 text-xs text-slate-400">
                                                <button type="button"
                                                        data-comment-reply-button
                                                        data-comment-id="{{ $reply->id }}"
                                                        data-comment-user="{{ $reply->user?->name ?? 'Người dùng' }}"
                                                        class="hover:text-sky-300">
                                                    Trả lời
                                                </button>
                                            </div>

                                            <div class="mt-3 space-y-3 pl-10" data-comment-replies></div>
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
