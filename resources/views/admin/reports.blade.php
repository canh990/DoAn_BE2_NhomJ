@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold bg-gradient-to-r from-amber-400 to-rose-500 bg-clip-text text-transparent flex items-center gap-3">
                <span class="material-symbols-outlined text-[36px] text-amber-400">admin_panel_settings</span>
                Quản lý Báo cáo Vi phạm
            </h1>
            <p class="text-sm text-slate-400 mt-1">Xem, lọc và xử lý các nội dung bị người dùng báo cáo vi phạm tiêu chuẩn cộng đồng.</p>
        </div>
        
        <!-- Quick stats summary card -->
        <div class="glass-panel px-6 py-3 flex gap-6 items-center rounded-2xl border border-white/5">
            <div class="text-center">
                <span class="text-xs text-slate-500 block uppercase font-bold tracking-wider">Tổng số</span>
                <span class="text-xl font-extrabold text-white">{{ $reports->total() }}</span>
            </div>
            <div class="h-8 w-px bg-white/10"></div>
            <div class="text-center">
                <span class="text-xs text-slate-500 block uppercase font-bold tracking-wider">Chờ xử lý</span>
                <span class="text-xl font-extrabold text-amber-400">
                    {{ \App\Models\BaoCao::where('trang_thai', 'cho_xu_ly')->count() }}
                </span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="glass-panel p-6 rounded-2xl mb-8 border border-white/5 bg-slate-900/40">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="type" class="block text-xs font-semibold text-slate-400 mb-2 uppercase tracking-wider">Loại nội dung</label>
                <select name="type" id="type" class="w-full bg-slate-950 border border-white/10 rounded-xl focus:border-amber-400 focus:ring-0 text-slate-200 text-sm py-2.5 px-3">
                    <option value="" {{ request('type') === '' ? 'selected' : '' }}>Tất cả</option>
                    <option value="bai_viet" {{ request('type') === 'bai_viet' ? 'selected' : '' }}>Bài viết</option>
                    <option value="binh_luan" {{ request('type') === 'binh_luan' ? 'selected' : '' }}>Bình luận</option>
                    <option value="nguoi_dung" {{ request('type') === 'nguoi_dung' ? 'selected' : '' }}>Người dùng bị báo cáo</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label for="status" class="block text-xs font-semibold text-slate-400 mb-2 uppercase tracking-wider">Trạng thái</label>
                <select name="status" id="status" class="w-full bg-slate-950 border border-white/10 rounded-xl focus:border-amber-400 focus:ring-0 text-slate-200 text-sm py-2.5 px-3">
                    <option value="" {{ request('status') === '' ? 'selected' : '' }}>Tất cả</option>
                    <option value="cho_xu_ly" {{ request('status') === 'cho_xu_ly' || !request()->has('status') ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="da_xu_ly" {{ request('status') === 'da_xu_ly' ? 'selected' : '' }}>Đã xử lý (Xóa nội dung)</option>
                    <option value="bo_qua" {{ request('status') === 'bo_qua' ? 'selected' : '' }}>Đã bỏ qua / Giữ lại</option>
                </select>
            </div>

            <div class="shrink-0 flex gap-2">
                <a href="{{ route('admin.reports.index', ['status' => 'cho_xu_ly']) }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-sm rounded-xl transition-all">
                    Reset
                </a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-extrabold text-sm rounded-xl shadow-lg shadow-amber-500/10 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span> Lọc kết quả
                </button>
            </div>
        </form>
    </div>

    <!-- Reports List -->
    @if($reports->isEmpty())
        <div class="glass-panel p-12 text-center rounded-2xl border border-white/5 bg-slate-900/20">
            <span class="material-symbols-outlined text-slate-600 text-6xl mb-3">check_circle</span>
            <p class="text-slate-400 text-lg font-semibold">Tuyệt vời! Không có báo cáo nào khớp với bộ lọc.</p>
            <p class="text-slate-500 text-sm mt-1">Hệ thống mạng xã hội của bạn đang rất sạch sẽ.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($reports as $report)
                @php
                    $violatingUser = null;
                    if ($report->bai_viet_id && $report->baiViet) {
                        $violatingUser = $report->baiViet->user;
                    } elseif ($report->binh_luan_id && $report->binhLuan) {
                        $violatingUser = $report->binhLuan->user;
                    } elseif ($report->nguoi_dung_bi_bao_cao_id && $report->nguoiBiBaoCao) {
                        $violatingUser = $report->nguoiBiBaoCao;
                    }
                @endphp
                <div class="glass-panel p-6 rounded-2xl border border-white/5 hover:border-white/10 transition-all bg-slate-950/40 relative flex flex-col gap-4" id="report-row-{{ $report->id }}">
                    <!-- Top row: Header & badges -->
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/5 pb-3">
                        <div class="flex items-center gap-3">
                            <!-- Type Icon badge -->
                            @if($report->bai_viet_id)
                                <span class="flex items-center gap-1 text-xs font-semibold px-2.5 py-1 bg-sky-500/10 text-sky-400 rounded-full border border-sky-500/20">
                                    <span class="material-symbols-outlined text-[14px]">article</span> Bài viết #{{ $report->bai_viet_id }}
                                </span>
                            @elseif($report->binh_luan_id)
                                <span class="flex items-center gap-1 text-xs font-semibold px-2.5 py-1 bg-purple-500/10 text-purple-400 rounded-full border border-purple-500/20">
                                    <span class="material-symbols-outlined text-[14px]">chat_bubble</span> Bình luận #{{ $report->binh_luan_id }}
                                </span>
                            @elseif($report->nguoi_dung_bi_bao_cao_id)
                                <span class="flex items-center gap-1 text-xs font-semibold px-2.5 py-1 bg-pink-500/10 text-pink-400 rounded-full border border-pink-500/20">
                                    <span class="material-symbols-outlined text-[14px]">person</span> Người dùng
                                </span>
                            @endif

                            <!-- Status Badge -->
                            @if($report->trang_thai === 'cho_xu_ly')
                                <span class="text-xs font-semibold px-2.5 py-1 bg-amber-500/10 text-amber-400 rounded-full border border-amber-500/20 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Chờ xử lý
                                </span>
                            @elseif($report->trang_thai === 'da_xu_ly')
                                <span class="text-xs font-semibold px-2.5 py-1 bg-emerald-500/10 text-emerald-400 rounded-full border border-emerald-500/20 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">check</span> Đã xử lý (Xóa)
                                </span>
                            @else
                                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-500/10 text-slate-400 rounded-full border border-slate-500/20 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">visibility_off</span> Đã bỏ qua
                                </span>
                            @endif
                        </div>

                        <span class="text-xs text-slate-500">
                            Báo cáo lúc: <strong class="text-slate-400">{{ $report->ngay_tao ? $report->ngay_tao->format('H:i d/m/Y') : '' }}</strong>
                        </span>
                    </div>

                    <!-- Middle Section: Reporter & Reported content details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Left column: People metadata -->
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs text-slate-500 uppercase font-bold tracking-wider">Người báo cáo</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <img src="{{ $report->nguoiBaoCao ? $report->nguoiBaoCao->avatar_url : 'https://ui-avatars.com/api/?name=User&background=random' }}" class="w-6 h-6 rounded-full object-cover">
                                    <span class="text-sm font-semibold text-slate-200">
                                        {{ $report->nguoiBaoCao ? $report->nguoiBaoCao->name : 'Ẩn danh' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <span class="text-xs text-slate-500 uppercase font-bold tracking-wider">Lý do cáo buộc</span>
                                <p class="text-sm text-red-400 font-medium mt-1 leading-relaxed">
                                    "{{ $report->ly_do }}"
                                </p>
                            </div>
                        </div>

                        <!-- Right/Center column: Target content preview (Post or Comment) -->
                        <div class="md:col-span-2 bg-slate-900/40 p-4 rounded-xl border border-white/5 space-y-3">
                            <span class="text-xs text-slate-500 uppercase font-bold tracking-wider block">Nội dung bị báo cáo</span>
                            
                            @if($report->bai_viet_id)
                                @if($report->baiViet)
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $report->baiViet->user ? $report->baiViet->user->avatar_url : 'https://ui-avatars.com/api/?name=User&background=random' }}" class="w-5 h-5 rounded-full object-cover">
                                            <span class="text-xs font-semibold text-slate-400">Tác giả: {{ $report->baiViet->user?->name }}</span>
                                        </div>
                                        @if($report->baiViet->user)
                                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full border {{ $report->baiViet->user->con_hoat_dong ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20' }}">
                                                {{ $report->baiViet->user->con_hoat_dong ? 'Tài khoản hoạt động' : 'Tài khoản đã khóa' }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-slate-300 line-clamp-3 whitespace-pre-line leading-relaxed">
                                        {{ $report->baiViet->noi_dung }}
                                    </p>
                                    @if($report->baiViet->media->isNotEmpty())
                                        <div class="flex gap-2 overflow-x-auto mt-2">
                                            @foreach($report->baiViet->media as $med)
                                                <img src="{{ asset('storage/' . $med->duong_dan) }}" class="h-12 w-12 rounded object-cover border border-white/10">
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <span class="text-xs text-rose-400 italic flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">delete</span> Bài viết gốc đã bị xóa
                                    </span>
                                @endif

                            @elseif($report->binh_luan_id)
                                @if($report->binhLuan)
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $report->binhLuan->user ? $report->binhLuan->user->avatar_url : 'https://ui-avatars.com/api/?name=User&background=random' }}" class="w-5 h-5 rounded-full object-cover">
                                            <span class="text-xs font-semibold text-slate-400">Người viết: {{ $report->binhLuan->user?->name }}</span>
                                        </div>
                                        @if($report->binhLuan->user)
                                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full border {{ $report->binhLuan->user->con_hoat_dong ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20' }}">
                                                {{ $report->binhLuan->user->con_hoat_dong ? 'Tài khoản hoạt động' : 'Tài khoản đã khóa' }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-slate-300 leading-relaxed italic">
                                        "{!! $report->binhLuan->noi_dung !!}"
                                    </p>
                                @else
                                    <span class="text-xs text-rose-400 italic flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">delete</span> Bình luận đã bị xóa trước đó
                                    </span>
                                @endif

                            @elseif($report->nguoi_dung_bi_bao_cao_id)
                                @if($report->nguoiBiBaoCao)
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $report->nguoiBiBaoCao->avatar_url }}" class="w-10 h-10 rounded-full object-cover border border-white/10">
                                            <div>
                                                <p class="text-sm font-bold text-white">{{ $report->nguoiBiBaoCao->name }}</p>
                                                <p class="text-xs text-slate-500">{{ '@' . $report->nguoiBiBaoCao->ten_dang_nhap }}</p>
                                            </div>
                                        </div>
                                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full border {{ $report->nguoiBiBaoCao->con_hoat_dong ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20' }}">
                                            {{ $report->nguoiBiBaoCao->con_hoat_dong ? 'Tài khoản hoạt động' : 'Tài khoản đã khóa' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1">
                                        Tiểu sử: {{ $report->nguoiBiBaoCao->tieu_su ?? '(Chưa có)' }}
                                    </p>
                                @else
                                    <span class="text-xs text-rose-400 italic">Người dùng không tồn tại</span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Bottom row: User controls & Process Buttons -->
                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-white/5 pt-4 mt-1">
                        <div>
                            @if($violatingUser)
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-500 font-semibold">Tác vụ tài khoản:</span>
                                    <button type="button" 
                                            id="user-status-btn-{{ $violatingUser->id }}"
                                            class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all cursor-pointer flex items-center gap-1.5 {{ $violatingUser->con_hoat_dong ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' }}" 
                                            onclick="toggleUserStatus({{ $violatingUser->id }}, '{{ $violatingUser->name }}')">
                                        <span class="material-symbols-outlined text-[16px]">{{ $violatingUser->con_hoat_dong ? 'lock' : 'lock_open' }}</span>
                                        <span class="btn-text">{{ $violatingUser->con_hoat_dong ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if($report->trang_thai === 'cho_xu_ly')
                            <div class="flex gap-3">
                                <button type="button" class="px-4 py-2 border border-white/10 hover:bg-white/5 text-slate-300 text-sm font-semibold rounded-xl transition-all cursor-pointer" onclick="processReport({{ $report->id }}, 'bo_qua')">
                                    Bỏ qua & Giữ lại
                                </button>
                                
                                @if(($report->bai_viet_id && $report->baiViet) || ($report->binh_luan_id && $report->binhLuan) || ($report->nguoi_dung_bi_bao_cao_id && $report->nguoiBiBaoCao && $report->nguoiBiBaoCao->con_hoat_dong))
                                    <button type="button" class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-rose-500/10 transition-all flex items-center gap-2 cursor-pointer" onclick="deleteViolatingContent({{ $report->id }})">
                                        <span class="material-symbols-outlined text-[18px]">delete_forever</span> Xóa nội dung vi phạm
                                    </button>
                                @else
                                    <button type="button" class="px-5 py-2 bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold rounded-xl transition-all cursor-pointer" onclick="processReport({{ $report->id }}, 'da_xu_ly')">
                                        Đánh dấu Đã xử lý
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            {{ $reports->links() }}
        </div>
    @endif
</div>

<!-- Handle Actions script -->
<script>
function toggleUserStatus(userId, userName) {
    const btn = document.getElementById('user-status-btn-' + userId);
    const isLocking = btn.classList.contains('bg-amber-500/10');
    const title = isLocking ? 'Khóa tài khoản này?' : 'Mở khóa tài khoản này?';
    const message = isLocking 
        ? `Tài khoản của "${userName}" sẽ bị vô hiệu hóa hoạt động trên hệ thống.` 
        : `Tài khoản của "${userName}" sẽ được mở khóa hoạt động trở lại.`;
    const actionText = isLocking ? 'Khóa tài khoản' : 'Mở khóa';

    window.openConfirmModal(
        title, 
        message, 
        function() {
            fetch('/admin/users/' + userId + '/toggle-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    if(typeof window.showToast === 'function') {
                        window.showToast(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    if(typeof window.showToast === 'function') {
                        window.showToast(data.message || 'Có lỗi xảy ra.', 'error');
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(err => {
                console.error(err);
                if(typeof window.showToast === 'function') window.showToast('Lỗi kết nối.', 'error');
            });
        },
        actionText
    );
}

function processReport(reportId, action) {
    const row = document.getElementById('report-row-' + reportId);
    
    fetch('/admin/reports/' + reportId + '/action/' + action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            if(typeof window.showToast === 'function') {
                window.showToast(data.message, 'success');
            } else {
                alert(data.message);
            }
            
            // Reload page to update counters and views after 1s
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            if(typeof window.showToast === 'function') {
                window.showToast(data.message || 'Có lỗi xảy ra.', 'error');
            } else {
                alert(data.message);
            }
        }
    })
    .catch(err => {
        console.error(err);
        if(typeof window.showToast === 'function') window.showToast('Lỗi kết nối.', 'error');
    });
}

function deleteViolatingContent(reportId) {
    window.openConfirmModal(
        'Xóa nội dung vi phạm?', 
        'Hành động này sẽ xóa vĩnh viễn bài viết/bình luận này hoặc vô hiệu hóa người dùng bị báo cáo.', 
        function() {
            fetch('/admin/reports/' + reportId + '/delete-content', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    if(typeof window.showToast === 'function') {
                        window.showToast(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    if(typeof window.showToast === 'function') {
                        window.showToast(data.message || 'Có lỗi xảy ra.', 'error');
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(err => {
                console.error(err);
                if(typeof window.showToast === 'function') window.showToast('Lỗi kết nối.', 'error');
            });
        },
        'Xóa vi phạm'
    );
}
</script>
@endsection
