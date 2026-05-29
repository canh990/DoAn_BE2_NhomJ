<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BinhChon extends Model
{
    protected $table = 'binh_chon';
    public $timestamps = false;

    protected $fillable = [
        'bai_viet_id',
        'cau_hoi',
        'ngay_ket_thuc',
    ];

    protected $casts = [
        'ngay_ket_thuc' => 'datetime',
        'ngay_tao' => 'datetime',
    ];

    public function baiViet(): BelongsTo
    {
        return $this->belongsTo(BaiViet::class, 'bai_viet_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(LuaChonBinhChon::class, 'binh_chon_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PhieuBau::class, 'binh_chon_id');
    }
}
