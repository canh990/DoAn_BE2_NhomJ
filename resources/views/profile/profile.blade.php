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


    <div class="relative">
        <div class="h-64 w-full overflow-hidden bg-slate-900">
            <img
                class="h-full w-full object-cover opacity-60"
                data-alt="Cover photo"
                src="{{ !empty($user->anh_bia) ? asset('storage/' . $user->anh_bia) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuAE8EPzz-gX79DnqAhi0_StOHC91uLm5YDBZwVLWbndwUQ6uK_rUjvdGCmWgdMz8vhDT_KZFa7NE8T8ihfKelL_dO6jLGlJ8sd5AE6svxEDyG59LqoA7KF1QD7pTUv6D9M81ss6aD-J7fp3RxaxKdLt7IZjLiJaECpsYmxZooT54hRgR9bp_99vkrKdEiEEJLPZHCE2LjfSk9G8-idX4qneAxORxh9pxv-y-X3poNr_QVPvLMaqwrEV3YZPUe4RW_tg-_TiSfiyeV4' }}" />
        </div>

        <div class="relative z-10 -mt-16 flex flex-col items-start px-6 sm:-mt-24">
            <div class="flex w-full items-end justify-between flex-wrap gap-4">
                <div class="group relative">
                    <div class="profile-avatar-container h-32 w-32 overflow-hidden rounded-full border-[3px] border-[#0a0e1a] sm:h-44 sm:w-44 shadow-lg z-20">
                        <img
                            class="h-full w-full object-cover"
                            alt="{{ $user->name }}"
                            src="{{ $user->avatar_url }}" />
                    </div>
                </div>

                <div class="mb-2 flex items-center gap-2 sm:gap-3 flex-wrap">
                    @if($isOwnProfile)
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center rounded-xl bg-gray-200 dark:bg-slate-800 hover:bg-gray-300 dark:hover:bg-slate-700 px-4 py-2 text-sm font-bold text-gray-800 dark:text-slate-200 transition-all active:scale-95 shadow-sm">
                        <span class="material-symbols-outlined text-[18px] mr-1.5">edit</span>
                        {{ __('messages.profile_edit_profile') }}
                    </a>
                    @elseif(auth()->check())
                    <button id="follow-btn" data-user-id="{{ $user->id }}" class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-bold transition-all active:scale-95 gap-1.5 shadow-sm {{ $isMutual ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : ($isFollowing ? 'bg-gray-200 dark:bg-slate-800 hover:bg-gray-300 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-200' : 'bg-blue-600 hover:bg-blue-700 text-white') }}">
                        @if($isPendingFollower)
                            {{ __('messages.profile_pending_accept') }}
                        @elseif($isMutual)
                            <span class="material-symbols-outlined text-[18px]">handshake</span>
                            {{ __('messages.profile_friends') }}
                        @elseif($isFollowing)
                            {{ __('messages.profile_unfollow') }}
                        @else
                            {{ __('messages.profile_follow') }}
                        @endif
                    </button>
                    <button type="button" onclick="openBlockModal('{{ $user->id }}', '{{ $user->name }}')" class="inline-flex items-center justify-center rounded-xl bg-gray-200 dark:bg-slate-800 hover:bg-gray-300 dark:hover:bg-slate-700 px-4 py-2 text-sm font-bold text-red-600 dark:text-red-400 transition-all active:scale-95 gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">block</span>
                        {{ __('messages.profile_block') }}
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-bold text-white transition-all">
                        {{ __('messages.profile_login_to_follow') }}
                    </a>
                    @endif

                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-200 dark:bg-slate-800 hover:bg-gray-300 dark:hover:bg-slate-700 p-2 text-gray-800 dark:text-slate-200 transition-all active:scale-95 shadow-sm"
                        onclick="navigator.clipboard.writeText('{{ $profileUrl }}'); if(typeof window.showToast === 'function') { window.showToast('{{ __('messages.profile_link_copied') }}', 'success'); }"
                        title="{{ __('messages.profile_copy_link') }}">
                        <span class="material-symbols-outlined text-[18px]" data-icon="share">share</span>
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
                    {{ $user->tieu_su ?? __('messages.profile_no_bio') }}
                </p>
            </div>

            <div class="mt-6 flex flex-wrap gap-6">
                <a href="{{ route('profile.following', ['username' => $user->ten_dang_nhap]) }}" class="group flex cursor-pointer items-center gap-1.5">
                    <span class="font-bold text-on-surface group-hover:text-sky-300">{{ number_format($user->following_count ?? 0) }}</span>
                    <span class="text-slate-400 group-hover:text-slate-300">{{ __('messages.profile_following') }}</span>
                </a>
                <a href="{{ route('profile.followers', ['username' => $user->ten_dang_nhap]) }}" class="group flex cursor-pointer items-center gap-1.5">
                    <span id="followers-count" class="font-bold text-on-surface group-hover:text-sky-300">{{ number_format($user->followers_count ?? 0) }}</span>
                    <span class="text-slate-400 group-hover:text-slate-300">{{ __('messages.profile_followers') }}</span>
                </a>
                <div class="flex items-center gap-1.5 text-slate-400">
                    <span class="material-symbols-outlined text-sm" data-icon="calendar_month">calendar_month</span>
                    <span>{{ __('messages.profile_joined') }} {{ isset($user->created_at) ? $user->created_at->format('m/Y') : '10/2023' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="sticky top-16 z-30 mt-8 border-b border-sky-400/10 bg-[#0a0e1a]/80 backdrop-blur-md">
        <div class="no-scrollbar flex overflow-x-auto px-2">
            <button id="btn-tab-bai-dang" data-tab="bai-dang" class="tab-btn whitespace-nowrap border-b-2 border-sky-400 px-6 py-4 font-bold text-sky-300 transition-all">{{ __('messages.profile_posts_tab') }}</button>
            @if($isOwnProfile)
            <button id="btn-tab-nhat-ky" data-tab="nhat-ky" class="tab-btn whitespace-nowrap px-6 py-4 font-medium text-slate-400 transition-all hover:bg-white/5 hover:text-sky-200">{{ __('messages.profile_activity_tab') }}</button>
            @endif
            <button id="btn-tab-phuong-tien" data-tab="phuong-tien" class="tab-btn whitespace-nowrap px-6 py-4 font-medium text-slate-400 transition-all hover:bg-white/5 hover:text-sky-200">{{ __('messages.profile_media_tab') }}</button>
            <button id="btn-tab-ban-be" data-tab="ban-be" class="tab-btn whitespace-nowrap px-6 py-4 font-medium text-slate-400 transition-all hover:bg-white/5 hover:text-sky-200">{{ __('messages.profile_friends_tab') }} ({{ $mutualFriends->count() }})</button>
        </div>
    </div>

    <div class="mt-8 px-6">
        @if(!$isOwnProfile && $user->quyen_rieng_tu === 'rieng_tu' && !$isAcceptedFollower)
            {{-- Private Profile View: Hide everything else, show beautiful full-width lock panel --}}
            <div class="glass-panel flex flex-col items-center justify-center rounded-3xl p-16 text-center min-h-[380px] border border-white/5 shadow-xl bg-slate-900/40 backdrop-blur-md">
                <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-slate-800/40 text-sky-400 border border-sky-400/20 shadow-lg shadow-sky-400/5">
                    <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1;">lock</span>
                </div>
                <h3 class="text-2xl font-black text-on-surface tracking-wide">{{ __('messages.profile_private') }}</h3>
                <p class="mt-3 text-slate-400 max-w-md mx-auto text-sm leading-relaxed">{{ __('messages.profile_private_desc') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div id="profile-sidebar" class="space-y-6 md:col-span-1 transition-all duration-300">
                <div class="glass-panel space-y-4 rounded-2xl p-5">
                    <h3 class="text-lg font-bold text-sky-300">{{ __('messages.profile_about') }}</h3>
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
                        <h3 class="text-lg font-bold text-sky-300">{{ __('messages.profile_media') }}</h3>
                        <button id="btn-view-all-media" class="text-xs text-sky-300 hover:underline">{{ __('messages.profile_view_all') }}</button>
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

            <div id="profile-main-content" class="space-y-6 md:col-span-2 transition-all duration-300">
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

                    {{-- Tab Bạn bè --}}
                    <div id="tab-content-ban-be" class="tab-content hidden">
                        <div class="glass-panel rounded-3xl p-4 sm:p-6">
                            <h3 class="text-xl font-bold text-sky-300 mb-6 flex items-center gap-2">
                                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">group</span>
                                Bạn bè
                                <span class="ml-1 text-sm font-normal text-slate-400">({{ $mutualFriends->count() }})</span>
                            </h3>

                            @if($mutualFriends->count() > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($mutualFriends as $friend)
                                        <a href="{{ route('profile.public', ['username' => $friend->ten_dang_nhap]) }}"
                                           class="flex items-center gap-3 p-3.5 rounded-2xl border border-sky-400/10 bg-white/[0.02] hover:bg-white/5 transition-all group">
                                            <div class="relative shrink-0">
                                                <img src="{{ $friend->avatar_url }}" alt="{{ $friend->name }}"
                                                     class="h-12 w-12 rounded-full object-cover border-2 border-blue-500/40 group-hover:border-blue-500/80 transition-all">
                                                <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-400 border-2 border-[#0f1524]"></span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="font-bold text-on-surface truncate flex items-center gap-1">
                                                    {{ $friend->name }}
                                                    @if($friend->da_xac_thuc)
                                                        <span class="material-symbols-outlined text-sm text-sky-400 shrink-0" style="font-variation-settings: 'FILL' 1;">verified</span>
                                                    @endif
                                                </h4>
                                                <p class="text-xs text-slate-400 truncate">{{ '@' . $friend->ten_dang_nhap }}</p>
                                            </div>
                                            <span class="material-symbols-outlined text-slate-500 group-hover:text-sky-400 group-hover:translate-x-0.5 transition-all text-[20px] shrink-0">chevron_right</span>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-20 text-center">
                                    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-800/50 text-slate-500 border border-dashed border-slate-700">
                                        <span class="material-symbols-outlined text-4xl">group_off</span>
                                    </div>
                                    <h3 class="text-lg font-bold text-on-surface">Chưa có bạn bè chung</h3>
                                    <p class="mt-2 text-slate-400 text-sm max-w-xs">Bạn bè là những người cả hai cùng theo dõi lẫn nhau.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($isOwnProfile)
                    {{-- Tab Nhật ký hoạt động --}}
                    <div id="tab-content-nhat-ky" class="tab-content hidden animate-fade-in">
                        <div class="glass-panel rounded-3xl p-6 space-y-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-white/5 pb-5">
                                <div>
                                    <h3 class="text-xl font-bold text-sky-300 flex items-center gap-2">
                                        <span class="material-symbols-outlined">history</span>
                                        Nhật ký hoạt động của bạn
                                    </h3>
                                    <p class="text-slate-400 text-xs mt-1">Lịch sử các hoạt động cá nhân được bảo mật hoàn toàn.</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                                    {{-- Dropdown lọc thời gian --}}
                                    <div class="relative w-full sm:w-auto">
                                        <select id="activity-time-filter" onchange="filterActivityByTime()" style="appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: none !important;" class="w-full sm:w-auto bg-[#0b0f19] border border-white/10 rounded-xl pl-4 pr-10 py-2.5 text-xs font-bold text-slate-300 hover:text-white focus:outline-none focus:border-sky-400 transition-all cursor-pointer">
                                            <option value="all">Tất cả thời gian</option>
                                            <option value="today">Hôm nay</option>
                                            <option value="7_days">7 ngày qua</option>
                                            <option value="30_days">30 ngày qua</option>
                                        </select>
                                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
                                    </div>
                                    
                                    {{-- Sub tabs switcher with premium design, overflow scrolling and flex --}}
                                    <div class="flex p-1.5 rounded-2xl bg-slate-900/50 border border-white/5 overflow-x-auto no-scrollbar w-full sm:w-auto">
                                    <button type="button" onclick="switchActivitySubTab('liked')" id="btn-sub-liked" class="sub-tab-btn flex-1 sm:flex-none whitespace-nowrap px-5 py-2.5 rounded-xl text-sm font-bold transition-all text-sky-400 bg-sky-400/10 shadow-sm">
                                        Đã thích ({{ $likedCount ?? 0 }})
                                    </button>
                                    <button type="button" onclick="switchActivitySubTab('commented')" id="btn-sub-commented" class="sub-tab-btn flex-1 sm:flex-none whitespace-nowrap px-5 py-2.5 rounded-xl text-sm font-bold transition-all text-slate-400 hover:text-slate-200 hover:bg-white/5 ml-1">
                                        Bình luận ({{ $commentsCount ?? 0 }})
                                    </button>
                                    <button type="button" onclick="switchActivitySubTab('saved')" id="btn-sub-saved" class="sub-tab-btn flex-1 sm:flex-none whitespace-nowrap px-5 py-2.5 rounded-xl text-sm font-bold transition-all text-slate-400 hover:text-slate-200 hover:bg-white/5 ml-1">
                                        Đã lưu ({{ $savedCount ?? 0 }})
                                    </button>
                                </div>
                            </div>
                        </div>

                            {{-- Sub Tab: Liked Posts --}}
                            <div id="sub-content-liked" data-loaded-filter="all" class="sub-activity-content space-y-4">
                                <div class="activity-list space-y-4">
                                    @include('profile.partials.activity_liked', ['likedPosts' => $likedPosts])
                                    @if(($likedCount ?? 0) === 0)
                                        <div class="text-center py-12 text-slate-500">
                                            <span class="material-symbols-outlined text-4xl mb-2">favorite</span>
                                            <p class="text-sm">Bạn chưa thích bài viết nào.</p>
                                        </div>
                                    @endif
                                </div>
                                @if(($likedCount ?? 0) > 10)
                                    <div class="text-center pt-2 btn-load-more-container">
                                        <button type="button" onclick="loadMoreActivity('liked')" id="btn-load-more-liked" data-page="2" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/5 transition-all">
                                            Xem thêm
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Sub Tab: Comment History --}}
                            <div id="sub-content-commented" data-loaded-filter="all" class="sub-activity-content space-y-4 hidden">
                                <div class="activity-list space-y-4">
                                    @include('profile.partials.activity_comments', ['myComments' => $myComments])
                                    @if(($commentsCount ?? 0) === 0)
                                        <div class="text-center py-12 text-slate-500">
                                            <span class="material-symbols-outlined text-4xl mb-2">chat_bubble</span>
                                            <p class="text-sm">Bạn chưa có bình luận nào.</p>
                                        </div>
                                    @endif
                                </div>
                                @if(($commentsCount ?? 0) > 10)
                                    <div class="text-center pt-2 btn-load-more-container">
                                        <button type="button" onclick="loadMoreActivity('commented')" id="btn-load-more-commented" data-page="2" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/5 transition-all">
                                            Xem thêm
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Sub Tab: Saved Posts --}}
                            <div id="sub-content-saved" data-loaded-filter="all" class="sub-activity-content space-y-4 hidden">
                                <div class="activity-list space-y-4">
                                    @include('profile.partials.activity_saved', ['savedPosts' => $savedPosts])
                                    @if(($savedCount ?? 0) === 0)
                                        <div class="text-center py-12 text-slate-500">
                                            <span class="material-symbols-outlined text-4xl mb-2">bookmark</span>
                                            <p class="text-sm">Bạn chưa lưu bài viết nào.</p>
                                        </div>
                                    @endif
                                </div>
                                @if(($savedCount ?? 0) > 10)
                                    <div class="text-center pt-2 btn-load-more-container">
                                        <button type="button" onclick="loadMoreActivity('saved')" id="btn-load-more-saved" data-page="2" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/5 transition-all">
                                            Xem thêm
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

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

                // Dynamic layout adjustments to hide sidebar and expand main content when selecting "Nhật ký hoạt động" or "Phương tiện"
                const sidebar = document.getElementById('profile-sidebar');
                const mainContent = document.getElementById('profile-main-content');
                if (sidebar && mainContent) {
                    if (tabId === 'nhat-ky' || tabId === 'phuong-tien' || tabId === 'ban-be') {
                        sidebar.classList.add('hidden');
                        mainContent.classList.remove('md:col-span-2');
                        mainContent.classList.add('md:col-span-3');
                        
                        // Tự động lướt mượt mà đến phần nội dung tương ứng
                        setTimeout(() => {
                            targetContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 100);
                    } else {
                        sidebar.classList.remove('hidden');
                        mainContent.classList.remove('md:col-span-3');
                        mainContent.classList.add('md:col-span-2');
                    }
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

    window.switchActivitySubTab = function(subtab) {
        // Toggle active states on buttons
        document.querySelectorAll('.sub-tab-btn').forEach(btn => {
            btn.classList.remove('text-sky-400', 'bg-sky-400/10', 'shadow-sm', 'bg-white/5');
            btn.classList.add('text-slate-400');
        });
        const activeBtn = document.getElementById(`btn-sub-${subtab}`);
        if (activeBtn) {
            activeBtn.classList.remove('text-slate-400');
            activeBtn.classList.add('text-sky-400', 'bg-sky-400/10', 'shadow-sm');
        }

        // Toggle active content divs
        document.querySelectorAll('.sub-activity-content').forEach(div => {
            div.classList.add('hidden');
        });
        const activeDiv = document.getElementById(`sub-content-${subtab}`);
        if (activeDiv) {
            activeDiv.classList.remove('hidden');
        }

        // Chỉ đồng bộ dữ liệu nếu bộ lọc đang chọn khác với bộ lọc đã tải cho tab này
        const timeFilter = document.getElementById('activity-time-filter')?.value || 'all';
        if (activeDiv && activeDiv.getAttribute('data-loaded-filter') !== timeFilter) {
            filterActivityByTime();
        }
    }

    window.filterActivityByTime = async function() {
        // Tìm tab đang hoạt động hiện tại
        let activeTab = 'liked';
        document.querySelectorAll('.sub-tab-btn').forEach(btn => {
            if (btn.classList.contains('text-sky-400')) {
                activeTab = btn.id.replace('btn-sub-', '');
            }
        });
        
        const container = document.querySelector(`#sub-content-${activeTab} .activity-list`);
        if (!container) return;

        const timeFilter = document.getElementById('activity-time-filter')?.value || 'all';
        const activeDiv = document.getElementById(`sub-content-${activeTab}`);
        
        // Hiển thị hiệu ứng skeleton/loading đẹp mắt
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center py-12 text-center text-slate-500 animate-pulse">
                <span class="material-symbols-outlined text-4xl mb-2 animate-spin" style="display: inline-block;">progress_activity</span>
                <p class="text-sm">Đang lọc hoạt động...</p>
            </div>
        `;
        
        // Xóa nút "Xem thêm" cũ nếu có
        const loadMoreBtnContainer = document.querySelector(`#sub-content-${activeTab} .btn-load-more-container`);
        if (loadMoreBtnContainer) {
            loadMoreBtnContainer.remove();
        }

        try {
            const fetchType = activeTab === 'commented' ? 'comments' : activeTab;
            const response = await fetch(`/profile/activity/${fetchType}?page=1&time_filter=${timeFilter}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                // Thay thế dữ liệu trang 1
                container.innerHTML = data.html;

                // Lưu trạng thái bộ lọc đã tải thành công cho tab
                if (activeDiv) {
                    activeDiv.setAttribute('data-loaded-filter', timeFilter);
                }
                
                // Đồng bộ số đếm mới lên tiêu đề tab
                if (data.likedCount !== undefined) {
                    document.getElementById('btn-sub-liked').innerHTML = `Đã thích (${data.likedCount})`;
                }
                if (data.commentsCount !== undefined) {
                    document.getElementById('btn-sub-commented').innerHTML = `Bình luận (${data.commentsCount})`;
                }
                if (data.savedCount !== undefined) {
                    document.getElementById('btn-sub-saved').innerHTML = `Đã lưu (${data.savedCount})`;
                }
                
                // Xử lý hiển thị nút Xem thêm
                if (data.hasMore) {
                    activeDiv.insertAdjacentHTML('beforeend', `
                        <div class="text-center pt-2 btn-load-more-container">
                            <button type="button" onclick="loadMoreActivity('${activeTab}')" id="btn-load-more-${activeTab}" data-page="2" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/5 transition-all">
                                Xem thêm
                            </button>
                        </div>
                    `);
                }
                
                // Nếu dữ liệu trống, hiển thị thông báo trống tương ứng vào thẳng trong container
                const currentCount = activeTab === 'liked' ? data.likedCount : (activeTab === 'commented' ? data.commentsCount : data.savedCount);
                if (currentCount === 0 || !data.html.trim()) {
                    let icon = 'favorite';
                    let text = 'Bạn chưa thích bài viết nào trong khoảng thời gian này.';
                    if (activeTab === 'commented') {
                        icon = 'chat_bubble';
                        text = 'Bạn chưa có bình luận nào trong khoảng thời gian này.';
                    } else if (activeTab === 'saved') {
                        icon = 'bookmark';
                        text = 'Bạn chưa lưu bài viết nào trong khoảng thời gian này.';
                    }
                    
                    container.innerHTML = `
                        <div class="text-center py-12 text-slate-500 animate-fade-in">
                            <span class="material-symbols-outlined text-4xl mb-2">${icon}</span>
                            <p class="text-sm">${text}</p>
                        </div>
                    `;
                }
            } else {
                container.innerHTML = `<p class="text-center py-12 text-red-400">Không thể tải hoạt động. Vui lòng thử lại.</p>`;
            }
        } catch (error) {
            console.error('Lỗi khi lọc hoạt động:', error);
            container.innerHTML = `<p class="text-center py-12 text-red-400">Có lỗi xảy ra. Vui lòng thử lại.</p>`;
        }
    }

    window.loadMoreActivity = async function(type) {
        const btn = document.getElementById(`btn-load-more-${type}`);
        if (!btn) return;
        
        const page = btn.getAttribute('data-page');
        const container = document.querySelector(`#sub-content-${type} .activity-list`);
        const timeFilter = document.getElementById('activity-time-filter')?.value || 'all';
        
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[16px] mr-1" style="display: inline-block; vertical-align: middle;">progress_activity</span> Đang tải...';
        
        try {
            const fetchType = type === 'commented' ? 'comments' : type;
            const response = await fetch(`/profile/activity/${fetchType}?page=${page}&time_filter=${timeFilter}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                // Nối HTML mới vào danh sách
                container.insertAdjacentHTML('beforeend', data.html);
                
                // Đồng bộ số đếm mới lên tiêu đề tab
                if (data.likedCount !== undefined) {
                    document.getElementById('btn-sub-liked').innerHTML = `Đã thích (${data.likedCount})`;
                }
                if (data.commentsCount !== undefined) {
                    document.getElementById('btn-sub-commented').innerHTML = `Bình luận (${data.commentsCount})`;
                }
                if (data.savedCount !== undefined) {
                    document.getElementById('btn-sub-saved').innerHTML = `Đã lưu (${data.savedCount})`;
                }

                if (data.hasMore) {
                    btn.setAttribute('data-page', parseInt(page) + 1);
                    btn.disabled = false;
                    btn.innerHTML = 'Xem thêm';
                } else {
                    // Xóa container nút Xem thêm nếu đã hết
                    btn.closest('.btn-load-more-container').remove();
                }
            } else {
                btn.disabled = false;
                btn.innerHTML = 'Thử lại';
            }
        } catch (error) {
            console.error('Lỗi khi tải nhật ký hoạt động:', error);
            btn.disabled = false;
            btn.innerHTML = 'Thử lại';
        }
    }
</script>

@endsection