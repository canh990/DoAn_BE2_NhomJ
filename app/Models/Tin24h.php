<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tin24h extends Model
{
    protected $table = 'tin_24h';

    // Bảng này dùng ngay_tao, không phải created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'nguoi_dung_id',
        'duong_dan_media',
        'loai_media',
        'quyen_rieng_tu',
        'het_han',
        'ngay_tao',
    ];

    protected $casts = [
        'het_han'  => 'datetime',
        'ngay_tao' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    /** Scope: chỉ lấy tin chưa hết hạn */
    public function scopeConHan($query)
    {
        return $query->where('het_han', '>', now());
    }
}
