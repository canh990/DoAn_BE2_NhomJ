@extends('layouts.app')

@section('title', 'Trang cá nhân - ' . ($user->name ?? 'Người dùng'))

@section('content')
@php
$isOwnProfile = auth()->check() && auth()->id() === $user->id;
$isFollowing = auth()->check() && ! $isOwnProfile
? auth()->user()->following()->where('nguoi_duoc_theo_doi_id', $user->id)->exists()
: false;
$isAcceptedFollower = auth()->check() && ! $isOwnProfile
? auth()->user()->following()->where('nguoi_duoc_theo_doi_id', $user->id)->where('theo_doi.trang_thai', 'da_chap_nhan')->exists()
: false;
$isPendingFollower = auth()->check() && ! $isOwnProfile
? auth()->user()->following()->where('nguoi_duoc_theo_doi_id', $user->id)->where('theo_doi.trang_thai', 'cho_chap_nhan')->exists()
: false;
$isFollowedBy = auth()->check() && ! $isOwnProfile
? auth()->user()->followers()->where('nguoi_theo_doi_id', $user->id)->where('theo_doi.trang_thai', 'da_chap_nhan')->exists()
: false;
$isMutual = $isAcceptedFollower && $isFollowedBy;
$profileUrl = route('profile.public', ['username' => $user->ten_dang_nhap]);
@endphp

