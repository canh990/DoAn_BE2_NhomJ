<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\BaiViet;

class CamXuc extends Model
{
    protected $table = 'cam_xuc';
    public $timestamps = false;

    protected $fillable = [
        'nguoi_dung_id',
        'bai_viet_id',
        'binh_luan_id',
        'loai_cam_xuc',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function baiViet(): BelongsTo
    {
        return $this->belongsTo(BaiViet::class, 'bai_viet_id');
    }
}
