@extends('layouts.app')

@section('title', 'Khám phá')

@section('content')
<div class="max-w-6xl mx-auto p-4 md:p-8 pb-24">
    <!-- Header & Search Bar -->
    <div class="flex flex-col items-center mb-8 w-full max-w-2xl mx-auto">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-extrabold tracking-tight text-white">Khám phá</h1>
            <p class="text-slate-400 mt-1">Tìm kiếm bài viết, hashtag hoặc người dùng từ cộng đồng NHOMJ</p>
        </div>

        <!-- Main Search Form -->
        <form id="explore-search-form" action="{{ route('explore') }}" method="GET" class="w-full">
            <input type="hidden" name="type" id="type-input" value="{{ $type }}">
            <input type="hidden" name="time" id="time-input" value="{{ $time }}">
            <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">

            <div class="relative flex items-center bg-slate-900/80 border border-white/10 rounded-2xl shadow-xl overflow-hidden p-1 backdrop-blur-md focus-within:border-sky-400/50 transition-all duration-300">
                <span class="material-symbols-outlined text-slate-500 pl-3">search</span>
                <input 
                    type="text" 
                    name="search" 
                    id="search-input" 
                    value="{{ $keyword }}" 
                    placeholder="Nhập từ khóa hoặc thẻ hashtag (Ví dụ: đồ án, #DalatWinter)..." 
                    class="w-full bg-transparent border-0 outline-none px-3 py-3 text-sm text-white placeholder:text-slate-500 focus:ring-0"
                >
                <button type="submit" class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-sm rounded-xl transition-all duration-300 active:scale-95 shadow-md shadow-sky-500/20 mr-1">
                    Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    <!-- Filters Section -->
    <div class="glass-panel p-5 rounded-2xl border border-white/10 bg-slate-900/60 backdrop-blur-md mb-8 max-w-4xl mx-auto space-y-4">
        <!-- 1. Kiểu nội dung -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <span class="text-xs font-bold text-slate-400 w-32 shrink-0">Kiểu nội dung:</span>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="selectFilter('type', 'all')" 
                        class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-300 {{ $type === 'all' ? 'bg-sky-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-800/40 text-slate-400 border border-white/5 hover:bg-slate-800/80 hover:text-slate-300' }}">
                    Tất cả
                </button>
                <button type="button" onclick="selectFilter('type', 'hashtag')" 
                        class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-300 {{ $type === 'hashtag' ? 'bg-sky-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-800/40 text-slate-400 border border-white/5 hover:bg-slate-800/80 hover:text-slate-300' }}">
                    Thẻ Hashtag
                </button>
                <button type="button" onclick="selectFilter('type', 'post')" 
                        class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-300 {{ $type === 'post' ? 'bg-sky-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-800/40 text-slate-400 border border-white/5 hover:bg-slate-800/80 hover:text-slate-300' }}">
                    Bài viết
                </button>
                <button type="button" onclick="selectFilter('type', 'user')" 
                        class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-300 {{ $type === 'user' ? 'bg-sky-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-800/40 text-slate-400 border border-white/5 hover:bg-slate-800/80 hover:text-slate-300' }}">
                    Người dùng
                </button>
            </div>
        </div>

        <!-- 2. Thời gian -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 border-t border-white/5 pt-3">
            <span class="text-xs font-bold text-slate-400 w-32 shrink-0">Thời gian:</span>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="selectFilter('time', 'all')" 
                        class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-300 {{ $time === 'all' ? 'bg-sky-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-800/40 text-slate-400 border border-white/5 hover:bg-slate-800/80 hover:text-slate-300' }}">
                    Tất cả
                </button>
                <button type="button" onclick="selectFilter('time', 'today')" 
                        class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-300 {{ $time === 'today' ? 'bg-sky-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-800/40 text-slate-400 border border-white/5 hover:bg-slate-800/80 hover:text-slate-300' }}">
                    Hôm nay
                </button>
                <button type="button" onclick="selectFilter('time', 'week')" 
                        class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-300 {{ $time === 'week' ? 'bg-sky-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-800/40 text-slate-400 border border-white/5 hover:bg-slate-800/80 hover:text-slate-300' }}">
                    Tuần này
                </button>
            </div>
        </div>

        <!-- 3. Mức độ phổ biến -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 border-t border-white/5 pt-3">
            <span class="text-xs font-bold text-slate-400 w-32 shrink-0">Mức độ phổ biến:</span>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="selectFilter('sort', 'popular')" 
                        class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-300 {{ $sort === 'popular' ? 'bg-sky-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-800/40 text-slate-400 border border-white/5 hover:bg-slate-800/80 hover:text-slate-300' }}">
                    Phổ biến
                </button>
                <button type="button" onclick="selectFilter('sort', 'latest')" 
                        class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-300 {{ $sort === 'latest' ? 'bg-sky-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-800/40 text-slate-400 border border-white/5 hover:bg-slate-800/80 hover:text-slate-300' }}">
                    Mới nhất
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Cột trái: Hashtag Phổ biến (1/3 width) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-panel p-5 rounded-2xl border border-white/10 bg-slate-900/60 backdrop-blur-md">
                <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sky-400">trending_up</span>
                    Hashtag Phổ biến
                </h2>

                <div class="space-y-4">
                    @forelse($popularHashtags as $tag)
                        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-white/5 transition-all duration-200">
                            <!-- Thumbnail -->
                            <img class="w-12 h-12 rounded-lg object-cover border border-white/10 shrink-0" 
                                 src="{{ $tag['thumbnail'] }}" 
                                 alt="{{ $tag['ten'] }}">
                            
                            <!-- Tag details -->
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-sm text-sky-400 truncate">#{{ $tag['ten'] }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $tag['so_bai_viet'] }} bài viết</p>
                            </div>

                            <!-- View posts button -->
                            <button type="button" onclick="viewHashtag('{{ $tag['ten'] }}')" 
                                    class="px-3 py-1.5 bg-sky-500/10 hover:bg-sky-500/20 border border-sky-500/20 hover:border-sky-500/30 text-sky-400 text-[11px] font-bold rounded-lg transition-all duration-200 shrink-0">
                                Xem bài viết
                            </button>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 text-center py-4">Chưa có hashtag nào phổ biến</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Cột phải: Kết quả Bài viết (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- If search type is user, show matched users first -->
            @if($type === 'user' && $matchedUsers->count() > 0)
                <div class="glass-panel p-5 rounded-2xl border border-white/10 bg-slate-900/60 backdrop-blur-md space-y-4">
                    <h3 class="text-sm font-bold text-slate-400 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-slate-400 text-sm">group</span>
                        Thành viên tìm thấy ({{ $matchedUsers->count() }})
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($matchedUsers as $mUser)
                            <div class="flex items-center justify-between p-3 bg-white/5 border border-white/5 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <img class="w-10 h-10 rounded-full border border-sky-400/20 object-cover shrink-0" 
                                         src="{{ $mUser->anh_dai_dien ? asset('storage/' . $mUser->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}" 
                                         alt="{{ $mUser->ten_dang_nhap }}">
                                    <div class="min-w-0">
                                        <a href="{{ route('profile.public', ['username' => $mUser->ten_dang_nhap]) }}" class="font-bold text-white hover:text-sky-400 transition-colors text-sm truncate block">
                                            {{ $mUser->ten_hien_thi ?? $mUser->ten_dang_nhap }}
                                        </a>
                                        <p class="text-xs text-slate-400 truncate">{{ '@' . $mUser->ten_dang_nhap }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('profile.public', ['username' => $mUser->ten_dang_nhap]) }}" 
                                   class="px-3 py-1 bg-sky-500/10 text-sky-400 border border-sky-500/20 rounded-full text-xs font-semibold hover:bg-sky-500/20 transition-all shrink-0">
                                    Hồ sơ
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Posts List -->
            @if($posts->count() > 0)
                <div class="space-y-4">
                    @foreach($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="glass-panel flex flex-col items-center justify-center rounded-2xl p-16 text-center border border-white/10 bg-slate-900/60">
                    <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-slate-800/50 text-slate-500 border border-dashed border-slate-700">
                        <span class="material-symbols-outlined text-4xl">search_off</span>
                    </div>
                    <h3 class="text-xl font-bold text-white">Không tìm thấy bài viết nào</h3>
                    <p class="mt-2 text-slate-400 max-w-md mx-auto text-xs">Hãy thử thay đổi từ khóa, kiểm tra chính tả hoặc điều chỉnh bộ lọc tìm kiếm ở trên để thử lại.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function selectFilter(filterName, value) {
    document.getElementById(filterName + '-input').value = value;
    document.getElementById('explore-search-form').submit();
}

function viewHashtag(tagName) {
    document.getElementById('search-input').value = '#' + tagName;
    document.getElementById('type-input').value = 'hashtag';
    document.getElementById('explore-search-form').submit();
}
</script>
@endsection
