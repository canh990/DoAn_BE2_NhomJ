@forelse($posts as $post)
    <x-post-card :post="$post" class="mb-6" />
@empty
    @if(($feedType ?? 'recommend') === 'following')
        <div class="glass-panel flex flex-col items-center justify-center rounded-2xl p-16 text-center border border-white/10 bg-slate-900/60">
            <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-slate-800/50 text-slate-500 border border-dashed border-slate-700">
                <span class="material-symbols-outlined text-4xl">group</span>
            </div>
            <h3 class="text-xl font-bold text-white">{{ __('messages.explore_no_results') }}</h3>
            <p class="mt-2 text-slate-400 max-w-md mx-auto text-xs">Bạn chưa theo dõi ai hoặc những người bạn theo dõi chưa đăng bài viết nào. Hãy đi tìm thêm bạn bè hoặc xem Tab Dành cho bạn!</p>
            <a href="{{ route('explore') }}" class="mt-6 px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-sm rounded-xl transition-all duration-300 active:scale-95 shadow-md shadow-sky-500/20">
                {{ __('messages.home_explore_members') }}
            </a>
        </div>
    @else
        <div class="glass-panel rounded-2xl p-12 text-center text-slate-400 border border-white/10 bg-slate-900/60">
            <p class="text-sm">Chưa có bài viết nào. Hãy là người đầu tiên đăng trạng thái!</p>
        </div>
    @endif
@endforelse

@if($posts->hasPages())
    <div class="mt-8 posts-pagination">
        {{ $posts->links() }}
    </div>
@endif
