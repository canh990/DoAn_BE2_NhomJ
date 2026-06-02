<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NhatKyHoatDong extends Model
{
    // Chỉ định tên bảng trong cơ sở dữ liệu
    protected $table = 'nhat_ky_hoat_dong';

    // Bảng chỉ dùng ngay_tao (timestamp), không sử dụng updated_at
    public const CREATED_AT = 'ngay_tao';
    public const UPDATED_AT = null;

    protected $fillable = [
        'nguoi_dung_id',
        'hanh_dong',
        'doi_tuong_id',
        'loai_doi_tuong',
        'ngay_tao',
    ];

    /**
     * Mối quan hệ với người thực hiện hoạt động.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    /**
     * Hàm helper static ghi log hoạt động nhanh chóng.
     *
     * @param int $userId ID người dùng
     * @param string $action Hành động hệ thống hoặc tương tác (ví dụ: 'dang_nhap', 'dang_ky')
     * @param int $objectId ID đối tượng liên quan (nếu có, ví dụ post ID)
     * @param string $objectType Loại đối tượng liên quan (mặc định là 'he_thong')
     * @return NhatKyHoatDong
     */
    public static function log($userId, $action, $objectId = 0, $objectType = 'he_thong')
    {
        return self::create([
            'nguoi_dung_id' => $userId,
            'hanh_dong' => $action,
            'doi_tuong_id' => $objectId,
            'loai_doi_tuong' => $objectType,
        ]);
    }
}
