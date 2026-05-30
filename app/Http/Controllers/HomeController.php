<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\Tin24h;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $feedType = $request->input('feed', 'recommend');
        if (!in_array($feedType, ['recommend', 'following'])) {
            $feedType = 'recommend';
        }

        $blockedUserIds = [];
        $followingIds = [];
        $user = auth()->user();

        if ($user) {
            $blockedByMe = $user->blockedUsers()->pluck('nguoi_bi_chan_id')->toArray();
            $blockedMe = $user->blockedByUsers()->pluck('nguoi_chan_id')->toArray();
            $blockedUserIds = array_unique(array_merge($blockedByMe, $blockedMe));

            $followingIds = \DB::table('theo_doi')
                ->where('nguoi_theo_doi_id', $user->id)
                ->where('trang_thai', 'da_chap_nhan')
                ->pluck('nguoi_duoc_theo_doi_id')
                ->toArray();
        }

        $query = BaiViet::with(['user', 'media', 'originalPost.user', 'originalPost.media', 'poll.options.votes', 'poll.votes'])
            ->withCount(['reactions', 'comments', 'shares'])
            ->with(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
            }, 'bookmarks' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }])
            ->where('da_xoa', false)
            ->whereIn('loai', ['van_ban', 'hinh_anh', 'chia_se', 'binh_chon']);

        if (!empty($blockedUserIds)) {
            $query->whereNotIn('nguoi_dung_id', $blockedUserIds);
        }

        if ($feedType === 'following' && $user) {
            // Chronological feed: CHỈ lấy bài viết của những người mà User đang đăng nhập theo dõi
            if (!empty($followingIds)) {
                $query->whereIn('nguoi_dung_id', $followingIds);
            } else {
                // Nếu chưa theo dõi ai, trả về danh sách rỗng
                $query->whereRaw('1 = 0');
            }
            $query->latest();
        } else {
            // Tab Dành cho bạn / Mặc định: Hiển thị tất cả bài viết trên toàn hệ thống, mới nhất lên đầu
            $query->latest();
        }

        $posts = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('components.posts-feed', [
                'posts'    => $posts,
                'feedType' => $feedType,
            ])->render();
        }

        // Lấy stories chưa hết hạn, eager-load user
        $stories = Tin24h::with('user')
            ->conHan()  // scope trong model: het_han > now()
            ->when(!empty($blockedUserIds), function ($query) use ($blockedUserIds) {
                $query->whereNotIn('nguoi_dung_id', $blockedUserIds);
            })
            ->latest('ngay_tao')
            ->take(30)
            ->get();

        // Lấy 9 ảnh/video mới nhất từ bảng tin
        $recentMedia = \App\Models\MediaBaiViet::whereIn('bai_viet_id', BaiViet::where('da_xoa', false)
            ->when(!empty($blockedUserIds), function ($query) use ($blockedUserIds) {
                $query->whereNotIn('nguoi_dung_id', $blockedUserIds);
            })->pluck('id'))
            ->latest('ngay_tao')
            ->take(9)
            ->get();

        // --- LOGIC GỢI Ý KẾT BẠN (NHỮNG NGƯỜI BẠN CÓ THỂ BIẾT) ---
        $suggestedUsers = collect();
        if ($user) {
            // Lấy danh sách ID đã gửi yêu cầu theo dõi hoặc đang theo dõi (tránh gợi ý lại)
            $alreadyFollowingIds = \DB::table('theo_doi')
                ->where('nguoi_theo_doi_id', $user->id)
                ->pluck('nguoi_duoc_theo_doi_id')
                ->toArray();

            // Danh sách ID loại trừ: Bản thân, người đã theo dõi, người đã chặn
            $excludeIds = array_unique(array_merge([$user->id], $alreadyFollowingIds, $blockedUserIds));

            // Ưu tiên 1: Gợi ý dựa trên bạn chung
            if (!empty($followingIds)) {
                $mutualFriendsQuery = \DB::table('theo_doi as td1')
                    ->join('theo_doi as td2', 'td1.nguoi_duoc_theo_doi_id', '=', 'td2.nguoi_theo_doi_id')
                    ->where('td1.nguoi_theo_doi_id', $user->id)
                    ->where('td1.trang_thai', 'da_chap_nhan')
                    ->where('td2.trang_thai', 'da_chap_nhan')
                    ->whereNotIn('td2.nguoi_duoc_theo_doi_id', $excludeIds)
                    ->select('td2.nguoi_duoc_theo_doi_id as id', \DB::raw('count(td1.nguoi_duoc_theo_doi_id) as mutual_count'))
                    ->groupBy('td2.nguoi_duoc_theo_doi_id')
                    ->orderBy('mutual_count', 'desc')
                    ->take(5)
                    ->get();

                if ($mutualFriendsQuery->isNotEmpty()) {
                    $mutualUserIds = $mutualFriendsQuery->pluck('id')->toArray();
                    $mutualUsers = User::whereIn('id', $mutualUserIds)->get()->keyBy('id');
                    
                    foreach ($mutualFriendsQuery as $item) {
                        if (isset($mutualUsers[$item->id])) {
                            $suUser = $mutualUsers[$item->id];
                            $suUser->suggestion_reason = "Có {$item->mutual_count} bạn chung";
                            $suggestedUsers->put($suUser->id, $suUser);
                        }
                    }
                }
            }

            // Ưu tiên 2: Cùng nơi ở (noi_o)
            if ($suggestedUsers->count() < 5 && !empty($user->noi_o)) {
                $locationExcludeIds = array_unique(array_merge($excludeIds, $suggestedUsers->keys()->toArray()));
                $locationUsers = User::whereNotIn('id', $locationExcludeIds)
                    ->where('noi_o', 'LIKE', '%' . $user->noi_o . '%')
                    ->where('con_hoat_dong', true)
                    ->take(5 - $suggestedUsers->count())
                    ->get();

                foreach ($locationUsers as $lUser) {
                    $lUser->suggestion_reason = "Sống tại " . $user->noi_o;
                    $suggestedUsers->put($lUser->id, $lUser);
                }
            }

            // Lấp đầy ngẫu nhiên
            if ($suggestedUsers->count() < 5) {
                $randomExcludeIds = array_unique(array_merge($excludeIds, $suggestedUsers->keys()->toArray()));
                $randomUsers = User::whereNotIn('id', $randomExcludeIds)
                    ->where('con_hoat_dong', true)
                    ->inRandomOrder()
                    ->take(5 - $suggestedUsers->count())
                    ->get();

                foreach ($randomUsers as $rUser) {
                    $rUser->suggestion_reason = "Gợi ý cho bạn";
                    $suggestedUsers->put($rUser->id, $rUser);
                }
            }
        }

        return view('components.home', [
            'posts'          => $posts,
            'stories'        => $stories,
            'recentMedia'    => $recentMedia,
            'feedType'       => $feedType,
            'suggestedUsers' => $suggestedUsers,
        ]);
    }
}
