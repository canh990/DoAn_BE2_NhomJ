@if(!$isOwnProfile && $user->quyen_rieng_tu === 'rieng_tu' && !$isAcceptedFollower)
<div class="glass-panel flex flex-col items-center justify-center rounded-3xl p-12 text-center h-full min-h-[300px]">
    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-800/50 text-slate-500 border border-slate-700/50">
        <span class="material-symbols-outlined text-4xl" data-icon="lock">lock</span>
    </div>
    <h3 class="text-xl font-bold text-on-surface">Đây là tài khoản riêng tư</h3>
    <p class="mt-2 text-slate-400">Chỉ những người được cấp quyền mới có thể xem nội dung của người dùng này.</p>
</div>
@else
    @if(isset($allMedia) && $allMedia->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($allMedia as $media)
                <div class="aspect-square overflow-hidden rounded-xl bg-slate-800 relative group cursor-pointer shadow-lg border border-white/5">
                    @if($media->loai === 'video')
                        <video class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" 
                               src="{{ asset('storage/' . $media->duong_dan) }}" 
                               preload="metadata" 
                               onclick="this.controls=true; this.play(); this.nextElementSibling.style.display='none';"></video>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/30 pointer-events-none transition-all group-hover:bg-black/10">
                            <span class="material-symbols-outlined text-white text-5xl drop-shadow-lg" data-icon="play_circle">play_circle</span>
                        </div>
                    @else
                        <img class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" src="{{ asset('storage/' . $media->duong_dan) }}" alt="Ảnh bài viết" />
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            {{ $allMedia->appends(['tab' => 'media'])->links() }}
        </div>
    @else
        <div class="glass-panel flex flex-col items-center justify-center rounded-3xl p-12 text-center">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-800/50 text-slate-500">
                <span class="material-symbols-outlined text-4xl" data-icon="perm_media">perm_media</span>
            </div>
            <h3 class="text-xl font-bold text-on-surface">Chưa có phương tiện nào</h3>
            <p class="mt-2 text-slate-400">Người dùng này chưa đăng tải bất kỳ hình ảnh hay video nào.</p>
        </div>
    @endif
@endif
