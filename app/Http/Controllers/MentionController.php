<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MentionController extends Controller
{
    /**
     * Get mention suggestions for the current user.
     * Prioritizes followed users and followers (connected users).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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

            // 1. Fetch following and follower IDs (active & accepted)
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

            $connectedUserIds = array_unique(array_merge($followingIds, $followerIds));

            // 2. Fetch interacted user IDs (chat, reactions, comments)
            // Members of same chat groups
            $chatUserIds = DB::table('thanh_vien_nhom')
                ->whereIn('cuoc_tro_chuyen_id', function ($query) use ($currentUser) {
                    $query->select('cuoc_tro_chuyen_id')
                        ->from('thanh_vien_nhom')
                        ->where('nguoi_dung_id', $currentUser->id);
                })
                ->where('nguoi_dung_id', '!=', $currentUser->id)
                ->pluck('nguoi_dung_id')
                ->toArray();

            // People who reacted to current user's posts
            $reactedToMyPostsUserIds = DB::table('cam_xuc')
                ->join('bai_viet', 'cam_xuc.bai_viet_id', '=', 'bai_viet.id')
                ->where('bai_viet.nguoi_dung_id', $currentUser->id)
                ->where('cam_xuc.nguoi_dung_id', '!=', $currentUser->id)
                ->pluck('cam_xuc.nguoi_dung_id')
                ->toArray();

            // People who commented on current user's posts
            $commentedOnMyPostsUserIds = DB::table('binh_luan')
                ->whereIn('bai_viet_id', function ($query) use ($currentUser) {
                    $query->select('id')
                        ->from('bai_viet')
                        ->where('nguoi_dung_id', $currentUser->id);
                })
                ->where('nguoi_dung_id', '!=', $currentUser->id)
                ->pluck('nguoi_dung_id')
                ->toArray();

            // People whose posts current user commented on
            $postsICommentedOnOwnerIds = DB::table('bai_viet')
                ->whereIn('id', function ($query) use ($currentUser) {
                    $query->select('bai_viet_id')
                        ->from('binh_luan')
                        ->where('nguoi_dung_id', $currentUser->id);
                })
                ->where('nguoi_dung_id', '!=', $currentUser->id)
                ->pluck('nguoi_dung_id')
                ->toArray();

            $interactedUserIds = array_unique(array_merge(
                $chatUserIds,
                $reactedToMyPostsUserIds,
                $commentedOnMyPostsUserIds,
                $postsICommentedOnOwnerIds
            ));

            // Query active users prioritizing connections, then interacted users
            $query = User::query()
                ->where('con_hoat_dong', true)
                ->where('id', '!=', $currentUser->id);

            if (!empty($search)) {
                $query->where('ten_dang_nhap', 'like', '%' . $search . '%');
            }

            // Prioritize: 
            // 1. Connected users (weight 2)
            // 2. Interacted users (weight 1)
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
                    'avatar' => $user->anh_dai_dien ? asset('storage/' . $user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png'),
                    'relation' => $relation,
                ];
            })->toArray();

            // Prepend @all option if query is empty or matches "all" and user has connections
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
