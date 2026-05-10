<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;
use App\Models\CamXuc;
use App\Models\BinhLuan;

class BaiViet extends Model
{
    protected $table = 'bai_viet';

    protected $fillable = [
        'nguoi_dung_id',
        'loai',
        'bai_goc_id',
        'noi_dung',
        'cam_xuc',
        'hoat_dong',
        'quyen_rieng_tu',
        'da_chinh_sua',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }



    public function reactions(): HasMany
    {
        return $this->hasMany(CamXuc::class, 'bai_viet_id');

    }


    public function media(): HasMany
    {
        return $this->hasMany(MediaBaiViet::class, 'bai_viet_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BinhLuan::class, 'bai_viet_id')->latest('ngay_tao');
    }

    public function originalPost(): BelongsTo
    {
        return $this->belongsTo(BaiViet::class, 'bai_goc_id');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(BaiViet::class, 'bai_goc_id');
    }
}
