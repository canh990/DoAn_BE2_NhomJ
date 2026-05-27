@extends('layouts.app')

@section('title', ($type === 'followers' ? 'Người theo dõi' : 'Đang theo dõi') . ' - ' . ($user->name ?? 'Người dùng'))

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('profile.public', ['username' => $user->ten_dang_nhap]) }}" class="flex h-10 w-10 items-center justify-center rounded-full glass-panel hover:bg-white/10 transition-colors">
            <span class="material-symbols-outlined text-sky-400">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-on-surface">{{ $user->name }}</h1>
    </div>

    <div class="glass-panel rounded-2xl overflow-hidden">
        <div class="flex border-b border-sky-400/10 bg-[#0a0e1a]/80">
            <a href="{{ route('profile.followers', ['username' => $user->ten_dang_nhap]) }}" 
               class="flex-1 text-center py-4 font-bold transition-all {{ $type === 'followers' ? 'border-b-2 border-sky-400 text-sky-300' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }}">
                Người theo dõi ({{ number_format($user->followers_count ?? 0) }})
            </a>
            <a href="{{ route('profile.following', ['username' => $user->ten_dang_nhap]) }}" 
               class="flex-1 text-center py-4 font-bold transition-all {{ $type === 'following' ? 'border-b-2 border-sky-400 text-sky-300' : 'text-slate-400 hover:bg-white/5 hover:text-sky-200' }}">
                Đang theo dõi ({{ number_format($user->following_count ?? 0) }})
            </a>
        </div>

        <div class="p-4 sm:p-6">
            @if($connections->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($connections as $connection)
                        <div class="flex items-center justify-between p-4 rounded-xl border border-sky-400/10 hover:bg-white/5 transition-colors gap-4">
                            <a href="{{ route('profile.public', ['username' => $connection->ten_dang_nhap]) }}" class="flex items-center gap-3 min-w-0">
                                <img src="{{ $connection->avatar_url }}" 
                                     alt="{{ $connection->name }}" 
                                     class="h-12 w-12 rounded-full object-cover shrink-0">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-on-surface flex items-center gap-1 truncate">
                                        {{ $connection->name }}
                                        @if($connection->da_xac_thuc)
                                            <span class="material-symbols-outlined text-sm text-sky-400 shrink-0" data-icon="verified" style="font-variation-settings: 'FILL' 1;">verified</span>
                                        @endif
                                    </h4>
                                    <p class="text-sm text-slate-400 truncate">{{ '@' . $connection->ten_dang_nhap }}</p>
                                </div>
                            </a>
                            
                            @if(auth()->check())
                                @if(auth()->id() === $user->id && $type === 'followers' && isset($connection->pivot) && $connection->pivot->trang_thai === 'cho_chap_nhan')
                                    <div class="flex items-center gap-2 shrink-0" id="action-container-{{ $connection->id }}">
                                        <button onclick="approveFollower({{ $connection->id }}, this)" class="px-3.5 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition-all hover:scale-[1.03] active:scale-95 flex items-center gap-1 whitespace-nowrap shrink-0">
                                            <span class="material-symbols-outlined text-[14px]">done</span>
                                            Chấp nhận
                                        </button>
                                        <button onclick="declineFollower({{ $connection->id }}, this)" class="px-3.5 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-bold rounded-xl border border-rose-500/20 transition-all hover:scale-[1.03] active:scale-95 flex items-center gap-1 whitespace-nowrap shrink-0">
                                            <span class="material-symbols-outlined text-[14px]">close</span>
                                            Từ chối
                                        </button>
                                    </div>
                                @elseif(auth()->id() !== $connection->id)
                                    @php
                                        $isFollowingConnection = auth()->user()->following()->where('nguoi_duoc_theo_doi_id', $connection->id)->exists();
                                    @endphp
                                    <button class="follow-btn rounded-xl border border-sky-400/20 px-4 py-1.5 text-sm font-semibold transition-all hover:bg-white/10 whitespace-nowrap shrink-0 {{ $isFollowingConnection ? 'text-slate-300' : 'text-sky-300' }}" 
                                            data-user-id="{{ $connection->id }}">
                                        {{ $isFollowingConnection ? 'Bỏ theo dõi' : 'Theo dõi' }}
                                    </button>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6 flex justify-center">
                    {{ $connections->links() }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-800/50 text-slate-500">
                        <span class="material-symbols-outlined text-3xl" data-icon="group">group</span>
                    </div>
                    <h3 class="text-lg font-bold text-on-surface">Chưa có {{ $type === 'followers' ? 'người theo dõi' : 'người đang theo dõi' }}</h3>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    async function approveFollower(followerId, btn) {
        try {
            const response = await fetch(`/user/${followerId}/accept-follow`, {
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
                if (window.showToast) window.showToast(data.message, 'success');
                
                // Thay thế nút bằng badge Đã chấp nhận
                const container = document.getElementById(`action-container-${followerId}`);
                if (container) {
                    container.innerHTML = `<span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-xs font-bold rounded-lg border border-emerald-500/20">Người theo dõi</span>`;
                }
            }
        } catch (error) {
            console.error('Lỗi:', error);
        }
    }

    async function declineFollower(followerId, btn) {
        try {
            const response = await fetch(`/user/${followerId}/decline-follow`, {
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
                if (window.showToast) window.showToast(data.message, 'success');
                
                // Ẩn/xóa card của follower này
                const card = btn.closest('.flex.items-center.justify-between');
                if (card) {
                    card.style.opacity = '0.5';
                    card.style.pointerEvents = 'none';
                    setTimeout(() => card.remove(), 500);
                }
            }
        } catch (error) {
            console.error('Lỗi:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const followBtns = document.querySelectorAll('.follow-btn');
        
        followBtns.forEach(btn => {
            btn.addEventListener('click', async function() {
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
                        if (data.is_following) {
                            this.classList.remove('text-sky-300');
                            this.classList.add('text-slate-300');
                        } else {
                            this.classList.remove('text-slate-300');
                            this.classList.add('text-sky-300');
                        }
                    }
                } catch (error) {
                    console.error('Lỗi khi thực hiện theo dõi:', error);
                }
            });
        });
    });
</script>
@endsection
