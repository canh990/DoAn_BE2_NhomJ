<div class="comment-thread w-full" data-comment-id="{{ $comment->id }}">
    <div class="rounded-2xl border border-white/10 bg-slate-950 p-3">
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
            </div>
        </div>
    </div>
    
    <div class="mt-2 space-y-2 pl-6 sm:pl-12 relative" data-comment-replies>
        @if($comment->nestedChildren && $comment->nestedChildren->count() > 0)
            <div class="absolute left-[15px] sm:left-[27px] top-0 bottom-0 w-px bg-white/10"></div>
            @foreach($comment->nestedChildren as $reply)
                @include('components.comment-node', ['comment' => $reply])
            @endforeach
        @endif
    </div>
</div>
