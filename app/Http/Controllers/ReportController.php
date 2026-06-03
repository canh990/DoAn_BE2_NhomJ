<?php

namespace App\Http\Controllers;

use App\Models\BaoCao;
use App\Models\BaiViet;
use App\Models\BinhLuan;
use App\Models\User;
use App\Models\ThongBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

            // Check duplicate reports
            $duplicateQuery = BaoCao::where('nguoi_bao_cao_id', $currentUser->id)
                ->where('trang_thai', 'cho_xu_ly');

            if (!empty($validated['bai_viet_id'])) {
                $duplicateQuery->where('bai_viet_id', $validated['bai_viet_id']);
            } elseif (!empty($validated['binh_luan_id'])) {
                $duplicateQuery->where('binh_luan_id', $validated['binh_luan_id']);
            } elseif (!empty($validated['nguoi_dung_bi_bao_cao_id'])) {
                $duplicateQuery->where('nguoi_dung_bi_bao_cao_id', $validated['nguoi_dung_bi_bao_cao_id']);
            }

            if ($duplicateQuery->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã gửi báo cáo cho nội dung này rồi và đang chờ hệ thống xử lý.'
                ], 422);
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

    /**
     * Admin view list of reports.
     */
    public function adminIndex(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $query = BaoCao::with(['nguoiBaoCao', 'nguoiBiBaoCao', 'baiViet.user', 'binhLuan.user'])
            ->latest('ngay_tao');

        // Apply filters
        if ($request->filled('type')) {
            $type = $request->input('type');
            if ($type === 'bai_viet') {
                $query->whereNotNull('bai_viet_id');
            } elseif ($type === 'binh_luan') {
                $query->whereNotNull('binh_luan_id');
            } elseif ($type === 'nguoi_dung') {
                $query->whereNotNull('nguoi_dung_bi_bao_cao_id');
            }
        }

        if ($request->filled('status')) {
            $query->where('trang_thai', $request->input('status'));
        }

        $reports = $query->paginate(15)->withQueryString();

        return view('admin.reports', compact('reports'));
    }

    /**
     * Admin processes a report (mark resolved or ignore).
     */
    public function adminAction(Request $request, BaoCao $report, $action)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!in_array($action, ['da_xu_ly', 'bo_qua'])) {
            return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.'], 400);
        }

        $report->update(['trang_thai' => $action]);

        return response()->json([
            'success' => true,
            'message' => $action === 'da_xu_ly' ? 'Đã đánh dấu xử lý thành công.' : 'Đã bỏ qua báo cáo này.'
        ]);
    }

    /**
     * Admin deletes violating content and resolves the report.
     */
    public function adminDeleteContent(Request $request, BaoCao $report)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        \DB::beginTransaction();
        try {
            if ($report->bai_viet_id) {
                $post = $report->baiViet;
                if ($post) {
                    if ($post->media) {
                        foreach ($post->media as $media) {
                            if (Storage::disk('public')->exists($media->duong_dan)) {
                                Storage::disk('public')->delete($media->duong_dan);
                            }
                            $media->delete();
                        }
                    }
                    $post->da_xoa = true;
                    $post->save();

                    BaoCao::where('bai_viet_id', $post->id)->update(['trang_thai' => 'da_xu_ly']);
                }
            } elseif ($report->binh_luan_id) {
                $comment = $report->binhLuan;
                if ($comment) {
                    $comment->delete();
                    BaoCao::where('binh_luan_id', $comment->id)->update(['trang_thai' => 'da_xu_ly']);
                }
            } elseif ($report->nguoi_dung_bi_bao_cao_id) {
                $user = $report->nguoiBiBaoCao;
                if ($user) {
                    $user->con_hoat_dong = false;
                    $user->save();

                    BaoCao::where('nguoi_dung_bi_bao_cao_id', $user->id)->update(['trang_thai' => 'da_xu_ly']);
                }
            }

            $report->update(['trang_thai' => 'da_xu_ly']);

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa nội dung vi phạm và cập nhật trạng thái báo cáo.'
            ]);
        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
}

