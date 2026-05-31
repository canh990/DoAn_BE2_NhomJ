<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MentionController extends Controller
{
    /**
     * LẤY GỢI Ý NHẮC TÊN (MENTION SUGGESTIONS):
     * Thuật toán sắp xếp ưu tiên những người dùng có mối quan hệ gần gũi (Bạn bè, Đang theo dõi, Người theo dõi)
     * và những người từng có tương tác gần đây (nhắn tin, thả cảm xúc, bình luận qua lại).
     */
    public function suggestions(Request $request)
    {
        try {
            $currentUser = auth()->user();
            if (!$currentUser) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $search = trim($request->input('q', ''));
            $postId = $request->input('post_id');

            // 1. LẤY MỐI QUAN HỆ THEO DÕI: Bạn bè (theo dõi chéo), Đang theo dõi, và Người theo dõi
            $followingIds = DB::table('theo_doi')
                ->where('nguoi_theo_doi_id', $currentUser->id)
                ->where('trang_thai', 'da_chap_nhan')
                ->pluck('nguoi_duoc_theo_doi_id')
                ->toArray();

            $followerIds = DB::table('theo_doi')
                ->where('nguoi_duoc_theo_doi_id', $currentUser->id)
                ->where('trang_thai', 'da_chap_nhan')
                ->pluck('nguoi_theo_doi_id')
                ->toArray();

            // Nhóm người dùng có liên kết trực tiếp (theo dõi/bị theo dõi)
            $connectedUserIds = array_unique(array_merge($followingIds, $followerIds));

            // 2. LẤY LỊCH SỬ TƯƠNG TÁC: Xác định các tài khoản có hoạt động giao tiếp gần đây để tăng độ ưu tiên đề xuất
            // A. Thành viên trong cùng các nhóm chat
            $chatUserIds = DB::table('thanh_vien_nhom')
                ->whereIn('cuoc_tro_chuyen_id', function ($query) use ($currentUser) {
                    $query->select('cuoc_tro_chuyen_id')
                        ->from('thanh_vien_nhom')
                        ->where('nguoi_dung_id', $currentUser->id);
                })
                ->where('nguoi_dung_id', '!=', $currentUser->id)
                ->pluck('nguoi_dung_id')
                ->toArray();

            // B. Những người từng thả cảm xúc trên bài viết của user hiện tại
            $reactedToMyPostsUserIds = DB::table('cam_xuc')
                ->join('bai_viet', 'cam_xuc.bai_viet_id', '=', 'bai_viet.id')
                ->where('bai_viet.nguoi_dung_id', $currentUser->id)
                ->where('cam_xuc.nguoi_dung_id', '!=', $currentUser->id)
                ->pluck('cam_xuc.nguoi_dung_id')
                ->toArray();

            // C. Những người từng bình luận trên bài viết của user hiện tại
            $commentedOnMyPostsUserIds = DB::table('binh_luan')
                ->whereIn('bai_viet_id', function ($query) use ($currentUser) {
                    $query->select('id')
                        ->from('bai_viet')
                        ->where('nguoi_dung_id', $currentUser->id);
                })
                ->where('nguoi_dung_id', '!=', $currentUser->id)
                ->pluck('nguoi_dung_id')
                ->toArray();

            // D. Chủ các bài viết mà user hiện tại từng vào bình luận
            $postsICommentedOnOwnerIds = DB::table('bai_viet')
                ->whereIn('id', function ($query) use ($currentUser) {
                    $query->select('bai_viet_id')
                        ->from('binh_luan')
                        ->where('nguoi_dung_id', $currentUser->id);
                })
                ->where('nguoi_dung_id', '!=', $currentUser->id)
                ->pluck('nguoi_dung_id')
                ->toArray();

            // Tổng hợp ID của tất cả người dùng từng tương tác
            $interactedUserIds = array_unique(array_merge(
                $chatUserIds,
                $reactedToMyPostsUserIds,
                $commentedOnMyPostsUserIds,
                $postsICommentedOnOwnerIds
            ));

            // 3. TRUY VẤN DANH SÁCH USER: Chỉ tìm kiếm các tài khoản còn hoạt động và không phải chính mình
            $query = User::query()
                ->where('con_hoat_dong', true)
                ->where('id', '!=', $currentUser->id);

            // Tìm kiếm theo tên đăng nhập (nếu có từ khóa truy vấn)
            if (!empty($search)) {
                $query->where('ten_dang_nhap', 'like', '%' . $search . '%');
            }

            // 4. THUẬT TOÁN XẾP HẠNG TRỌNG SỐ (WEIGHTED RANKING):
            // - Nhóm có liên kết (Friends, Followers, Following) nhận trọng số là 2.
            // - Nhóm chỉ có tương tác thông thường nhận trọng số là 1.
            // - Sắp xếp điểm số giảm dần để đẩy các kết quả phù hợp nhất lên đầu danh sách gợi ý.
            $caseParts = [];
            if (!empty($connectedUserIds)) {
                $idsString = implode(',', array_map('intval', $connectedUserIds));
                $caseParts[] = "CASE WHEN id IN ({$idsString}) THEN 2 ELSE 0 END";
            }
            if (!empty($interactedUserIds)) {
                $idsString = implode(',', array_map('intval', $interactedUserIds));
                $caseParts[] = "CASE WHEN id IN ({$idsString}) THEN 1 ELSE 0 END";
            }

            if (!empty($caseParts)) {
                $orderByExpression = '(' . implode(' + ', $caseParts) . ') DESC';
                $query->orderByRaw($orderByExpression);
            }

            $users = $query->take(10)->get();

            // Định dạng dữ liệu trả về và xác định nhãn mối quan hệ tương ứng ngoài frontend
            $results = $users->map(function ($user) use ($followingIds, $followerIds, $interactedUserIds) {
                $isFollowing = in_array($user->id, $followingIds);
                $isFollower = in_array($user->id, $followerIds);

                $relation = '';
                if ($isFollowing && $isFollower) {
                    $relation = 'Bạn bè';
                } elseif ($isFollowing) {
                    $relation = 'Đang theo dõi';
                } elseif ($isFollower) {
                    $relation = 'Người theo dõi';
                } elseif (in_array($user->id, $interactedUserIds)) {
                    $relation = 'Từng tương tác';
                }

                return [
                    'id' => $user->id,
                    'username' => $user->ten_dang_nhap,
                    'name' => $user->name ?? $user->ten_dang_nhap,
                    'avatar' => $user->anh_dai_dien ? (filter_var($user->anh_dai_dien, FILTER_VALIDATE_URL) ? $user->anh_dai_dien : asset('storage/' . $user->anh_dai_dien)) : 'https://ui-avatars.com/api/?name=' . urlencode($user->ten_dang_nhap) . '&background=random',
                    'relation' => $relation,
                ];
            })->toArray();

            // 5. GỢI Ý MENTION ALL (@all):
            // Cho phép người dùng tag toàn bộ danh sách liên kết khi gõ @all (chỉ khả dụng nếu người dùng có bạn bè/theo dõi)
            $hasAllPermission = !empty($connectedUserIds);

            if ($hasAllPermission && (empty($search) || stripos('all', $search) !== false)) {
                array_unshift($results, [
                    'id' => 0,
                    'username' => 'all',
                    'name' => 'Nhắc tất cả bạn bè & người theo dõi',
                    'avatar' => null,
                    'is_all' => true,
                    'relation' => 'Mọi người'
                ]);
            }

            return response()->json($results);

        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
