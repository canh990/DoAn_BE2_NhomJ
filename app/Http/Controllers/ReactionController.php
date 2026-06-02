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
        'thuong_thuong',
        'wow',
    ];

    // THẢ/GỠ CẢM XÚC: Xử lý tương tác của người dùng trên bài viết
    public function store(Request $request, BaiViet $post)
    {
        // XÁC THỰC DỮ LIỆU: Chỉ chấp nhận các loại cảm xúc được định nghĩa sẵn
        $validated = $request->validate([
            'loai_cam_xuc' => ['required', 'string', 'in:' . implode(',', $this->allowedTypes)],
        ]);

        $user = $request->user();
        // KIỂM TRA CHẶN: Không cho phép tương tác nếu có mối quan hệ chặn giữa 2 người dùng để tránh xung đột
        if ($user->hasAnyBlockRelationship($post->nguoi_dung_id)) {
            abort(403, 'Bạn không thể tương tác với bài viết này.');
        }

        // Lấy cảm xúc hiện tại của người dùng đối với bài viết này
        $currentReaction = CamXuc::where('nguoi_dung_id', $user->id)
            ->where('bai_viet_id', $post->id)
            ->first();

        $removed = false;
        $message = 'Bạn đã thả cảm xúc.';

        // XỬ LÝ TOGGLE CẢM XÚC: Nếu cảm xúc mới trùng cảm xúc cũ thì tiến hành gỡ bỏ
        if ($currentReaction && $currentReaction->loai_cam_xuc === $validated['loai_cam_xuc']) {
            $currentReaction->delete();
            
            // XÓA THÔNG BÁO TƯƠNG ỨNG: Khi gỡ cảm xúc, phải xóa thông báo đã tạo trước đó để giữ dữ liệu sạch
            \App\Models\ThongBao::where([
                'nguoi_dung_id' => $post->nguoi_dung_id,
                'nguoi_thuc_hien_id' => $user->id,
                'loai' => 'thich',
                'bai_viet_id' => $post->id,
            ])->delete();

            $removed = true;
            $message = 'Bạn đã gỡ cảm xúc.';
        } else {
            // CẬP NHẬT HOẶC TẠO MỚI: Nếu chưa có cảm xúc hoặc thay đổi loại cảm xúc khác
            CamXuc::updateOrCreate(
                [
                    'nguoi_dung_id' => $user->id,
                    'bai_viet_id' => $post->id,
                ],
                [
                    'loai_cam_xuc' => $validated['loai_cam_xuc'],
                ]
            );

            // TẠO THÔNG BÁO: Chỉ gửi thông báo cho chủ bài viết khi người thực hiện là người khác
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

        // Trả về JSON để phục vụ luồng cập nhật AJAX realtime ở phía giao diện
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

    // LẤY DANH SÁCH USER BÀY TỎ CẢM XÚC: Phục vụ cho Modal hiển thị chi tiết ngoài giao diện
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

                // Tạo avatar mặc định nếu người dùng chưa thiết lập ảnh đại diện
                $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random';
                if ($user->anh_dai_dien) {
                    if (\Illuminate\Support\Str::startsWith($user->anh_dai_dien, ['http://', 'https://'])) {
                        $avatar = $user->anh_dai_dien;
                    } else {
                        $avatar = asset('storage/' . ltrim($user->anh_dai_dien, '/'));
                    }
                }

                // Cấu hình các metadata hiển thị tương ứng với từng loại cảm xúc (Icon, Label, CSS Class)
                $reactionLabels = [
                    'thich' => ['icon' => 'sentiment_satisfied', 'label' => 'Mỉm cười', 'color' => 'text-yellow-300', 'bg' => 'bg-yellow-500/20'],
                    'tim' => ['icon' => 'favorite', 'label' => 'Yêu thích', 'color' => 'text-rose-400', 'bg' => 'bg-rose-500/20'],
                    'haha' => ['icon' => 'mood', 'label' => 'Haha', 'color' => 'text-yellow-300', 'bg' => 'bg-yellow-500/20'],
                    'buon' => ['icon' => 'sentiment_dissatisfied', 'label' => 'Buồn', 'color' => 'text-slate-400', 'bg' => 'bg-slate-500/20'],
                    'phan_no' => ['icon' => 'mood_bad', 'label' => 'Phẫn nộ', 'color' => 'text-orange-400', 'bg' => 'bg-orange-500/20'],
                    'thuong_thuong' => ['icon' => 'favorite', 'label' => 'Thương thương', 'color' => 'text-pink-400', 'bg' => 'bg-pink-500/20'],
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
