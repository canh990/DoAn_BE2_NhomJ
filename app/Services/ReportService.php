<?php

namespace App\Services;

use App\Models\BaoCao;
use App\Models\BaiViet;
use App\Models\BinhLuan;
use App\Models\User;
use App\Models\ThongBao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    /**
     * Create a new report and send necessary notifications.
     *
     * @param User $currentUser
     * @param array $data
     * @return BaoCao
     * @throws \Exception
     */
    public function createReport(User $currentUser, array $data)
    {
        // Ensure exactly one entity is being reported
        $targetCount = 0;
        if (!empty($data['bai_viet_id'])) $targetCount++;
        if (!empty($data['binh_luan_id'])) $targetCount++;
        if (!empty($data['nguoi_dung_bi_bao_cao_id'])) $targetCount++;

        if ($targetCount !== 1) {
            throw new \InvalidArgumentException('Dữ liệu báo cáo không hợp lệ.');
        }

        // Check duplicate reports
        $duplicateQuery = BaoCao::where('nguoi_bao_cao_id', $currentUser->id)
            ->where('trang_thai', 'cho_xu_ly');

        if (!empty($data['bai_viet_id'])) {
            $duplicateQuery->where('bai_viet_id', $data['bai_viet_id']);
        } elseif (!empty($data['binh_luan_id'])) {
            $duplicateQuery->where('binh_luan_id', $data['binh_luan_id']);
        } elseif (!empty($data['nguoi_dung_bi_bao_cao_id'])) {
            $duplicateQuery->where('nguoi_dung_bi_bao_cao_id', $data['nguoi_dung_bi_bao_cao_id']);
        }

        if ($duplicateQuery->exists()) {
            throw new \RuntimeException('Bạn đã gửi báo cáo cho nội dung này rồi và đang chờ hệ thống xử lý.');
        }

        return DB::transaction(function () use ($currentUser, $data) {
            // Determine the target user to notify (system warning)
            $targetUserId = null;
            $systemMessage = '';

            if (!empty($data['bai_viet_id'])) {
                $post = BaiViet::findOrFail($data['bai_viet_id']);
                $targetUserId = $post->nguoi_dung_id;
                $systemMessage = 'Bài viết của bạn đã bị báo cáo với lý do: "' . $data['ly_do'] . '". Vui lòng tuân thủ tiêu chuẩn cộng đồng.';
            } elseif (!empty($data['binh_luan_id'])) {
                $comment = BinhLuan::findOrFail($data['binh_luan_id']);
                $targetUserId = $comment->nguoi_dung_id;
                $systemMessage = 'Bình luận của bạn đã bị báo cáo với lý do: "' . $data['ly_do'] . '". Vui lòng tuân thủ tiêu chuẩn cộng đồng.';
            } elseif (!empty($data['nguoi_dung_bi_bao_cao_id'])) {
                $targetUserId = $data['nguoi_dung_bi_bao_cao_id'];
                $systemMessage = 'Hồ sơ cá nhân của bạn đã bị báo cáo với lý do: "' . $data['ly_do'] . '". Vui lòng kiểm tra lại hành vi.';
            }

            // Save report
            $report = BaoCao::create([
                'nguoi_bao_cao_id' => $currentUser->id,
                'bai_viet_id' => $data['bai_viet_id'] ?? null,
                'binh_luan_id' => $data['binh_luan_id'] ?? null,
                'nguoi_dung_bi_bao_cao_id' => $data['nguoi_dung_bi_bao_cao_id'] ?? null,
                'ly_do' => $data['ly_do'],
                'trang_thai' => 'cho_xu_ly',
                'ngay_tao' => now(),
            ]);

            // Send system warning notification if the target isn't the reporter itself
            if ($targetUserId && $targetUserId !== $currentUser->id) {
                ThongBao::create([
                    'nguoi_dung_id' => $targetUserId,
                    'nguoi_thuc_hien_id' => $currentUser->id,
                    'loai' => 'he_thong',
                    'bai_viet_id' => $data['bai_viet_id'] ?? null,
                    'binh_luan_id' => $data['binh_luan_id'] ?? null,
                    'da_doc' => false,
                    'noi_dung' => $systemMessage,
                    'ngay_tao' => now(),
                ]);
            }

            // Notify all administrators about the new report
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                if ($admin->id !== $currentUser->id) {
                    ThongBao::create([
                        'nguoi_dung_id' => $admin->id,
                        'nguoi_thuc_hien_id' => $currentUser->id,
                        'loai' => 'bao_cao',
                        'bai_viet_id' => $data['bai_viet_id'] ?? null,
                        'binh_luan_id' => $data['binh_luan_id'] ?? null,
                        'da_doc' => false,
                        'noi_dung' => 'Nội dung mới bị báo cáo vi phạm với lý do: "' . $data['ly_do'] . '".',
                        'ngay_tao' => now(),
                    ]);
                }
            }

            return $report;
        });
    }

    /**
     * Get paginated reports with eager loaded relations.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedReports(array $filters, $perPage = 15)
    {
        $query = BaoCao::with(['nguoiBaoCao', 'nguoiBiBaoCao', 'baiViet.user', 'binhLuan.user'])
            ->latest('ngay_tao');

        if (!empty($filters['type'])) {
            $type = $filters['type'];
            if ($type === 'bai_viet') {
                $query->whereNotNull('bai_viet_id');
            } elseif ($type === 'binh_luan') {
                $query->whereNotNull('binh_luan_id');
            } elseif ($type === 'nguoi_dung') {
                $query->whereNotNull('nguoi_dung_bi_bao_cao_id');
            }
        }

        if (!empty($filters['status'])) {
            $query->where('trang_thai', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Admin processes a report (mark resolved or ignore).
     *
     * @param User $admin
     * @param BaoCao $report
     * @param string $action
     * @return void
     * @throws \Exception
     */
    public function processReportAction(User $admin, BaoCao $report, string $action)
    {
        if (!in_array($action, ['da_xu_ly', 'bo_qua'])) {
            throw new \InvalidArgumentException('Hành động không hợp lệ.');
        }

        DB::transaction(function () use ($admin, $report, $action) {
            $report->update(['trang_thai' => $action]);

            // Send feedback notification to the reporter
            if ($report->nguoi_bao_cao_id) {
                $feedbackMessage = $action === 'da_xu_ly'
                    ? 'Báo cáo của bạn về nội dung vi phạm đã được xử lý. Cảm ơn bạn đã đóng góp xây dựng cộng đồng.'
                    : 'Báo cáo của bạn đã được xem xét. Nội dung được giữ lại do chưa phát hiện vi phạm tiêu chuẩn cộng đồng.';

                ThongBao::create([
                    'nguoi_dung_id' => $report->nguoi_bao_cao_id,
                    'nguoi_thuc_hien_id' => $admin->id,
                    'loai' => 'he_thong',
                    'bai_viet_id' => $report->bai_viet_id,
                    'binh_luan_id' => $report->binh_luan_id,
                    'da_doc' => false,
                    'noi_dung' => $feedbackMessage,
                    'ngay_tao' => now(),
                ]);
            }
        });
    }

    /**
     * Admin deletes violating content and resolves the report.
     *
     * @param User $admin
     * @param BaoCao $report
     * @return void
     * @throws \Exception
     */
    public function deleteViolatingContent(User $admin, BaoCao $report)
    {
        DB::transaction(function () use ($admin, $report) {
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

            // Send feedback notification to the reporter
            if ($report->nguoi_bao_cao_id) {
                ThongBao::create([
                    'nguoi_dung_id' => $report->nguoi_bao_cao_id,
                    'nguoi_thuc_hien_id' => $admin->id,
                    'loai' => 'he_thong',
                    'bai_viet_id' => $report->bai_viet_id,
                    'binh_luan_id' => $report->binh_luan_id,
                    'da_doc' => false,
                    'noi_dung' => 'Báo cáo của bạn về nội dung vi phạm đã được xử lý (nội dung vi phạm đã bị gỡ bỏ). Cảm ơn bạn đã đóng góp xây dựng cộng đồng.',
                    'ngay_tao' => now(),
                ]);
            }
        });
    }

    /**
     * Admin locks or unlocks a user account.
     *
     * @param User $admin
     * @param User $user
     * @return bool New active status
     * @throws \Exception
     */
    public function toggleUserStatus(User $admin, User $user)
    {
        return DB::transaction(function () use ($admin, $user) {
            $user->con_hoat_dong = !$user->con_hoat_dong;
            $user->save();
            return $user->con_hoat_dong;
        });
    }
}
