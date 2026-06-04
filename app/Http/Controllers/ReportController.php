<?php

namespace App\Http\Controllers;

use App\Models\BaoCao;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    /**
     * ReportController constructor.
     *
     * @param ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Store a new report and notify relevant users.
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

            $this->reportService->createReport($currentUser, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Gửi báo cáo thành công. Chúng tôi sẽ xem xét nội dung này sớm nhất có thể.'
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Admin view list of reports.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function adminIndex(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $filters = [
            'type' => $request->input('type'),
            'status' => $request->input('status', 'cho_xu_ly'),
        ];

        // Default filter should show 'cho_xu_ly' if not overridden
        if (!$request->has('status')) {
            $request->merge(['status' => 'cho_xu_ly']);
        }

        $reports = $this->reportService->getPaginatedReports($filters, 15)->withQueryString();

        return view('admin.reports', compact('reports'));
    }

    /**
     * Admin processes a report (mark resolved or ignore).
     *
     * @param Request $request
     * @param BaoCao $report
     * @param string $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminAction(Request $request, BaoCao $report, $action)
    {
        $admin = auth()->user();
        if ($admin->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $this->reportService->processReportAction($admin, $report, $action);

            return response()->json([
                'success' => true,
                'message' => $action === 'da_xu_ly' ? 'Đã đánh dấu xử lý thành công.' : 'Đã bỏ qua báo cáo này.'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Admin deletes violating content and resolves the report.
     *
     * @param Request $request
     * @param BaoCao $report
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminDeleteContent(Request $request, BaoCao $report)
    {
        $admin = auth()->user();
        if ($admin->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $this->reportService->deleteViolatingContent($admin, $report);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa nội dung vi phạm và cập nhật trạng thái báo cáo.'
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Admin locks or unlocks a user account.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminToggleUserStatus(Request $request, User $user)
    {
        $admin = auth()->user();
        if ($admin->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $newStatus = $this->reportService->toggleUserStatus($admin, $user);

            $statusMessage = $newStatus ? 'Đã kích hoạt lại tài khoản thành công.' : 'Đã khóa tài khoản thành công.';

            return response()->json([
                'success' => true,
                'message' => $statusMessage,
                'con_hoat_dong' => $newStatus
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
