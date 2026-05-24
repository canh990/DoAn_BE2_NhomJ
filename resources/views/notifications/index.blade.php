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
                    case 'tag':
                    case 'tag_all':
                        if ($notification->bai_viet_id) {
                            $redirectUrl = route('posts.show', $notification->bai_viet_id);
                            if ($notification->binh_luan_id) {
                                $redirectUrl .= '#comment-' . $notification->binh_luan_id;
                            }
                        }
                        break;
                    case 'theo_doi':
                        $redirectUrl = route('profile.public', $notification->nguoiThucHien->ten_dang_nhap);
                        break;
                    case 'tin_nhan':
                    case 'ket_ban':
                        $redirectUrl = route('chat.demo', ['user_id' => $notification->nguoi_thuc_hien_id]);
                        break;
                    case 'tin_nhan_nhom':
                        $redirectUrl = $notification->cuoc_tro_chuyen_id ? route('chat.groups.index', ['group_id' => $notification->cuoc_tro_chuyen_id]) : '#';
                        break;
                    case 'dang_tin':
                        $redirectUrl = route('home'); // Stories usually on home
                        break;
                }
            @endphp
            <div class="notification-item group relative glass-panel rounded-2xl p-4 transition-all hover:bg-white/5 hover:border-sky-400/30 cursor-pointer {{ $notification->da_doc ? 'opacity-80' : 'border-l-4 border-sky-400' }}" 
                 data-id="{{ $notification->id }}"
                 onclick="handleNotificationClick(event, '{{ $notification->id }}', '{{ $redirectUrl }}', {{ $notification->da_doc ? 'true' : 'false' }})">
                
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
                                @case('theo_doi') @case('ket_ban') bg-purple-500 @break
                                @case('chia_se') bg-emerald-500 @break
                                @case('tag') @case('tag_all') bg-amber-500 @break
                                @case('ghim_binh_luan') bg-yellow-500 @break
                                @case('tin_nhan') @case('tin_nhan_nhom') bg-indigo-500 @break
                                @default bg-slate-500
                            @endswitch">
                            <span class="material-symbols-outlined text-white text-[14px]">
                                @switch($notification->loai)
                                    @case('thich') favorite @break
                                    @case('binh_luan') chat_bubble @break
                                    @case('theo_doi') person_add @break
                                    @case('ket_ban') handshake @break
                                    @case('chia_se') share @break
                                    @case('tin_nhan') mail @break
                                    @case('tin_nhan_nhom') forum @break
                                    @case('tag') alternate_email @break
                                    @case('tag_all') groups @break
                                    @case('ghim_binh_luan') push_pin @break
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
                                @case('ket_ban')
                                    đã trở thành bạn bè với bạn.
                                    @break
                                @case('chia_se')
                                    đã chia sẻ bài viết của bạn.
                                    @break
                                @case('tin_nhan')
                                    đã gửi cho bạn một tin nhắn mới.
                                    @break
                                @case('tin_nhan_nhom')
                                    đã gửi tin nhắn mới trong nhóm chat.
                                    @break
                                @case('tag')
                                    @if($notification->binh_luan_id)
                                        đã nhắc đến bạn trong một bình luận.
                                    @else
                                        đã nhắc đến bạn trong một bài viết.
                                    @endif
                                    @break
                                @case('tag_all')
                                    @if($notification->binh_luan_id)
                                        đã nhắc đến mọi người trong một bình luận.
                                    @else
                                        đã nhắc đến mọi người trong một bài viết.
                                    @endif
                                    @break
                                @case('ghim_binh_luan')
                                    đã ghim bình luận của bạn.
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
                                <span class="unread-dot w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(125,211,252,0.5)]"></span>
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
    // Cập nhật số thông báo chưa đọc realtime trên giao diện
    function decrementBadgeCount(amount = 1) {
        const badges = document.querySelectorAll('.notification-badge');
        badges.forEach(badge => {
            let count = parseInt(badge.textContent);
            if (!isNaN(count)) {
                count = Math.max(0, count - amount);
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                    badge.textContent = '0';
                }
            }
        });
    }

    function handleNotificationClick(event, id, url, daDoc) {
        // Nếu click trúng link cá nhân, nút check hoặc nút xóa thì không xử lý click dòng
        if (event.target.closest('a') || event.target.closest('button')) {
            return;
        }

        const openInNewTab = event.ctrlKey || event.metaKey || event.button === 1;

        if (daDoc) {
            if (url && url !== '#') {
                if (openInNewTab) {
                    window.open(url, '_blank');
                } else {
                    window.location.href = url;
                }
            }
            return;
        }

        // Cập nhật giao diện lập tức (Realtime)
        const item = event.currentTarget;
        if (item) {
            item.classList.remove('border-l-4', 'border-sky-400');
            item.classList.add('opacity-80');
            const readBtn = item.querySelector('.mark-read-btn');
            if (readBtn) readBtn.remove();
            const unreadDot = item.querySelector('.unread-dot');
            if (unreadDot) unreadDot.remove();
        }

        // Giảm badge chưa đọc realtime
        decrementBadgeCount(1);

        // Gửi fetch API đánh dấu đã đọc
        const fetchPromise = fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(() => {
            if (typeof updateGlobalNotificationCount === 'function') {
                updateGlobalNotificationCount();
            }
        });

        if (url && url !== '#') {
            if (openInNewTab) {
                window.open(url, '_blank');
            } else {
                // Đợi request hoàn thành (hoặc bị lỗi) trước khi chuyển hướng
                fetchPromise.finally(() => {
                    window.location.href = url;
                });
            }
        }
    }

    function markAsRead(event, id) {
        event.stopPropagation();
        const item = event.target.closest('.notification-item');
        
        if (item && !item.classList.contains('opacity-80')) {
            // Cập nhật UI lập tức
            item.classList.remove('border-l-4', 'border-sky-400');
            item.classList.add('opacity-80');
            const readBtn = item.querySelector('.mark-read-btn');
            if (readBtn) readBtn.remove();
            const unreadDot = item.querySelector('.unread-dot');
            if (unreadDot) unreadDot.remove();
            
            // Giảm badge chưa đọc
            decrementBadgeCount(1);
        }
        
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(() => {
            if (typeof updateGlobalNotificationCount === 'function') {
                updateGlobalNotificationCount();
            }
        });
    }

    function deleteNotification(event, id) {
        event.stopPropagation();
        const item = event.target.closest('.notification-item');
        
        const performDelete = () => {
            const isUnread = item && !item.classList.contains('opacity-80');
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
                        if (isUnread) {
                            decrementBadgeCount(1);
                        }
                        if (typeof updateGlobalNotificationCount === 'function') {
                            updateGlobalNotificationCount();
                        }
                        
                        // Hiển thị thông báo xóa thành công
                        if (typeof window.showToast === 'function') {
                            window.showToast('Xóa thông báo thành công!', 'success');
                        }
                        
                        if(document.querySelectorAll('.notification-item').length === 0) {
                            location.reload(); // Hiển thị trạng thái trống
                        }
                    } else {
                        item.classList.remove('removing');
                        if (typeof window.showToast === 'function') {
                            window.showToast(data.message || 'Xóa thông báo thất bại!', 'error');
                        }
                    }
                })
                .catch(() => {
                    item.classList.remove('removing');
                    if (typeof window.showToast === 'function') {
                        window.showToast('Lỗi kết nối khi xóa thông báo!', 'error');
                    }
                });
            }, 300);
        };

        // Sử dụng Global Confirm Modal nếu có, ngược lại dùng confirm mặc định
        if (typeof window.openConfirmModal === 'function') {
            window.openConfirmModal(
                'Xác nhận xóa', 
                'Bạn có chắc chắn muốn xóa thông báo này không? Thao tác này không thể hoàn tác.', 
                performDelete, 
                'Xóa ngay'
            );
        } else {
            if (confirm('Bạn có chắc chắn muốn xóa thông báo này không?')) {
                performDelete();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const markAllBtn = document.getElementById('mark-all-read');
        if(markAllBtn) {
            markAllBtn.addEventListener('click', function() {
                // Đếm xem có bao nhiêu thông báo chưa đọc trên trang hiện tại để giảm badge
                const unreadCount = document.querySelectorAll('.notification-item:not(.opacity-80)').length;
                
                // Cập nhật UI lập tức
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.classList.remove('border-l-4', 'border-sky-400');
                    item.classList.add('opacity-80');
                    const btn = item.querySelector('.mark-read-btn');
                    if(btn) btn.remove();
                    const unreadDot = item.querySelector('.unread-dot');
                    if(unreadDot) unreadDot.remove();
                });
                
                decrementBadgeCount(unreadCount);
                this.remove();

                fetch('{{ route('notifications.markAllRead') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(() => {
                    if (typeof updateGlobalNotificationCount === 'function') {
                        updateGlobalNotificationCount();
                    }
                });
            });
        }

        const clearAllBtn = document.getElementById('clear-all-notifications');
        if(clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                const performClearAll = () => {
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
                            if (typeof window.showToast === 'function') {
                                window.showToast('Đã xóa tất cả thông báo thành công!', 'success');
                            }
                            setTimeout(() => {
                                document.getElementById('notification-list').innerHTML = '';
                                location.reload();
                            }, 500);
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast(data.message || 'Xóa tất cả thông báo thất bại!', 'error');
                            }
                        }
                    })
                    .catch(() => {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Lỗi kết nối khi xóa tất cả thông báo!', 'error');
                        }
                    });
                };

                // Sử dụng Global Confirm Modal nếu có, ngược lại dùng confirm mặc định
                if (typeof window.openConfirmModal === 'function') {
                    window.openConfirmModal(
                        'Xóa tất cả thông báo', 
                        'Bạn có chắc chắn muốn xóa tất cả thông báo không? Thao tác này không thể hoàn tác.', 
                        performClearAll, 
                        'Xóa tất cả'
                    );
                } else {
                    if(confirm('Bạn có chắc chắn muốn xóa tất cả thông báo không? Thao tác này không thể hoàn tác.')) {
                        performClearAll();
                    }
                }
            });
        }
    });
</script>
@endsection
