@extends('layouts.app')

@section('title', 'Thông báo - NHOMJ')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-sky-400 to-purple-400 bg-clip-text text-transparent">Thông báo</h1>
            <p class="text-slate-500 text-sm mt-1">Luôn cập nhật những tương tác mới nhất</p>
        </div>
        <div class="flex gap-2">
            @if($notifications->count() > 0)
                <button id="clear-all-notifications" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-400/10 rounded-xl transition-all" title="Xóa tất cả thông báo">
                    <span class="material-symbols-outlined">delete_sweep</span>
                </button>
                @if($notifications->where('da_doc', false)->count() > 0)
                    <button id="mark-all-read" class="p-2 text-slate-400 hover:text-sky-400 hover:bg-sky-400/10 rounded-xl transition-all" title="Đánh dấu tất cả là đã đọc">
                        <span class="material-symbols-outlined">done_all</span>
                    </button>
                @endif
            @endif
        </div>
    </div>

    <div class="space-y-3" id="notification-list">
        @forelse($notifications as $notification)
            @php
                $redirectUrl = '#';
                switch($notification->loai) {
                    case 'thich':
                    case 'binh_luan':
                    case 'chia_se':
                    case 'dang_bai':
                        $redirectUrl = $notification->bai_viet_id ? route('posts.show', $notification->bai_viet_id) : '#';
                        break;
                    case 'theo_doi':
                        $redirectUrl = route('profile.public', $notification->nguoiThucHien->ten_dang_nhap);
                        break;
                    case 'tin_nhan':
                        $redirectUrl = route('chat.demo', ['user_id' => $notification->nguoi_thuc_hien_id]);
                        break;
                    case 'dang_tin':
                        $redirectUrl = route('home'); // Stories usually on home
                        break;
                }
            @endphp
            <div class="notification-item group relative glass-panel rounded-2xl p-4 transition-all hover:bg-white/5 hover:border-sky-400/30 cursor-pointer {{ $notification->da_doc ? 'opacity-80' : 'border-l-4 border-sky-400' }}" 
                 data-id="{{ $notification->id }}"
                 onclick="handleNotificationClick(event, '{{ $redirectUrl }}')">
                
                <div class="flex gap-4 relative z-10">
                    {{-- Actor Avatar --}}
                    <div class="shrink-0 relative">
                        <a href="{{ route('profile.public', $notification->nguoiThucHien->ten_dang_nhap) }}" class="block" onclick="event.stopPropagation()">
                            <img src="{{ $notification->nguoiThucHien->anh_dai_dien ? asset('storage/' . $notification->nguoiThucHien->anh_dai_dien) : 'https://ui-avatars.com/api/?name='.urlencode($notification->nguoiThucHien->name).'&background=random' }}" 
                                 alt="{{ $notification->nguoiThucHien->name }}"
                                 class="w-12 h-12 rounded-full object-cover border border-sky-400/20 shadow-sm">
                        </a>
                        {{-- Icon badge for notification type --}}
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full flex items-center justify-center border-2 border-[#0a0e1a] 
                            @switch($notification->loai)
                                @case('thich') bg-rose-500 @break
                                @case('binh_luan') bg-sky-500 @break
                                @case('theo_doi') bg-purple-500 @break
                                @case('chia_se') bg-emerald-500 @break
                                @default bg-slate-500
                            @endswitch">
                            <span class="material-symbols-outlined text-white text-[14px]">
                                @switch($notification->loai)
                                    @case('thich') favorite @break
                                    @case('binh_luan') chat_bubble @break
                                    @case('theo_doi') person_add @break
                                    @case('chia_se') share @break
                                    @case('tin_nhan') mail @break
                                    @case('dang_bai') article @break
                                    @default notifications
                                @endswitch
                            </span>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="text-sm text-slate-300 leading-relaxed">
                            <a href="{{ route('profile.public', $notification->nguoiThucHien->ten_dang_nhap) }}" class="font-bold text-white hover:text-sky-400 transition-colors" onclick="event.stopPropagation()">
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
                        </div>
                        
                        <div class="flex items-center gap-3 mt-1.5">
                            <span class="text-[11px] text-slate-500 font-medium">{{ $notification->ngay_tao->diffForHumans() }}</span>
                            @if(!$notification->da_doc)
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(125,211,252,0.5)]"></span>
                            @endif
                        </div>

                        {{-- Action Preview (if post or comment) --}}
                        @if($notification->loai === 'binh_luan' && $notification->binhLuan)
                            <div class="mt-2.5 p-3 rounded-xl bg-white/5 border border-white/5 text-xs text-slate-400 italic line-clamp-2">
                                "{{ $notification->binhLuan->noi_dung }}"
                            </div>
                        @endif
                    </div>

                    {{-- Post Thumbnail --}}
                    @if($notification->baiViet && $notification->baiViet->media->count() > 0)
                        <div class="shrink-0 w-14 h-14 rounded-xl overflow-hidden border border-white/10 group-hover:border-sky-400/30 transition-colors">
                            @php $firstMedia = $notification->baiViet->media->first(); @endphp
                            @if($firstMedia->loai === 'hinh_anh')
                                <img src="{{ asset('storage/' . $firstMedia->duong_dan) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-slate-900 flex items-center justify-center relative">
                                    <video src="{{ asset('storage/' . $firstMedia->duong_dan) }}" class="w-full h-full object-cover opacity-50"></video>
                                    <span class="material-symbols-outlined absolute text-white text-lg">play_circle</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="shrink-0 flex flex-col gap-1">
                        @if(!$notification->da_doc)
                            <button class="mark-read-btn p-1.5 text-slate-500 hover:text-sky-400 hover:bg-sky-400/10 rounded-lg transition-all opacity-0 group-hover:opacity-100" title="Đánh dấu đã đọc" onclick="markAsRead(event, {{ $notification->id }})">
                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            </button>
                        @endif
                        <button class="delete-notification-btn p-1.5 text-slate-500 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all opacity-0 group-hover:opacity-100" title="Xóa thông báo" onclick="deleteNotification(event, {{ $notification->id }})">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-24 h-24 rounded-full bg-sky-400/5 flex items-center justify-center mb-6 border border-sky-400/10">
                    <span class="material-symbols-outlined text-5xl text-sky-400/20">notifications_off</span>
                </div>
                <h3 class="text-xl font-bold text-slate-300">Không có thông báo nào</h3>
                <p class="text-slate-500 text-sm mt-2 max-w-xs mx-auto">Chúng tôi sẽ thông báo cho bạn khi có hoạt động mới diễn ra.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-10">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

