@if(!$isOwnProfile && $user->quyen_rieng_tu === 'rieng_tu' && !$isAcceptedFollower)
<div class="glass-panel flex flex-col items-center justify-center rounded-3xl p-12 text-center h-full min-h-[300px]">
    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-800/50 text-slate-500 border border-slate-700/50">
        <span class="material-symbols-outlined text-4xl" data-icon="lock">lock</span>
    </div>
    <h3 class="text-xl font-bold text-on-surface">Đây là tài khoản riêng tư</h3>
    <p class="mt-2 text-slate-400">Chỉ những người được cấp quyền mới có thể xem nội dung của người dùng này.</p>
</div>
@else
    @if(isset($posts) && $posts->count() > 0)
        @foreach($posts as $post)
            <x-post-card :post="$post" />
        @endforeach
    @else
        <div class="glass-panel flex flex-col items-center justify-center rounded-3xl p-12 text-center">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-800/50 text-slate-500">
                <span class="material-symbols-outlined text-4xl" data-icon="post_add">post_add</span>
            </div>
            <h3 class="text-xl font-bold text-on-surface">Chưa có bài đăng nào</h3>
            <p class="mt-2 text-slate-400">Người dùng này vẫn chưa chia sẻ bài viết nào với cộng đồng.</p>
        </div>
    @endif
@endif
