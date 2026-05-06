<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;
use App\Models\CamXuc;

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


    public function reactions(): HasMany
    {
        return $this->hasMany(CamXuc::class, 'bai_viet_id');

    public function media(): HasMany
    {
        return $this->hasMany(MediaBaiViet::class, 'bai_viet_id');
    }
}
