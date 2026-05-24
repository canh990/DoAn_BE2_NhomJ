<?php

namespace App\Http\Controllers;

use App\Models\BaoCao;
use App\Models\BaiViet;
use App\Models\BinhLuan;
use App\Models\User;
use App\Models\ThongBao;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Store a new report and notify the reported user of the system warning.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ly_do' => ['required', 'string', 'max:500'],
            'bai_viet_id' => ['nullable', 'integer', 'exists:bai_viet,id'],
            'binh_luan_id' => ['nullable', 'integer', 'exists:binh_luan,id'],
            'nguoi_dung_bi_bao_cao_id' => ['nullable', 'integer', 'exists:nguoi_dung,id'],
        ]);

        try {
            $currentUser = auth()->user();
            if (!$currentUser) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // Ensure exactly one entity is being reported
            $targetCount = 0;
            if (!empty($validated['bai_viet_id'])) $targetCount++;
            if (!empty($validated['binh_luan_id'])) $targetCount++;
            if (!empty($validated['nguoi_dung_bi_bao_cao_id'])) $targetCount++;

            if ($targetCount !== 1) {
                return response()->json(['success' => false, 'message' => 'Dữ liệu báo cáo không hợp lệ.'], 400);
            }

            // Determine the target user to notify
            $targetUserId = null;
            $systemMessage = '';

            if (!empty($validated['bai_viet_id'])) {
                $post = BaiViet::findOrFail($validated['bai_viet_id']);
                $targetUserId = $post->nguoi_dung_id;
                $systemMessage = 'Bài viết của bạn đã bị báo cáo với lý do: "' . $validated['ly_do'] . '". Vui lòng tuân thủ tiêu chuẩn cộng đồng.';
            } elseif (!empty($validated['binh_luan_id'])) {
                $comment = BinhLuan::findOrFail($validated['binh_luan_id']);
                $targetUserId = $comment->nguoi_dung_id;
                $systemMessage = 'Bình luận của bạn đã bị báo cáo với lý do: "' . $validated['ly_do'] . '". Vui lòng tuân thủ tiêu chuẩn cộng đồng.';
            } elseif (!empty($validated['nguoi_dung_bi_bao_cao_id'])) {
                $targetUserId = $validated['nguoi_dung_bi_bao_cao_id'];
                $systemMessage = 'Hồ sơ cá nhân của bạn đã bị báo cáo với lý do: "' . $validated['ly_do'] . '". Vui lòng kiểm tra lại hành vi.';
            }

            // Save report
            $report = BaoCao::create([
                'nguoi_bao_cao_id' => $currentUser->id,
                'bai_viet_id' => $validated['bai_viet_id'] ?? null,
                'binh_luan_id' => $validated['binh_luan_id'] ?? null,
                'nguoi_dung_bi_bao_cao_id' => $validated['nguoi_dung_bi_bao_cao_id'] ?? null,
                'ly_do' => $validated['ly_do'],
                'trang_thai' => 'cho_xu_ly',
                'ngay_tao' => now(),
            ]);

            // Send system warning notification if the target isn't the reporter itself
            if ($targetUserId && $targetUserId !== $currentUser->id) {
                ThongBao::create([
                    'nguoi_dung_id' => $targetUserId,
                    'nguoi_thuc_hien_id' => $currentUser->id,
                    'loai' => 'he_thong',
                    'bai_viet_id' => $validated['bai_viet_id'] ?? null,
                    'binh_luan_id' => $validated['binh_luan_id'] ?? null,
                    'da_doc' => false,
                    'noi_dung' => $systemMessage,
                    'ngay_tao' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Gửi báo cáo thành công. Chúng tôi sẽ xem xét nội dung này sớm nhất có thể.'
            ]);

        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
