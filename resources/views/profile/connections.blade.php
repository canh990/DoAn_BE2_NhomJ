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
                        <div class="flex items-center justify-between p-4 rounded-xl border border-sky-400/10 hover:bg-white/5 transition-colors">
                            <a href="{{ route('profile.public', ['username' => $connection->ten_dang_nhap]) }}" class="flex items-center gap-3">
                                <img src="{{ $connection->anh_dai_dien ? asset('storage/' . $connection->anh_dai_dien) : 'https://ui-avatars.com/api/?name='.urlencode($connection->name).'&background=random' }}" 
                                     alt="{{ $connection->name }}" 
                                     class="h-12 w-12 rounded-full object-cover">
                                <div>
                                    <h4 class="font-bold text-on-surface flex items-center gap-1">
                                        {{ $connection->name }}
                                        @if($connection->da_xac_thuc)
                                            <span class="material-symbols-outlined text-sm text-sky-400" data-icon="verified" style="font-variation-settings: 'FILL' 1;">verified</span>
                                        @endif
                                    </h4>
                                    <p class="text-sm text-slate-400">{{ '@' . $connection->ten_dang_nhap }}</p>
                                </div>
                            </a>
                            
                            @if(auth()->check() && auth()->id() !== $connection->id)
                                @php
                                    $isFollowingConnection = auth()->user()->following()->where('nguoi_duoc_theo_doi_id', $connection->id)->exists();
                                @endphp
                                <button class="follow-btn rounded-xl border border-sky-400/20 px-4 py-1.5 text-sm font-semibold transition-all hover:bg-white/10 {{ $isFollowingConnection ? 'text-slate-300' : 'text-sky-300' }}" 
                                        data-user-id="{{ $connection->id }}">
                                    {{ $isFollowingConnection ? 'Bỏ theo dõi' : 'Theo dõi' }}
                                </button>
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
