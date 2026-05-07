@extends('layouts.app')

@section('title', 'Trang cá nhân - ' . ($user->name ?? 'Người dùng'))

@section('content')
@php
$isOwnProfile = auth()->check() && auth()->id() === $user->id;
$isFollowing = auth()->check() && ! $isOwnProfile
? auth()->user()->following()->where('nguoi_duoc_theo_doi_id', $user->id)->exists()
: false;
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
                    <button id="follow-btn" data-user-id="{{ $user->id }}" class="rounded-xl border border-sky-400/20 glass-panel px-6 py-2 font-semibold text-sky-300 transition-all hover:bg-white/10">
                        {{ $isFollowing ? 'Bỏ theo dõi' : 'Theo dõi' }}
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl border border-sky-400/20 glass-panel px-6 py-2 font-semibold text-sky-300 transition-all hover:bg-white/10">
                        Đăng nhập để theo dõi
                    </a>
                    @endif

                    <button
                        type="button"
                        class="rounded-xl border border-sky-400/20 glass-panel p-2 text-on-surface transition-all hover:bg-white/10"
                        onclick="navigator.clipboard.writeText('{{ $profileUrl }}')"
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
                <div class="group flex cursor-pointer items-center gap-1.5">
                    <span class="font-bold text-on-surface group-hover:text-sky-300">{{ number_format($user->following_count ?? 0) }}</span>
                    <span class="text-slate-400 group-hover:text-slate-300">Đang theo dõi</span>
                </div>
                <div class="group flex cursor-pointer items-center gap-1.5">
                    <span id="followers-count" class="font-bold text-on-surface group-hover:text-sky-300">{{ number_format($user->followers_count ?? 0) }}</span>
                    <span class="text-slate-400 group-hover:text-slate-300">Người theo dõi</span>
                </div>
                <div class="flex items-center gap-1.5 text-slate-400">
                    <span class="material-symbols-outlined text-sm" data-icon="calendar_month">calendar_month</span>
                    <span>Đã tham gia {{ isset($user->created_at) ? $user->created_at->format('m/Y') : '10/2023' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="sticky top-16 z-30 mt-8 border-b border-sky-400/10 bg-[#0a0e1a]/80 backdrop-blur-md">
        <div class="no-scrollbar flex overflow-x-auto px-2">
            <button class="whitespace-nowrap border-b-2 border-sky-400 px-6 py-4 font-bold text-sky-300 transition-all">Bài đăng</button>
            <button class="whitespace-nowrap px-6 py-4 font-medium text-slate-400 transition-all hover:bg-white/5 hover:text-sky-200">Phản hồi</button>
            <button class="whitespace-nowrap px-6 py-4 font-medium text-slate-400 transition-all hover:bg-white/5 hover:text-sky-200">Hình ảnh</button>
            <button class="whitespace-nowrap px-6 py-4 font-medium text-slate-400 transition-all hover:bg-white/5 hover:text-sky-200">Thích</button>
        </div>
    </div>

    <div class="mt-8 px-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="space-y-6 md:col-span-1">
                <div class="glass-panel space-y-4 rounded-2xl p-5">
                    <h3 class="text-lg font-bold text-sky-300">Giới thiệu</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-slate-300">
                            <span class="material-symbols-outlined text-sky-400/70" data-icon="location_on">location_on</span>
                            <span class="text-sm">{{ $user->noi_o ?? 'Hà Nội, Việt Nam' }}</span>
                        </div>
                        @if(!empty($user->ngay_sinh) && $user->quyen_rieng_tu !== 'rieng_tu')
                        <div class="flex items-center gap-3 text-slate-300">
                            <span class="material-symbols-outlined text-sky-400/70" data-icon="cake">cake</span>
                            <span class="text-sm">{{ \Illuminate\Support\Carbon::parse($user->ngay_sinh)->format('d/m/Y') }}</span>
                        </div>
                        @endif

                    </div>
                </div>

                <div class="glass-panel space-y-4 rounded-2xl p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-sky-300">Phương tiện</h3>
                        <button class="text-xs text-sky-300 hover:underline">Xem tất cả</button>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="aspect-square overflow-hidden rounded-lg bg-slate-800"><img class="h-full w-full object-cover transition-transform duration-500 hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAcJ-QooLwGSegkbQYTxdBq5P5q5gaQT4yoYAwnrdJ3lBsIFFXLjdGUEVrUw1JiEQsL-Px0AqQGCSndymq7KsfrKTrLN0nZKUYGnkVvYwaPlgja8OMe5cq_88kvnV7sc_0e7kX1gWZth-L7hJWrd7amohSFhp7r-e6Q5bUDaTV7Ocg9pFRRoVXztX_mDAlQDcb5YH_pfqHKTwk1TOtDo8g2sv8vjim10WuBLKp3tH-k9HFEDxRlMhpVUffRR40gicaB9f5phb0iFkI" /></div>
                        <div class="aspect-square overflow-hidden rounded-lg bg-slate-800"><img class="h-full w-full object-cover transition-transform duration-500 hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDJ1pV1EdqRMUvrmPQV1YmlQN1SQbVVZKK_AjI8Fq5GGKlVqkeZ3pkGcAlZgM9oBcOKLFRRb79FW2GPHH5LruN4B1KEZewuwSXuwM6HtQ85TpTGO9kzT12mPiIKXWGjHx0geVnHouKJO5p6r3cwlA3_FP8py_xR5CsYtGphnkotu_MeA-9gZ4d8mY7iq63EI0sad2twkWs_IBa44lh_pcCI8AUk0d7MSJ-VbVJKh_2Orn29kLLp2xbVLpQ0u2Ct5bnfiVQGAPCo170" /></div>
                        <div class="aspect-square overflow-hidden rounded-lg bg-slate-800"><img class="h-full w-full object-cover transition-transform duration-500 hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDVLN5mzLMVOyuWiXAutttI_EloRRs0k2frSwqkUe03DyV9DcDIGDh-Tt9JYVE3DhIwojrKvMiWhOvBagxt0-lEXoICAeW_PXYL-wBYggxCqY-NFNs7Go34j_kvXEO6GEw0ZZOAftN8Ez3sYrH1qNohrrg19sYc51sFuiZto54EYo-VZPbNeV4Fep00902OvhVAp0xROh3uUhHgFyUQcFXfE-BK48aTCvjrV-o8JblcWa4OLCkLYzb9GFFcStm3l3SnlusKETi_T_M" /></div>
                        <div class="aspect-square overflow-hidden rounded-lg bg-slate-800"><img class="h-full w-full object-cover transition-transform duration-500 hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCLD7ngkFLziGv9MzlIa5dyTTUUxIfiCAlKMY1sSxAWvU9q1JgVxxszn6Z3lzKKAuNya2eQSqXkQn2ibNUNQ6RsxxDzuegPYwmkEL5xgbtkSGuPF80cLWBRyrDys3YtRaWojkKy2mO8rF4ugg8v8RMpm8NMoycOA5KHGbL-gw5_mNjJ2190O1Gql2KT3bBWPV2SbdtM4KKY9S8FcYEGPYfq1Do25twrzgUU1cJy60q2jBvHAWAUALcW6T1Vk-lGeP7iW_LUdYgSzhM" /></div>
                        <div class="aspect-square overflow-hidden rounded-lg bg-slate-800"><img class="h-full w-full object-cover transition-transform duration-500 hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBAgiicC-g8lvn-X0_aktsjCh_w-lEMB0tLQtzkGbzb3kHkEUSYA206VV_FuNupxt0gQuvO7zr3BDIVqS3mN13A5SRsMdBIql84sDnRYxhcbB8a0QgK1aiHMxaun4LlTxV92DfatsEKXQB_wejyCJMWB3UYusrsUIsXGurTZUYYQq_3RYgCq0SHAdlZEd08FC389FjOE6gKyIjM2oy8OVx4NIBgjPWoxXz71rE4xUIHDt76nRtBy1FdotYtbvK99jT4RJ8kTr7tUjQ" /></div>
                        <div class="aspect-square overflow-hidden rounded-lg bg-slate-800"><img class="h-full w-full object-cover transition-transform duration-500 hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDsTkjQnhVrNwndcrogjc0i8VzJpIGp2G-6X9ahqHzbJmvrE_sXtHWZMql1A9GuQeAW90tgZuf_IZiPt_XqCFeC7xOFb1HcHjtPG2NnJ5e99P-QKr46bnN88MGK3N5vbk2-jMvECmuVSxY7WCW9UCvY2tVtIdNFXpbxqtQxvXgGxn9_o6dT-18aaKk9hDQSJbepRA8YMWcmw4ppPIHzFruvDT1HiirQoZGI66810-em0UneqhxOE24PHi_amXdzgXdCwDCHA2yoh3k" /></div>
                    </div>
                </div>
            </div>

            <div class="space-y-6 md:col-span-2">
                @if(isset($posts) && $posts->count() > 0)
                @foreach($posts as $post)
                <x-post-card :post="$post" />
                @endforeach
                @else
                {{-- Phần thay đổi ở đây --}}
                <div class="glass-panel flex flex-col items-center justify-center rounded-3xl p-12 text-center">
                    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-800/50 text-slate-500">
                        <span class="material-symbols-outlined text-4xl" data-icon="post_add">post_add</span>
                    </div>
                    <h3 class="text-xl font-bold text-on-surface">Chưa có bài đăng nào</h3>
                    <p class="mt-2 text-slate-400">Người dùng này vẫn chưa chia sẻ bài viết nào với cộng đồng.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
                        this.innerText = data.is_following ? 'Bỏ theo dõi' : 'Theo dõi';
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