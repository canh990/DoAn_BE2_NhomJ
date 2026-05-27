<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhieuBau extends Model
{
    protected $table = 'phieu_bau';
    public $timestamps = false;

    protected $fillable = [
        'binh_chon_id',
        'lua_chon_id',
        'nguoi_dung_id',
    ];

    protected $casts = [
        'ngay_tao' => 'datetime',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(BinhChon::class, 'binh_chon_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(LuaChonBinhChon::class, 'lua_chon_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
