<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\CamXuc;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    protected array $allowedTypes = [
        'thich',
        'tim',
        'haha',
        'buon',
        'phan_no',
        'wow',
    ];

    public function store(Request $request, BaiViet $post)
    {
        $validated = $request->validate([
            'loai_cam_xuc' => ['required', 'string', 'in:' . implode(',', $this->allowedTypes)],
        ]);

        $user = $request->user();
        if ($user->hasAnyBlockRelationship($post->nguoi_dung_id)) {
            abort(403, 'Bạn không thể tương tác với bài viết này.');
        }

        $currentReaction = CamXuc::where('nguoi_dung_id', $user->id)
            ->where('bai_viet_id', $post->id)
            ->first();

        $removed = false;
        $message = 'Bạn đã thả cảm xúc.';

        if ($currentReaction && $currentReaction->loai_cam_xuc === $validated['loai_cam_xuc']) {
            $currentReaction->delete();
            
            // Xóa thông báo khi gỡ cảm xúc
            \App\Models\ThongBao::where([
                'nguoi_dung_id' => $post->nguoi_dung_id,
                'nguoi_thuc_hien_id' => $user->id,
                'loai' => 'thich',
                'bai_viet_id' => $post->id,
            ])->delete();

            $removed = true;
            $message = 'Bạn đã gỡ cảm xúc.';
        } else {
            CamXuc::updateOrCreate(
                [
                    'nguoi_dung_id' => $user->id,
                    'bai_viet_id' => $post->id,
                ],
                [
                    'loai_cam_xuc' => $validated['loai_cam_xuc'],
                ]
            );

            // Tạo thông báo nếu không phải bài viết của chính mình
            if ($post->nguoi_dung_id !== $user->id) {
                \App\Models\ThongBao::updateOrCreate(
                    [
                        'nguoi_dung_id' => $post->nguoi_dung_id,
                        'nguoi_thuc_hien_id' => $user->id,
                        'loai' => 'thich',
                        'bai_viet_id' => $post->id,
                    ],
                    [
                        'da_doc' => false,
                        'ngay_tao' => now(),
                    ]
                );
            }
        }

        $reactionsCount = $post->reactions()->count();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'removed' => $removed,
                'reaction' => $validated['loai_cam_xuc'],
                'reactions_count' => $reactionsCount,
            ]);
        }

        return back()->with('success', $message);
    }

    public function reactors(BaiViet $post)
    {
        $reactions = $post->reactions()
            ->with(['user'])
            ->get();

        return response()->json([
            'success' => true,
            'reactors' => $reactions->map(function ($reaction) {
                $user = $reaction->user;
                if (!$user) return null;

                $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random';
                if ($user->anh_dai_dien) {
                    if (\Illuminate\Support\Str::startsWith($user->anh_dai_dien, ['http://', 'https://'])) {
                        $avatar = $user->anh_dai_dien;
                    } else {
                        $avatar = asset('storage/' . ltrim($user->anh_dai_dien, '/'));
                    }
                }

                $reactionLabels = [
                    'thich' => ['icon' => 'thumb_up', 'label' => 'Thích', 'color' => 'text-sky-400', 'bg' => 'bg-sky-500/20'],
                    'tim' => ['icon' => 'favorite', 'label' => 'Yêu thích', 'color' => 'text-rose-400', 'bg' => 'bg-rose-500/20'],
                    'haha' => ['icon' => 'mood', 'label' => 'Haha', 'color' => 'text-yellow-300', 'bg' => 'bg-yellow-500/20'],
                    'buon' => ['icon' => 'sentiment_dissatisfied', 'label' => 'Buồn', 'color' => 'text-slate-400', 'bg' => 'bg-slate-500/20'],
                    'phan_no' => ['icon' => 'mood_bad', 'label' => 'Phẫn nộ', 'color' => 'text-orange-400', 'bg' => 'bg-orange-500/20'],
                    'wow' => ['icon' => 'emoji_objects', 'label' => 'Wow', 'color' => 'text-emerald-400', 'bg' => 'bg-emerald-500/20'],
                ];

                $rMeta = $reactionLabels[$reaction->loai_cam_xuc] ?? ['icon' => 'thumb_up', 'label' => 'Thích', 'color' => 'text-sky-400', 'bg' => 'bg-sky-500/20'];

                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->ten_dang_nhap,
                    'avatar' => $avatar,
                    'is_verified' => (bool)$user->da_xac_thuc,
                    'reaction_type' => $reaction->loai_cam_xuc,
                    'reaction_icon' => $rMeta['icon'],
                    'reaction_color' => $rMeta['color'],
                    'reaction_bg' => $rMeta['bg'],
                ];
            })->filter()->values()
        ]);
    }
}
