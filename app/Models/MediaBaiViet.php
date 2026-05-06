<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaBaiViet extends Model
{
    protected $table = 'media_bai_viet';

    public $timestamps = false; // Bảng này không có created_at/updated_at

    protected $fillable = [
        'bai_viet_id',
        'loai',
        'duong_dan',
        'thu_tu',
        'ngay_tao',
    ];

    public function baiViet(): BelongsTo
    {
        return $this->belongsTo(BaiViet::class, 'bai_viet_id');
    }
}