<div class="max-w-4xl mx-auto pb-20">
    @if(session('success'))
    <div class="mx-6 mt-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
        {{ session('success') }}
    </div>
    @endif

    <div class="relative">
        <div class="h-64 w-full overflow-hidden bg-slate-900">
            <img
                class="h-full w-full object-cover opacity-60"
                data-alt="Cover photo"
                src="{{ !empty($user->anh_bia) ? asset('storage/' . $user->anh_bia) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuAE8EPzz-gX79DnqAhi0_StOHC91uLm5YDBZwVLWbndwUQ6uK_rUjvdGCmWgdMz8vhDT_KZFa7NE8T8ihfKelL_dO6jLGlJ8sd5AE6svxEDyG59LqoA7KF1QD7pTUv6D9M81ss6aD-J7fp3RxaxKdLt7IZjLiJaECpsYmxZooT54hRgR9bp_99vkrKdEiEEJLPZHCE2LjfSk9G8-idX4qneAxORxh9pxv-y-X3poNr_QVPvLMaqwrEV3YZPUe4RW_tg-_TiSfiyeV4' }}" />
        </div>

        <div class="relative z-10 -mt-16 flex flex-col items-start px-6 sm:-mt-24">
            <div class="flex w-full items-end justify-between">
                <div class="group relative">
                    <div class="glass-panel-elevated h-32 w-32 overflow-hidden rounded-full border-4 border-background sm:h-44 sm:w-44">
                        <img
                            class="h-full w-full object-cover"
                            alt="{{ $user->name }}"
                            <img
                            id="avatar-preview"
                            alt="{{ $user->name }}"
                            class="w-full h-full object-cover"
                            src="{{ $user->anh_dai_dien ? asset('storage/' . $user->anh_dai_dien) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}" />
                    </div>
                    <!-- @if($isOwnProfile)
                    <div class="absolute bottom-2 right-2 hidden cursor-pointer rounded-full bg-sky-400 p-2 text-on-primary shadow-lg transition-transform hover:scale-110 group-hover:block">
                        <span class="material-symbols-outlined text-base" data-icon="photo_camera">photo_camera</span>
                    </div>
                    @endif -->
                </div>

                <div class="mb-2 flex gap-3">
                    @if($isOwnProfile)
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center rounded-xl border border-sky-400/20 glass-panel px-6 py-2 font-semibold text-on-surface transition-all hover:bg-white/10 active:scale-95">
                        Chỉnh sửa hồ sơ
                    </a>
                    @elseif(auth()->check())
                    <button id="follow-btn" data-user-id="{{ $user->id }}" class="rounded-xl border {{ $isMutual ? 'border-emerald-500/20 text-emerald-400' : ($isFollowing ? 'border-sky-400/20 text-slate-300' : 'border-sky-400/20 text-sky-300') }} glass-panel px-6 py-2 font-semibold transition-all hover:bg-white/10 flex items-center justify-center gap-1.5 shadow-lg">
                        @if($isPendingFollower)
                            Chờ chấp nhận
                        @elseif($isMutual)
                            <span class="material-symbols-outlined text-[18px]">handshake</span>
                            Bạn bè
                        @elseif($isFollowing)
                            Bỏ theo dõi
                        @else
                            Theo dõi
                        @endif
                    </button>
                    <button type="button" onclick="openBlockModal('{{ $user->id }}', '{{ $user->name }}')" class="rounded-xl border border-red-500/20 glass-panel px-6 py-2 font-semibold text-red-400 transition-all hover:bg-red-500/10 active:scale-95 flex items-center gap-1.5 shadow-lg shadow-red-500/5">
                        <span class="material-symbols-outlined text-[18px]">block</span>
                        Chặn
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl border border-sky-400/20 glass-panel px-6 py-2 font-semibold text-sky-300 transition-all hover:bg-white/10">
                        Đăng nhập để theo dõi
                    </a>
                    @endif

                    <button
                        type="button"
                        class="rounded-xl border border-sky-400/20 glass-panel p-2 text-on-surface transition-all hover:bg-white/10"
                        onclick="navigator.clipboard.writeText('{{ $profileUrl }}'); if(typeof window.showToast === 'function') { window.showToast('Đã sao chép liên kết hồ sơ!', 'success'); }"
                        title="Sao chép liên kết hồ sơ">
                        <span class="material-symbols-outlined" data-icon="share">share</span>
                    </button>
                </div>
            </div>

            <div class="mt-4 space-y-1">
                <div class="flex items-center gap-2">
                    <h1 class="text-3xl font-bold tracking-tight text-on-surface">{{ $user->name }}</h1>
                    @if($user->da_xac_thuc)
                    <span class="material-symbols-outlined text-xl text-sky-400" data-icon="verified" style="font-variation-settings: 'FILL' 1;">verified</span>
                    @endif
                </div>
                <p class="text-lg font-medium text-slate-400">{{ '@' . ($user->ten_dang_nhap ?? 'nguoidung') }}</p>
            </div>

            <div class="mt-4 max-w-2xl">
                <p class="leading-relaxed text-on-surface-variant">
                    {{ $user->tieu_su ?? 'Chưa có giới thiệu nào' }}
                </p>
            </div>

            <div class="mt-6 flex flex-wrap gap-6">
                <a href="{{ route('profile.following', ['username' => $user->ten_dang_nhap]) }}" class="group flex cursor-pointer items-center gap-1.5">
                    <span class="font-bold text-on-surface group-hover:text-sky-300">{{ number_format($user->following_count ?? 0) }}</span>
                    <span class="text-slate-400 group-hover:text-slate-300">Đang theo dõi</span>
                </a>
                <a href="{{ route('profile.followers', ['username' => $user->ten_dang_nhap]) }}" class="group flex cursor-pointer items-center gap-1.5">
                    <span id="followers-count" class="font-bold text-on-surface group-hover:text-sky-300">{{ number_format($user->followers_count ?? 0) }}</span>
                    <span class="text-slate-400 group-hover:text-slate-300">Người theo dõi</span>
                </a>
                <div class="flex items-center gap-1.5 text-slate-400">
                    <span class="material-symbols-outlined text-sm" data-icon="calendar_month">calendar_month</span>
                    <span>Đã tham gia {{ isset($user->created_at) ? $user->created_at->format('m/Y') : '10/2023' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="sticky top-16 z-30 mt-8 border-b border-sky-400/10 bg-[#0a0e1a]/80 backdrop-blur-md">
        <div class="no-scrollbar flex overflow-x-auto px-2">
            <button id="btn-tab-bai-dang" data-tab="bai-dang" class="tab-btn whitespace-nowrap border-b-2 border-sky-400 px-6 py-4 font-bold text-sky-300 transition-all">Bài đăng</button>
            <button id="btn-tab-phan-hoi" data-tab="phan-hoi" class="tab-btn whitespace-nowrap px-6 py-4 font-medium text-slate-400 transition-all hover:bg-white/5 hover:text-sky-200">Phản hồi</button>
            <button id="btn-tab-phuong-tien" data-tab="phuong-tien" class="tab-btn whitespace-nowrap px-6 py-4 font-medium text-slate-400 transition-all hover:bg-white/5 hover:text-sky-200">Phương tiện</button>
            <button id="btn-tab-thich" data-tab="thich" class="tab-btn whitespace-nowrap px-6 py-4 font-medium text-slate-400 transition-all hover:bg-white/5 hover:text-sky-200">Thích</button>
        </div>
    </div>

    <div class="mt-8 px-6">
        @if(!$isOwnProfile && $user->quyen_rieng_tu === 'rieng_tu' && !$isAcceptedFollower)
            {{-- Private Profile View: Hide everything else, show beautiful full-width lock panel --}}
            <div class="glass-panel flex flex-col items-center justify-center rounded-3xl p-16 text-center min-h-[380px] border border-white/5 shadow-xl bg-slate-900/40 backdrop-blur-md">
                <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-slate-800/40 text-sky-400 border border-sky-400/20 shadow-lg shadow-sky-400/5">
                    <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1;">lock</span>
                </div>
                <h3 class="text-2xl font-black text-on-surface tracking-wide">Đây là tài khoản riêng tư</h3>
                <p class="mt-3 text-slate-400 max-w-md mx-auto text-sm leading-relaxed">Chỉ những người theo dõi được chấp nhận mới có thể xem ảnh, bài viết và thông tin chi tiết của người dùng này.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="space-y-6 md:col-span-1">
                <div class="glass-panel space-y-4 rounded-2xl p-5">
                    <h3 class="text-lg font-bold text-sky-300">Giới thiệu</h3>
                    <div class="space-y-3">
                        @if(!empty($user->noi_o) && ($isOwnProfile || $user->quyen_rieng_tu !== 'rieng_tu' || $isAcceptedFollower))
                        <div class="flex items-center gap-3 text-slate-300 min-w-0">
                            <span class="material-symbols-outlined text-sky-400/70 shrink-0" data-icon="location_on">location_on</span>
                            <span class="text-sm truncate" title="{{ $user->noi_o }}">{{ $user->noi_o }}</span>
                        </div>
                        @endif
                        
                        @if(!empty($user->ngay_sinh) && ($isOwnProfile || $user->quyen_rieng_tu !== 'rieng_tu' || $isAcceptedFollower))
                        <div class="flex items-center gap-3 text-slate-300 min-w-0">
                            <span class="material-symbols-outlined text-sky-400/70 shrink-0" data-icon="cake">cake</span>
                            <span class="text-sm truncate" title="{{ \Illuminate\Support\Carbon::parse($user->ngay_sinh)->format('d/m/Y') }}">{{ \Illuminate\Support\Carbon::parse($user->ngay_sinh)->format('d/m/Y') }}</span>
                        </div>
                        @endif

                        @if(!empty($user->so_dien_thoai) && ($isOwnProfile || $user->quyen_rieng_tu !== 'rieng_tu' || $isAcceptedFollower))
                        <div class="flex items-center gap-3 text-slate-300 min-w-0">
                            <span class="material-symbols-outlined text-sky-400/70 shrink-0" data-icon="call">call</span>
                            <span class="text-sm truncate" title="{{ $user->so_dien_thoai }}">{{ $user->so_dien_thoai }}</span>
                        </div>
                        @endif

                        @if(!empty($user->email) && ($isOwnProfile || $user->quyen_rieng_tu !== 'rieng_tu' || $isAcceptedFollower))
                        <div class="flex items-center gap-3 text-slate-300 min-w-0">
                            <span class="material-symbols-outlined text-sky-400/70 shrink-0" data-icon="mail">mail</span>
                            <span class="text-sm truncate" title="{{ $user->email }}">{{ $user->email }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="glass-panel space-y-4 rounded-2xl p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-sky-300">Phương tiện</h3>
                        <button id="btn-view-all-media" class="text-xs text-sky-300 hover:underline">Xem tất cả</button>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        @forelse($userMedia->take(6) as $media)
                            @php
                                $mediaSrc = \Illuminate\Support\Str::startsWith($media->duong_dan, ['http://', 'https://'])
                                    ? $media->duong_dan
                                    : asset('storage/' . ltrim($media->duong_dan, '/'));
                                $isVideo = $media->loai === 'video' || \Illuminate\Support\Str::endsWith($media->duong_dan, ['.mp4', '.webm', '.mov']);
                            @endphp
                            <a href="{{ route('posts.show', $media->bai_viet_id) }}" class="aspect-square overflow-hidden rounded-lg bg-slate-800 relative group block">
                                @if($isVideo)
                                    <video src="{{ $mediaSrc }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" muted playsinline></video>
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/40 transition-colors">
                                        <span class="material-symbols-outlined text-white text-xl drop-shadow-md">play_circle</span>
                                    </div>
                                @else
                                    <img class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                         src="{{ $mediaSrc }}" 
                                         alt="Media" />
                                @endif
                            </a>
                        @empty
                            <div class="col-span-3 py-8 text-center text-sm text-slate-500 bg-slate-900/50 rounded-xl border border-dashed border-white/10">
                                Chưa có ảnh hoặc video nào.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6 md:col-span-2">
                {{-- Tab Bài đăng --}}
                    <div id="tab-content-bai-dang" class="tab-content space-y-6">
                        @include('components.stories-bar', ['stories' => $stories ?? collect()])

                        @if(isset($posts) && $posts->count() > 0)
                            <div id="post-list-container" class="space-y-6">
                                @foreach($posts as $post)
                                    <x-post-card :post="$post" />
                                @endforeach
                            </div>
                        @else
                            <div class="glass-panel flex flex-col items-center justify-center rounded-3xl p-12 text-center">
                                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-800/50 text-slate-500">
                                    <span class="material-symbols-outlined text-4xl" data-icon="post_add">post_add</span>
                                </div>
                                <h3 class="text-xl font-bold text-on-surface">Chưa có bài đăng nào</h3>
                                <p class="mt-2 text-slate-400">Người dùng này vẫn chưa chia sẻ bài viết nào với cộng đồng.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Tab Phương tiện --}}
                    <div id="tab-content-phuong-tien" class="tab-content hidden">
                        <div class="glass-panel rounded-3xl p-4 sm:p-6">
                            <h3 class="text-xl font-bold text-sky-300 mb-6 flex items-center gap-2">
                                <span class="material-symbols-outlined">perm_media</span>
                                Tất cả phương tiện
                            </h3>
                            
                            @if($userMedia->count() > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    @foreach($userMedia as $media)
                                        @php
                                            $mediaSrc = \Illuminate\Support\Str::startsWith($media->duong_dan, ['http://', 'https://'])
                                                ? $media->duong_dan
                                                : asset('storage/' . ltrim($media->duong_dan, '/'));
                                            $isVideo = $media->loai === 'video' || \Illuminate\Support\Str::endsWith($media->duong_dan, ['.mp4', '.webm', '.mov']);
                                        @endphp
                                        <a href="{{ route('posts.show', $media->bai_viet_id) }}" class="aspect-square overflow-hidden rounded-2xl bg-slate-900 border border-white/5 relative group block">
                                            @if($isVideo)
                                                <video src="{{ $mediaSrc }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" muted playsinline></video>
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/40 transition-colors">
                                                    <span class="material-symbols-outlined text-white text-3xl drop-shadow-md">play_circle</span>
                                                </div>
                                            @else
                                                <img class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                                     src="{{ $mediaSrc }}" 
                                                     alt="Media" />
                                            @endif
                                            
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                                <span class="text-[10px] text-white/80 font-medium">
                                                    {{ $media->ngay_tao ? \Carbon\Carbon::parse($media->ngay_tao)->format('d/m/Y') : '' }}
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-20 text-center">
                                    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-800/50 text-slate-500 border border-dashed border-slate-700">
                                        <span class="material-symbols-outlined text-4xl">no_photography</span>
                                    </div>
                                    <h3 class="text-lg font-bold text-on-surface">Không tìm thấy phương tiện</h3>
                                    <p class="mt-2 text-slate-400">Người dùng này chưa đăng tải hình ảnh hoặc video nào.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Các tab khác (Placeholder) --}}
                    <div id="tab-content-phan-hoi" class="tab-content hidden">
                        <div class="glass-panel rounded-3xl p-12 text-center">
                            <span class="material-symbols-outlined text-5xl text-slate-600 mb-4">forum</span>
                            <p class="text-slate-400 text-lg font-medium">Chức năng xem phản hồi đang được phát triển.</p>
                        </div>
                    </div>
                    <div id="tab-content-thich" class="tab-content hidden">
                        <div class="glass-panel rounded-3xl p-12 text-center">
                            <span class="material-symbols-outlined text-5xl text-slate-600 mb-4">favorite</span>
                            <p class="text-slate-400 text-lg font-medium">Chức năng xem bài viết đã thích đang được phát triển.</p>
                        </div>
                    </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Block Confirmation Modal --}}
