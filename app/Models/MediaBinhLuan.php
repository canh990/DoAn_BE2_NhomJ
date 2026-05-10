<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaBinhLuan extends Model
{
    protected $table = 'media_binh_luan';

    public $timestamps = false; // Because table only has ngay_tao

    protected $fillable = [
        'binh_luan_id',
        'loai',
        'duong_dan',
        'ngay_tao',
    ];

    protected $casts = [
        'ngay_tao' => 'datetime',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(BinhLuan::class, 'binh_luan_id');
    }
}
