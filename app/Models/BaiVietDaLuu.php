<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaiVietDaLuu extends Model
{
    // ✅ Trỏ đúng tên bảng trong database
    protected $table = 'bai_viet_da_luu';

    // Cấu hình timestamps (chỉ dùng ngay_tao, không có ngay_cap_nhat/updated_at)
    public const CREATED_AT = 'ngay_tao';
    public const UPDATED_AT = null;

    protected $fillable = [
        'nguoi_dung_id',
        'bai_viet_id',
        'ngay_tao',
    ];

    /**
     * Quan hệ BelongsTo với Model User (Người dùng đã lưu bài viết).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    /**
     * Quan hệ BelongsTo với Model BaiViet (Bài viết được lưu).
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(BaiViet::class, 'bai_viet_id');
    }
}