<div id="block-user-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    {{-- Backdrop with glass effect --}}
    <div class="absolute inset-0 bg-[#070b13]/80 backdrop-blur-sm" onclick="closeBlockModal()"></div>
    
    {{-- Content --}}
    <div class="relative w-full max-w-md overflow-hidden rounded-3xl border border-red-500/20 bg-[#0b0f19] p-6 shadow-2xl transition-all duration-300 transform scale-95 opacity-0" id="block-modal-content">
        <div class="flex flex-col items-center text-center">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-500/10 text-red-500 border border-red-500/20 shadow-lg shadow-red-500/5">
                <span class="material-symbols-outlined text-3xl">gavel</span>
            </div>
            
            <h3 class="text-xl font-bold text-on-surface">Chặn người dùng này?</h3>
            <p class="mt-3 text-sm text-slate-400 leading-relaxed">
                Bạn có chắc chắn muốn chặn <span class="font-semibold text-red-400" id="block-user-name-placeholder">Người dùng</span>? Khi đã chặn, cả hai bên sẽ không thể theo dõi, xem hồ sơ, xem bài viết hoặc nhắn tin trò chuyện với nhau.
            </p>
        </div>
        
        <div class="mt-6 flex gap-3">
            <button type="button" onclick="closeBlockModal()" class="flex-1 rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-semibold text-slate-300 transition-all hover:bg-white/10 active:scale-95">
                Hủy
            </button>
            <button type="button" id="confirm-block-btn" class="flex-1 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 py-3 text-sm font-semibold text-white shadow-lg shadow-red-500/20 transition-all hover:brightness-110 active:scale-95 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-base">block</span>
                Chặn ngay
            </button>
        </div>
    </div>
