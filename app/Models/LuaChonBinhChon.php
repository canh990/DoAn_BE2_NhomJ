<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LuaChonBinhChon extends Model
{
    protected $table = 'lua_chon_binh_chon';
    public $timestamps = false;

    protected $fillable = [
        'binh_chon_id',
        'noi_dung',
    ];

    protected $casts = [
        'ngay_tao' => 'datetime',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(BinhChon::class, 'binh_chon_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PhieuBau::class, 'lua_chon_id');
    }
}
