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
        <div class="h-64 w-full bg-slate-900 overflow-hidden">
            <img
                class="w-full h-full object-cover opacity-60"
                data-alt="Cover photo"
                src="{{ !empty($user->anh_bia) ? asset('storage/' . $user->anh_bia) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuAE8EPzz-gX79DnqAhi0_StOHC91uLm5YDBZwVLWbndwUQ6uK_rUjvdGCmWgdMz8vhDT_KZFa7NE8T8ihfKelL_dO6jLGlJ8sd5AE6svxEDyG59LqoA7KF1QD7pTUv6D9M81ss6aD-J7fp3RxaxKdLt7IZjLiJaECpsYmxZooT54hRgR9bp_99vkrKdEiEEJLPZHCE2LjfSk9G8-idX4qneAxORxh9pxv-y-X3poNr_QVPvLMaqwrEV3YZPUe4RW_tg-_TiSfiyeV4' }}"
            />
        </div>

        <div class="px-6 -mt-16 sm:-mt-24 flex flex-col items-start relative z-10">
            <div class="flex items-end justify-between w-full">
                <div class="relative group">
                    <div class="w-32 h-32 sm:w-44 sm:h-44 rounded-full border-4 border-background overflow-hidden glass-panel-elevated">
                        <img
                            class="w-full h-full object-cover"
                            alt="{{ $user->name }}"
                            src="{{ $user->anh_dai_dien ? asset('storage/' . $user->anh_dai_dien) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuBB2X-acXW_1cf-FYDgTbvrKUbxQxX6Cg299CVHtaNYCQRKITR_1PwPyZywPFBetIgi2qXpqS9JURFRUyt2YRSV7-DSnR4EkHhM7DuHZ8EI0F6kYBWYbNcwgDetrejvxYyfxV8o7L84z4zC_cIqlzQMLve0LR2szBxaT8jjJeINxPtQT5Wi3bLnJAUcvqBnP3dGvxatXkae_gQm1vz6CGeCT0zQJmj55dgFhyrrMYSdBkd66GagHIasFgIOcEuJehCi0TxWORmK-W0' }}"
                        />
                    </div>
                    @if($isOwnProfile)
                        <div class="absolute bottom-2 right-2 p-2 bg-sky-400 rounded-full text-on-primary shadow-lg cursor-pointer hover:scale-110 transition-transform hidden group-hover:block">
                            <span class="material-symbols-outlined text-base" data-icon="photo_camera">photo_camera</span>
                        </div>
                    @endif
                </div>

                <div class="flex gap-3 mb-2">
                    @if($isOwnProfile)
                        <a href="{{ route('profile.edit') }}" class="px-6 py-2 rounded-xl glass-panel text-on-surface font-semibold hover:bg-white/10 transition-all active:scale-95 border border-sky-400/20 inline-flex items-center">
                            Chỉnh sửa hồ sơ
                        </a>
                    @elseif(auth()->check())
                        <button id="follow-btn" data-user-id="{{ $user->id }}" class="px-6 py-2 rounded-xl glass-panel text-sky-300 font-semibold hover:bg-white/10 transition-all border border-sky-400/20">
                            {{ $isFollowing ? 'Bỏ theo dõi' : 'Theo dõi' }}
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2 rounded-xl glass-panel text-sky-300 font-semibold hover:bg-white/10 transition-all border border-sky-400/20 inline-flex items-center">
                            Đăng nhập để theo dõi
                        </a>
                    @endif
                    <button
                        type="button"
                        class="p-2 rounded-xl glass-panel text-on-surface hover:bg-white/10 transition-all border border-sky-400/20"
                        onclick="navigator.clipboard.writeText('{{ $profileUrl }}')"
                        title="Sao chép liên kết hồ sơ"
                    >
                        <span class="material-symbols-outlined" data-icon="share">share</span>
                    </button>
                </div>
            </div>

            <div class="mt-4 space-y-1">
                <div class="flex items-center gap-2">
                    <h1 class="text-3xl font-bold text-on-surface tracking-tight">{{ $user->name }}</h1>
                    @if($user->da_xac_thuc)
                        <span class="material-symbols-outlined text-sky-400 text-xl" data-icon="verified" style="font-variation-settings: 'FILL' 1;">verified</span>
                    @endif
                </div>
                <p class="text-slate-400 font-medium text-lg">{{ '@' . ($user->ten_dang_nhap ?? 'nguoidung') }}</p>
            </div>

            <div class="mt-4 max-w-2xl">
                <p class="text-on-surface-variant leading-relaxed">
                    {{ $user->tieu_su ?? 'Nhà thiết kế sản phẩm số & Người yêu thích công nghệ. Đang khám phá những giới hạn mới của giao diện Glassmorphism và Web3.' }}
                </p>
            </div>

            <div class="flex flex-wrap gap-6 mt-6">
                <div class="flex items-center gap-1.5 group cursor-pointer">
                    <span class="font-bold text-on-surface group-hover:text-sky-300">{{ number_format($user->following_count ?? 0) }}</span>
                    <span class="text-slate-400 group-hover:text-slate-300">Đang theo dõi</span>
                </div>
                <div class="flex items-center gap-1.5 group cursor-pointer">
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

    <div class="mt-8 border-b border-sky-400/10 sticky top-16 bg-[#0a0e1a]/80 backdrop-blur-md z-30">
        <div class="flex px-2 overflow-x-auto no-scrollbar">
            <button class="px-6 py-4 text-sky-300 border-b-2 border-sky-400 font-bold whitespace-nowrap transition-all">Bài đăng</button>
            <button class="px-6 py-4 text-slate-400 hover:text-sky-200 hover:bg-white/5 font-medium whitespace-nowrap transition-all">Phản hồi</button>
            <button class="px-6 py-4 text-slate-400 hover:text-sky-200 hover:bg-white/5 font-medium whitespace-nowrap transition-all">Hình ảnh</button>
            <button class="px-6 py-4 text-slate-400 hover:text-sky-200 hover:bg-white/5 font-medium whitespace-nowrap transition-all">Thích</button>
        </div>
    </div>

    <div class="px-6 mt-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1 space-y-6">
                <div class="glass-panel rounded-2xl p-5 space-y-4">
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
                        <div class="flex items-center gap-3 text-slate-300">
                            <span class="material-symbols-outlined text-sky-400/70" data-icon="work">work</span>
                            <span class="text-sm">UI/UX Designer tại NHOMJ Lab</span>
                        </div>
                    </div>
                </div>

                <div class="glass-panel rounded-2xl p-5 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-sky-300">Phương tiện</h3>
                        <button class="text-xs text-sky-300 hover:underline">Xem tất cả</button>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="aspect-square rounded-lg overflow-hidden bg-slate-800"><img class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAcJ-QooLwGSegkbQYTxdBq5P5q5gaQT4yoYAwnrdJ3lBsIFFXLjdGUEVrUw1JiEQsL-Px0AqQGCSndymq7KsfrKTrLN0nZKUYGnkVvYwaPlgja8OMe5cq_88kvnV7sc_0e7kX1gWZth-L7hJWrd7amohSFhp7r-e6Q5bUDaTV7Ocg9pFRRoVXztX_mDAlQDcb5YH_pfqHKTwk1TOtDo8g2sv8vjim10WuBLKp3tH-k9HFEDxRlMhpVUffRR40gicaB9f5phb0iFkI"/></div>
                        <div class="aspect-square rounded-lg overflow-hidden bg-slate-800"><img class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDJ1pV1EdqRMUvrmPQV1YmlQN1SQbVVZKK_AjI8Fq5GGKlVqkeZ3pkGcAlZgM9oBcOKLFRRb79FW2GPHH5LruN4B1KEZewuwSXuwM6HtQ85TpTGO9kzT12mPiIKXWGjHx0geVnHouKJO5p6r3cwlA3_FP8py_xR5CsYtGphnkotu_MeA-9gZ4d8mY7iq63EI0sad2twkWs_IBa44lh_pcCI8AUk0d7MSJ-VbVJKh_2Orn29kLLp2xbVLpQ0u2Ct5bnfiVQGAPCo170"/></div>
                        <div class="aspect-square rounded-lg overflow-hidden bg-slate-800"><img class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDVLN5mzLMVOyuWiXAutttI_EloRRs0k2frSwqkUe03DyV9DcDIGDh-Tt9JYVE3DhIwojrKvMiWhOvBagxt0-lEXoICAeW_PXYL-wBYggxCqY-NFNs7Go34j_kvXEO6GEw0ZZOAftN8Ez3sYrH1qNohrrg19sYc51sFuiZto54EYo-VZPbNeV4Fep00902OvhVAp0xROh3uUhHgFyUQcFXfE-BK48aTCvjrV-o8JblcWa4OLCkLYzb9GFFcStm3l3SnlusKETi_T_M"/></div>
                        <div class="aspect-square rounded-lg overflow-hidden bg-slate-800"><img class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCLD7ngkFLziGv9MzlIa5dyTTUUxIfiCAlKMY1sSxAWvU9q1JgVxxszn6Z3lzKKAuNya2eQSqXkQn2ibNUNQ6RsxxDzuegPYwmkEL5xgbtkSGuPF80cLWBRyrDys3YtRaWojkKy2mO8rF4ugg8v8RMpm8NMoycOA5KHGbL-gw5_mNjJ2190O1Gql2KT3bBWPV2SbdtM4KKY9S8FcYEGPYfq1Do25twrzgUU1cJy60q2jBvHAWAUALcW6T1Vk-lGeP7iW_LUdYgSzhM"/></div>
                        <div class="aspect-square rounded-lg overflow-hidden bg-slate-800"><img class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBAgiicC-g8lvn-X0_aktsjCh_w-lEMB0tLQtzkGbzb3kHkEUSYA206VV_FuNupxt0gQuvO7zr3BDIVqS3mN13A5SRsMdBIql84sDnRYxhcbB8a0QgK1aiHMxaun4LlTxV92DfatsEKXQB_wejyCJMWB3UYusrsUIsXGurTZUYYQq_3RYgCq0SHAdlZEd08FC389FjOE6gKyIjM2oy8OVx4NIBgjPWoxXz71rE4xUIHDt76nRtBy1FdotYtbvK99jT4RJ8kTr7tUjQ"/></div>
                        <div class="aspect-square rounded-lg overflow-hidden bg-slate-800"><img class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDsTkjQnhVrNwndcrogjc0i8VzJpIGp2G-6X9ahqHzbJmvrE_sXtHWZMql1A9GuQeAW90tgZuf_IZiPt_XqCFeC7xOFb1HcHjtPG2NnJ5e99P-QKr46bnN88MGK3N5vbk2-jMvECmuVSxY7WCW9UCvY2tVtIdNFXpbxqtQxvXgGxn9_o6dT-18aaKk9hDQSJbepRA8YMWcmw4ppPIHzFruvDT1HiirQoZGI66810-em0UneqhxOE24PHi_amXdzgXdCwDCHA2yoh3k"/></div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 space-y-6">
                @if(isset($posts) && $posts->count() > 0)
                    @foreach($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                @else
                    <article class="glass-panel rounded-2xl p-6 hover:border-sky-400/30 transition-all group">
                        <div class="flex gap-4">
                            <img
                                class="w-12 h-12 rounded-full border border-sky-400/20 shrink-0"
                                src="{{ !empty($user->anh_dai_dien) ? asset('storage/' . $user->anh_dai_dien) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuD9qskgk4XFpsZBYKZc4rdDypD28T8KdOwfnG808klKqwpLfAh0i6HBd2d0MOLnmwV9V9-EqiVsS4taYDJ5eJtvloI7UlY_mfzHTAZcq8EUkxnI6ZZ2bFHhDU3BP6hg0HqdlUPwiFNOoVtsWepab4DN8U8fPQLkZRVNixZmxxi2OH6ozJEYvCuUQoTNNoYrIy_NHBBU4ki1udM8dLTr9zqhSuJEWvjDJxLDsF5F7w17Ftjw50F3a_chMuJpHT4GiqavrorCqAm3bmk' }}"
                            />
                            <div class="flex-1 space-y-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="font-bold text-on-surface hover:text-sky-300 cursor-pointer">{{ $user->name }}</span>
                                        <span class="text-slate-500 text-sm ml-2">{{ '@' . ($user->ten_dang_nhap ?? 'nguoidung') }} · 2h</span>
                                    </div>
                                    <button class="text-slate-500 hover:text-sky-300"><span class="material-symbols-outlined" data-icon="more_horiz">more_horiz</span></button>
                                </div>
                                <p class="text-on-surface-variant leading-relaxed">
                                    Vừa hoàn thiện xong Concept UI cho dự án NHOMJ mới. Phong cách Glacier thực sự mang lại cảm giác cao cấp và hiện đại hơn cho trải nghiệm người dùng. Ý kiến các bạn thế nào?
                                </p>
                                <div class="rounded-2xl overflow-hidden border border-sky-400/10 mt-3 aspect-video bg-slate-900">
                                    <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBaz-tnlcYkz-gWZiH-UT6jdm67JAHuTNPKhw8p3QV41fkx9ngQxGLeDRwjtYeLSmVgVIRcGxuGhNOcHAmYooWM6-ZEA3QBYwwbo47EVBQW1Mq7VEz5rvMJWPhnKaqWY-6VeWO9IOSnF-kNW9MjnqrRQN--QlVidWtaO1fkXCQTWtMsj6zpZCEXOpdssqh8hGjDXVcY0b9V6T2MwwXrdokoVVoZKdbicXU5sxf5bzaJHAhxK9n_Oh9EUwk4RwCKP8h0fze1nyziCNE"/>
                                </div>
                                <div class="flex items-center justify-between pt-3 text-slate-400 max-w-sm">
                                    <button class="flex items-center gap-2 hover:text-sky-300 transition-colors group/btn"><span class="material-symbols-outlined text-xl group-hover/btn:bg-sky-400/10 p-2 rounded-full" data-icon="chat_bubble">chat_bubble</span><span class="text-sm">24</span></button>
                                    <button class="flex items-center gap-2 hover:text-emerald-400 transition-colors group/btn"><span class="material-symbols-outlined text-xl group-hover/btn:bg-emerald-400/10 p-2 rounded-full" data-icon="repost">retweet</span><span class="text-sm">12</span></button>
                                    <button class="flex items-center gap-2 hover:text-rose-400 transition-colors group/btn"><span class="material-symbols-outlined text-xl group-hover/btn:bg-rose-400/10 p-2 rounded-full" data-icon="favorite">favorite</span><span class="text-sm">1.4K</span></button>
                                    <button class="flex items-center gap-2 hover:text-sky-300 transition-colors group/btn"><span class="material-symbols-outlined text-xl group-hover/btn:bg-sky-400/10 p-2 rounded-full" data-icon="bar_chart">bar_chart</span><span class="text-sm">12.5K</span></button>
                                </div>
                            </div>
                        </div>
                    </article>
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