</div>

<script>
    let blockTargetUserId = null;

    window.openBlockModal = function(userId, userName) {
        blockTargetUserId = userId;
        document.getElementById('block-user-name-placeholder').innerText = userName;
        
        const modal = document.getElementById('block-user-modal');
        const content = document.getElementById('block-modal-content');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    window.closeBlockModal = function() {
        const modal = document.getElementById('block-user-modal');
        const content = document.getElementById('block-modal-content');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const confirmBlockBtn = document.getElementById('confirm-block-btn');
        if (confirmBlockBtn) {
            confirmBlockBtn.addEventListener('click', async function() {
                if (!blockTargetUserId) return;
                
                try {
                    const response = await fetch(`/user/${blockTargetUserId}/block`, {
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
                        closeBlockModal();
                        if (typeof window.showToast === 'function') {
                            window.showToast(data.message || 'Đã chặn thành công!', 'success');
                        }
                        if (data.redirect_url) {
                            setTimeout(() => {
                                window.location.href = data.redirect_url;
                            }, 1000);
                        }
                    } else {
                        const err = await response.json();
                        if (typeof window.showToast === 'function') {
                            window.showToast(err.error || 'Có lỗi xảy ra.', 'error');
                        }
                    }
                } catch (error) {
                    console.error('Lỗi khi chặn:', error);
                }
            });
        }

        // Tab switching logic
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');

                // Update buttons
                tabBtns.forEach(b => {
                    b.classList.remove('border-b-2', 'border-sky-400', 'font-bold', 'text-sky-300');
                    b.classList.add('font-medium', 'text-slate-400');
                });
                this.classList.remove('font-medium', 'text-slate-400');
                this.classList.add('border-b-2', 'border-sky-400', 'font-bold', 'text-sky-300');

                // Update contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
        const targetContent = document.getElementById(`tab-content-${tabId}`);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });

        // "Xem tất cả" media button logic
        const btnViewAllMedia = document.getElementById('btn-view-all-media');
        const btnTabPhuongTien = document.getElementById('btn-tab-phuong-tien');
        if (btnViewAllMedia && btnTabPhuongTien) {
            btnViewAllMedia.addEventListener('click', function() {
                btnTabPhuongTien.click();
                // Tùy chọn: Cuộn lên đến phần tab
                btnTabPhuongTien.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }

        const followBtn = document.getElementById('follow-btn');
        const followersCount = document.getElementById('followers-count');

        if (followBtn) {
            followBtn.addEventListener('click', async function() {
                const userId = this.getAttribute('data-user-id');

                try {
                    const response = await fetch(`/user/${userId}/toggle-follow`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.is_following) {
                            if (data.status === 'cho_chap_nhan') {
                                this.innerHTML = 'Chờ chấp nhận';
                                this.className = "rounded-xl border border-sky-400/20 glass-panel px-6 py-2 font-semibold transition-all hover:bg-white/10 text-slate-300 flex items-center justify-center gap-1.5 shadow-lg";
                            } else if (data.is_mutual) {
                                this.innerHTML = '<span class="material-symbols-outlined text-[18px]">handshake</span> Bạn bè';
                                this.className = "rounded-xl border border-emerald-500/20 text-emerald-400 glass-panel px-6 py-2 font-semibold transition-all hover:bg-white/10 flex items-center justify-center gap-1.5 shadow-lg";
                            } else {
                                this.innerHTML = 'Bỏ theo dõi';
                                this.className = "rounded-xl border border-sky-400/20 glass-panel px-6 py-2 font-semibold transition-all hover:bg-white/10 text-slate-300 flex items-center justify-center gap-1.5 shadow-lg";
                            }
                        } else {
                            this.innerHTML = 'Theo dõi';
                            this.className = "rounded-xl border border-sky-400/20 glass-panel px-6 py-2 font-semibold transition-all hover:bg-white/10 text-sky-300 flex items-center justify-center gap-1.5 shadow-lg";
                        }
                        followersCount.innerText = new Intl.NumberFormat().format(data.followers_count);
                    }
                } catch (error) {
                    console.error('Lỗi khi thực hiện theo dõi:', error);
                }
            });
        }
    });
</script>

@endsection