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
                
                @php
                    $mediaItems = $comment->media ?? collect();
                    $mediaCount = $mediaItems->count();
                @endphp
                @if ($mediaCount > 0)
                    <div class="mt-2 grid gap-2 {{ $mediaCount == 1 ? 'grid-cols-1' : ($mediaCount == 2 ? 'grid-cols-2' : 'grid-cols-2 sm:grid-cols-3') }} max-w-sm">
                        @foreach($mediaItems as $media)
                            @php
                                $mediaSrc = asset('storage/' . $media->duong_dan);
                                $isVideo = $media->loai === 'video';
                            @endphp
                            <div class="overflow-hidden rounded-xl border border-white/10 bg-slate-900/50 {{ $mediaCount > 1 ? 'aspect-square' : '' }}">
                                @if($isVideo)
                                    <video src="{{ $mediaSrc }}" controls controlsList="nodownload" muted playsinline loop class="w-full h-full {{ $mediaCount == 1 ? 'max-h-[300px] object-contain block' : 'object-cover' }}"></video>
                                @else
                                    <img src="{{ $mediaSrc }}" alt="Comment media" data-post-id="comment-{{ $comment->id }}" class="post-image-item cursor-pointer hover:opacity-90 transition-opacity w-full h-full {{ $mediaCount == 1 ? 'max-h-[300px] object-contain block' : 'object-cover' }}">
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <div class="mt-3 flex items-center gap-3 text-xs text-slate-400">
                    <button type="button" data-comment-reply-button data-comment-id="{{ $comment->id }}" data-comment-user="{{ $comment->user?->name ?? 'Người dùng' }}" class="hover:text-sky-300">Trả lời</button>
                    
                    @if(auth()->check() && (auth()->id() === $comment->nguoi_dung_id || auth()->id() === optional($comment->post)->nguoi_dung_id))
                        <button type="button" class="hover:text-red-400 transition-colors" onclick="if(confirm('Bạn chắc chắn muốn xóa bình luận này? Thao tác này sẽ xoá luôn các ảnh/video đính kèm.')) {
                            fetch('{{ route('comments.destroy', $comment->id) }}', {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    const thread = document.querySelector('.comment-thread[data-comment-id=\'{{ $comment->id }}\']');
                                    if(thread) thread.remove();
                                    if(typeof window.showToast === 'function') {
                                        window.showToast('Bình luận đã được xoá', 'success');
                                    } else {
                                        alert('Đã xoá bình luận thành công!');
                                    }
                                } else {
                                    if(typeof window.showToast === 'function') {
                                        window.showToast(data.message || 'Có lỗi xảy ra', 'error');
                                    } else {
                                        alert(data.message || 'Có lỗi xảy ra.');
                                    }
                                }
                            })
                            .catch(err => {
                                if(typeof window.showToast === 'function') {
                                    window.showToast('Lỗi kết nối khi xoá bình luận.', 'error');
                                } else {
                                    alert('Có lỗi xảy ra.');
                                }
                            });
                        }">Xóa</button>
                    @endif
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
