@extends('layouts.app')

@section('title', __('messages.notifications_title'))

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-sky-400 to-purple-400 bg-clip-text text-transparent">{{ __('messages.notifications_title') }}</h1>
            <p class="text-slate-500 text-sm mt-1">{{ __('messages.notifications_always_updated') }}</p>
        </div>
        <div class="flex gap-2">
            @if($notifications->count() > 0)
                <button id="clear-all-notifications" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-400/10 rounded-xl transition-all" title="{{ __('messages.notifications_clear_all') }}">
                    <span class="material-symbols-outlined">delete_sweep</span>
                </button>
                @if($notifications->where('da_doc', false)->count() > 0)
                    <button id="mark-all-read" class="p-2 text-slate-400 hover:text-sky-400 hover:bg-sky-400/10 rounded-xl transition-all" title="{{ __('messages.notifications_mark_all_read') }}">
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
                        if ($notification->binh_luan_id) {
                            $redirectUrl .= '#comment-' . $notification->binh_luan_id;
                        }
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
                    case 'bao_cao':
                        $redirectUrl = route('admin.reports.index');
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
                            <img src="{{ $notification->nguoiThucHien->avatar_url }}" 
                                 alt="{{ $notification->nguoiThucHien->name }}"
                                 class="w-12 h-12 rounded-full object-cover border border-sky-400/20 shadow-sm">
                        </a>
                        {{-- Icon badge for notification type --}}
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full flex items-center justify-center border-2 border-[#0a0e1a] 
                            @switch($notification->loai)
                                @case('thich')
                                    @php
                                        $rx = $notification->binh_luan_id ? $notification->commentReaction : $notification->reaction;
                                        $rxType = $rx ? $rx->loai_cam_xuc : 'thich';
                                    @endphp
                                    @switch($rxType)
                                        @case('thich') bg-sky-500 @break
                                        @case('tim') bg-rose-500 @break
                                        @case('haha') bg-yellow-500 @break
                                        @case('buon') bg-slate-500 @break
                                        @case('phan_no') bg-orange-500 @break
                                        @case('wow') bg-emerald-500 @break
                                        @default bg-rose-500
                                    @endswitch
                                    @break
                                @case('binh_luan') bg-sky-500 @break
                                @case('theo_doi') @case('ket_ban') bg-purple-500 @break
                                @case('chia_se') bg-emerald-500 @break
                                @case('tag') @case('tag_all') bg-amber-500 @break
                                @case('ghim_binh_luan') bg-yellow-500 @break
                                @case('tin_nhan') @case('tin_nhan_nhom') bg-indigo-500 @break
                                @case('bao_cao') bg-red-500 @break
                                @default bg-slate-500
                            @endswitch">
                            <span class="material-symbols-outlined text-white text-[14px]">
                                @switch($notification->loai)
                                    @case('thich')
                                        @php
                                            $rx = $notification->binh_luan_id ? $notification->commentReaction : $notification->reaction;
                                            $rxType = $rx ? $rx->loai_cam_xuc : 'thich';
                                        @endphp
                                        @switch($rxType)
                                            @case('thich') thumb_up @break
                                            @case('tim') favorite @break
                                            @case('haha') mood @break
                                            @case('buon') sentiment_dissatisfied @break
                                            @case('phan_no') mood_bad @break
                                            @case('wow') emoji_objects @break
                                            @default favorite
                                        @endswitch
                                        @break
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
                                    @case('bao_cao') report @break
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
                                    @if($notification->binh_luan_id)
                                        @php
                                            $rx = $notification->commentReaction;
                                            $rxType = $rx ? $rx->loai_cam_xuc : 'thich';
                                        @endphp
                                        @switch($rxType)
                                            @case('tim') {{ app()->getLocale() == 'vi' ? 'đã bày tỏ yêu thích bình luận của bạn.' : 'loved your comment.' }} @break
                                            @case('haha') {{ app()->getLocale() == 'vi' ? 'đã bày tỏ cảm xúc haha với bình luận của bạn.' : 'reacted haha to your comment.' }} @break
                                            @case('buon') {{ app()->getLocale() == 'vi' ? 'đã bày tỏ cảm xúc buồn với bình luận của bạn.' : 'reacted sad to your comment.' }} @break
                                            @case('phan_no') {{ app()->getLocale() == 'vi' ? 'đã phẫn nộ với bình luận của bạn.' : 'reacted angry to your comment.' }} @break
                                            @case('wow') {{ app()->getLocale() == 'vi' ? 'đã bày tỏ cảm xúc wow với bình luận của bạn.' : 'reacted wow to your comment.' }} @break
                                            @default {{ app()->getLocale() == 'vi' ? 'đã thích bình luận của bạn.' : 'liked your comment.' }}
                                        @endswitch
                                    @else
                                        @php
                                            $rx = $notification->reaction;
                                            $rxType = $rx ? $rx->loai_cam_xuc : 'thich';
                                        @endphp
                                        @switch($rxType)
                                            @case('tim') {{ app()->getLocale() == 'vi' ? 'đã bày tỏ yêu thích bài viết của bạn.' : 'loved your post.' }} @break
                                            @case('haha') {{ app()->getLocale() == 'vi' ? 'đã bày tỏ cảm xúc haha với bài viết của bạn.' : 'reacted haha to your post.' }} @break
                                            @case('buon') {{ app()->getLocale() == 'vi' ? 'đã bày tỏ cảm xúc buồn với bài viết của bạn.' : 'reacted sad to your post.' }} @break
                                            @case('phan_no') {{ app()->getLocale() == 'vi' ? 'đã phẫn nộ với bài viết của bạn.' : 'reacted angry to your post.' }} @break
                                            @case('wow') {{ app()->getLocale() == 'vi' ? 'đã bày tỏ cảm xúc wow với bài viết của bạn.' : 'reacted wow to your post.' }} @break
                                            @default {{ __('messages.notifications_liked_post') }}
                                        @endswitch
                                    @endif
                                    @break
                                @case('binh_luan')
                                    @if($notification->binhLuan && $notification->binhLuan->binh_luan_cha_id)
                                        {{ __('messages.notifications_replied_comment') }}
                                    @else
                                        {{ __('messages.notifications_commented_post') }}
                                    @endif
                                    @break
                                @case('theo_doi')
                                    @php
                                        $isPendingRequest = \DB::table('theo_doi')
                                            ->where('nguoi_theo_doi_id', $notification->nguoi_thuc_hien_id)
                                            ->where('nguoi_duoc_theo_doi_id', $notification->nguoi_dung_id)
                                            ->where('trang_thai', 'cho_chap_nhan')
                                            ->exists();
                                    @endphp
                                    @if($isPendingRequest)
                                        {{ __('messages.notifications_follow_request') }}
                                    @else
                                        {{ __('messages.notifications_started_following') }}
                                    @endif
                                    @break
                                @case('ket_ban')
                                    {{ __('messages.notifications_became_friends') }}
                                    @break
                                @case('chia_se')
                                    {{ __('messages.notifications_shared_post') }}
                                    @break
                                @case('tin_nhan')
                                    {{ __('messages.notifications_sent_new_message') }}
                                    @break
                                @case('tin_nhan_nhom')
                                    {{ __('messages.notifications_new_group_message') }}
                                    @break
                                @case('tag')
                                    @if($notification->binh_luan_id)
                                        {{ __('messages.notifications_tagged_comment') }}
                                    @else
                                        {{ __('messages.notifications_tagged_post') }}
                                    @endif
                                    @break
                                @case('tag_all')
                                    @if($notification->binh_luan_id)
                                        {{ __('messages.notifications_tagged_all_comment') }}
                                    @else
                                        {{ __('messages.notifications_tagged_all_post') }}
                                    @endif
                                    @break
                                @case('ghim_binh_luan')
                                    {{ __('messages.notifications_pinned_comment') }}
                                    @break
                                @case('dang_bai')
                                    {{ __('messages.notifications_posted_new') }}
                                    @break
                                @case('dang_tin')
                                    {{ __('messages.notifications_story_new') }}
                                    @break
                                @case('he_thong')
                                    <span class="text-sky-400 font-bold">{{ __('messages.notifications_system') }}</span> {{ $notification->noi_dung ?? __('messages.notifications_system_notification') }}
                                    @break
                                @case('bao_cao')
                                    <span class="text-red-400 font-bold">Báo cáo vi phạm:</span> {{ $notification->noi_dung ?? 'Có báo cáo mới cần xử lý.' }}
                                    @break
                                @default
                                    {{ __('messages.notifications_interacted') }}
                            @endswitch
                        </div>
                        
                        <div class="flex items-center gap-3 mt-1.5">
                            <span class="text-[11px] text-slate-500 font-medium">{{ $notification->ngay_tao->diffForHumans() }}</span>
                            @if(!$notification->da_doc)
                                <span class="unread-dot w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(125,211,252,0.5)]"></span>
                            @endif
                        </div>

                        {{-- Follow Request Actions --}}
                        @if($notification->loai === 'theo_doi' && isset($isPendingRequest) && $isPendingRequest)
                            <div class="mt-3 flex items-center gap-2.5" id="follow-request-actions-{{ $notification->id }}">
                                <button type="button" 
                                        onclick="handleFollowRequest(event, {{ $notification->nguoi_thuc_hien_id }}, 'accept', {{ $notification->id }})" 
                                        class="px-4 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-xs font-bold text-white shadow-lg shadow-emerald-500/20 hover:brightness-110 active:scale-95 transition-all flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">done</span>
                                    {{ __('messages.notifications_accept') }}
                                </button>
                                <button type="button" 
                                        onclick="handleFollowRequest(event, {{ $notification->nguoi_thuc_hien_id }}, 'decline', {{ $notification->id }})" 
                                        class="px-4 py-1.5 rounded-xl border border-white/10 bg-white/5 hover:bg-red-500/10 hover:border-red-500/20 text-xs font-bold text-slate-300 hover:text-red-400 active:scale-95 transition-all flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">close</span>
                                    {{ __('messages.notifications_decline') }}
                                </button>
                            </div>
                        @endif

                        {{-- Action Preview (if post or comment) --}}
                        @if(($notification->loai === 'binh_luan' || ($notification->loai === 'thich' && $notification->binh_luan_id)) && $notification->binhLuan)
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
                            <button class="mark-read-btn p-1.5 text-slate-500 hover:text-sky-400 hover:bg-sky-400/10 rounded-lg transition-all opacity-0 group-hover:opacity-100" title="{{ __('messages.common_save') }}" onclick="markAsRead(event, {{ $notification->id }})">
                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            </button>
                        @endif
                        <button class="delete-notification-btn p-1.5 text-slate-500 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all opacity-0 group-hover:opacity-100" title="{{ __('messages.notifications_delete') }}" onclick="deleteNotification(event, {{ $notification->id }})">
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
                <h3 class="text-xl font-bold text-slate-300">{{ __('messages.notifications_no_notifications') }}</h3>
                <p class="text-slate-500 text-sm mt-2 max-w-xs mx-auto">{{ __('messages.notifications_no_notifications_desc') }}</p>
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
    window.handleFollowRequest = async function(event, followerId, action, notificationId) {
        event.stopPropagation();
        
        const container = document.getElementById(`follow-request-actions-${notificationId}`);
        if (!container) return;
        
        // Find buttons and disable them
        const buttons = container.querySelectorAll('button');
        buttons.forEach(btn => btn.disabled = true);
        
        const endpoint = action === 'accept' 
            ? `/user/${followerId}/accept-follow` 
            : `/user/${followerId}/decline-follow`;
            
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                // Show success toast
                if (window.showToast) {
                    window.showToast(data.message || 'Thao tác thành công!', 'success');
                }
                
                // Replace action buttons with a premium badge
                if (action === 'accept') {
                    container.innerHTML = `
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-xl">
                            <span class="material-symbols-outlined text-[14px]">done</span>
                            Đã chấp nhận
                        </span>
                    `;
                } else {
                    container.innerHTML = `
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 px-3 py-1 rounded-xl">
                            <span class="material-symbols-outlined text-[14px]">close</span>
                            Đã từ chối
                        </span>
                    `;
                }
            } else {
                buttons.forEach(btn => btn.disabled = false);
                if (window.showToast) {
                    window.showToast('Có lỗi xảy ra, vui lòng thử lại.', 'error');
                }
            }
        } catch (error) {
            buttons.forEach(btn => btn.disabled = false);
            console.error(error);
            if (window.showToast) {
                window.showToast('Lỗi kết nối mạng.', 'error');
            }
        }
    }

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
