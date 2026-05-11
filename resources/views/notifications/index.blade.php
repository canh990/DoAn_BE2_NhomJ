@extends('layouts.app')

@section('title', 'Thông báo - NHOMJ')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-sky-400 to-purple-400 bg-clip-text text-transparent">Thông báo</h1>
        @if($notifications->where('da_doc', false)->count() > 0)
            <button id="mark-all-read" class="text-sm font-medium text-sky-400 hover:text-sky-300 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">done_all</span>
                Đánh dấu tất cả là đã đọc
            </button>
        @endif
    </div>

    <div class="space-y-4">
        @forelse($notifications as $notification)
            <div class="notification-item group relative glass-panel rounded-2xl p-4 transition-all hover:bg-white/5 border-l-4 {{ $notification->da_doc ? 'border-transparent' : 'border-sky-400' }}" data-id="{{ $notification->id }}">
                <div class="flex gap-4">
                    {{-- Actor Avatar --}}
                    <div class="shrink-0">
                        <a href="{{ route('profile.public', $notification->nguoiThucHien->ten_dang_nhap) }}">
                            <img src="{{ $notification->nguoiThucHien->anh_dai_dien ? asset('storage/' . $notification->nguoiThucHien->anh_dai_dien) : 'https://ui-avatars.com/api/?name='.urlencode($notification->nguoiThucHien->name).'&background=random' }}" 
                                 alt="{{ $notification->nguoiThucHien->name }}"
                                 class="w-12 h-12 rounded-full object-cover border border-sky-400/20">
                        </a>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-300 leading-relaxed">
                            <a href="{{ route('profile.public', $notification->nguoiThucHien->ten_dang_nhap) }}" class="font-bold text-white hover:text-sky-400 transition-colors">
                                {{ $notification->nguoiThucHien->name }}
                            </a>
                            
                            @switch($notification->loai)
                                @case('thich')
                                    đã thích bài viết của bạn.
                                    @break
                                @case('binh_luan')
                                    @if($notification->binhLuan && $notification->binhLuan->binh_luan_cha_id)
                                        đã trả lời bình luận của bạn.
                                    @else
                                        đã bình luận về bài viết của bạn.
                                    @endif
                                    @break
                                @case('theo_doi')
                                    đã bắt đầu theo dõi bạn.
                                    @break
                                @case('chia_se')
                                    đã chia sẻ bài viết của bạn.
                                    @break
                                @case('tin_nhan')
                                    đã gửi cho bạn một tin nhắn mới.
                                    @break
                                @case('dang_bai')
                                    vừa đăng một bài viết mới.
                                    @break
                                @case('dang_tin')
                                    vừa đăng một tin mới.
                                    @break
                                @case('he_thong')
                                    <span class="text-sky-400 font-bold">[Hệ thống]</span> {{ $notification->noi_dung ?? 'Thông báo từ hệ thống.' }}
                                    @break
                                @default
                                    đã tương tác với bạn.
                            @endswitch
                        </p>
                        
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xs text-slate-500">{{ $notification->ngay_tao->diffForHumans() }}</span>
                            @if(!$notification->da_doc)
                                <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                            @endif
                        </div>

                        {{-- Action Preview (if post or comment) --}}
                        @if($notification->loai === 'binh_luan' && $notification->binhLuan)
                            <div class="mt-2 p-2 rounded-xl bg-white/5 border border-white/10 text-xs text-slate-400 italic">
                                "{{ Str::limit($notification->binhLuan->noi_dung, 100) }}"
                            </div>
                        @endif
                    </div>

                    {{-- Post Thumbnail --}}
                    @if($notification->baiViet && $notification->baiViet->media->count() > 0)
                        <div class="shrink-0 w-12 h-12 rounded-lg overflow-hidden border border-white/10">
                            @php $firstMedia = $notification->baiViet->media->first(); @endphp
                            @if($firstMedia->loai === 'hinh_anh')
                                <img src="{{ asset('storage/' . $firstMedia->duong_dan) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-slate-800 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-sm">videocam</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Mark as read button overlay if unread --}}
                @if(!$notification->da_doc)
                    <button class="mark-read-btn absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity p-1 text-slate-500 hover:text-sky-400" title="Đánh dấu là đã đọc">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                    </button>
                @endif
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 rounded-full bg-sky-400/10 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-4xl text-sky-400/40">notifications_off</span>
                </div>
                <h3 class="text-lg font-medium text-slate-300">Chưa có thông báo nào</h3>
                <p class="text-slate-500 text-sm mt-1">Khi có ai đó tương tác với bạn, thông báo sẽ xuất hiện ở đây.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $notifications->links() }}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark as read
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const item = this.closest('.notification-item');
            const id = item.dataset.id;
            
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    item.classList.remove('border-sky-400');
                    item.classList.add('border-transparent');
                    this.remove();
                    // Update global count if needed
                    updateGlobalNotificationCount();
                }
            });
        });
    });

    // Mark all as read
    const markAllBtn = document.getElementById('mark-all-read');
    if(markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    document.querySelectorAll('.notification-item').forEach(item => {
                        item.classList.remove('border-sky-400');
                        item.classList.add('border-transparent');
                        const btn = item.querySelector('.mark-read-btn');
                        if(btn) btn.remove();
                    });
                    this.remove();
                    updateGlobalNotificationCount();
                }
            });
        });
    }

    function updateGlobalNotificationCount() {
        fetch('/notifications/unread-count')
        .then(res => res.json())
        .then(data => {
            const badges = document.querySelectorAll('.notification-badge');
            badges.forEach(badge => {
                if(data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        });
    }
});
</script>
@endsection