<script>
    function handleNotificationClick(event, url) {
        if (url && url !== '#') {
            window.location.href = url;
        }
    }

    function markAsRead(event, id) {
        event.stopPropagation();
        const item = event.target.closest('.notification-item');
        
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
                item.classList.remove('border-l-4', 'border-sky-400');
                item.classList.add('opacity-80');
                const readBtn = item.querySelector('.mark-read-btn');
                if(readBtn) readBtn.remove();
                if(window.updateGlobalNotificationCount) window.updateGlobalNotificationCount();
            }
        });
    }

    function deleteNotification(event, id) {
        event.stopPropagation();
        const item = event.target.closest('.notification-item');
        
        item.classList.add('removing');
        
        setTimeout(() => {
            fetch(`/notifications/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    item.remove();
                    if(document.querySelectorAll('.notification-item').length === 0) {
                        location.reload(); // Show empty state
                    }
                    if(window.updateGlobalNotificationCount) window.updateGlobalNotificationCount();
                } else {
                    item.classList.remove('removing');
                }
            })
            .catch(() => item.classList.remove('removing'));
        }, 300);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const markAllBtn = document.getElementById('mark-all-read');
        if(markAllBtn) {
            markAllBtn.addEventListener('click', function() {
                fetch('{{ route('notifications.markAllRead') }}', {
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
                            item.classList.remove('border-l-4', 'border-sky-400');
                            item.classList.add('opacity-80');
                            const btn = item.querySelector('.mark-read-btn');
                            if(btn) btn.remove();
                        });
                        this.remove();
                        if(window.updateGlobalNotificationCount) window.updateGlobalNotificationCount();
                    }
                });
            });
        }

        const clearAllBtn = document.getElementById('clear-all-notifications');
        if(clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                if(confirm('Xóa tất cả thông báo? Thao tác này không thể hoàn tác.')) {
                    fetch('{{ route('notifications.deleteAll') }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            document.getElementById('notification-list').innerHTML = '';
                            location.reload();
                        }
                    });
                }
            });
        }
    });
</script>
@endsection
