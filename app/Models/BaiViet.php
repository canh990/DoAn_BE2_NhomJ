<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;

class BaiViet extends Model
{
    protected $table = 'bai_viet';

    protected $fillable = [
        'nguoi_dung_id',
        'loai',
        'noi_dung',
        'quyen_rieng_tu',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
