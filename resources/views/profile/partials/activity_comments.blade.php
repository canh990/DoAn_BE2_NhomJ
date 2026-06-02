@foreach($myComments as $comment)
    @if($comment->post)
        <a href="{{ route('posts.show', $comment->bai_viet_id) }}#comment-{{ $comment->id }}" class="flex items-start gap-4 p-4 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/5 transition-all group">
            <div class="w-10 h-10 rounded-full bg-sky-500/10 text-sky-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[20px]">chat_bubble</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white">Bạn đã bình luận trên bài viết của {{ $comment->post->user->name ?? 'Người dùng' }}</p>
                <div class="mt-2 p-3 rounded-xl bg-slate-950/40 text-xs text-slate-300 border border-white/5 italic">
                    "{{ $comment->noi_dung }}"
                </div>
                <span class="text-[10px] text-slate-500 block mt-2">
                    {{ $comment->ngay_tao ? \Carbon\Carbon::parse($comment->ngay_tao)->diffForHumans() : '' }}
                </span>
            </div>
        </a>
    @endif
@endforeach
