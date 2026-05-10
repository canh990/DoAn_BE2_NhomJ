<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BaiViet;
use App\Models\User;

class BinhLuan extends Model
{
    protected $table = 'binh_luan';

    protected $fillable = [
        'bai_viet_id',
        'nguoi_dung_id',
        'binh_luan_cha_id',
        'noi_dung',
        'da_xoa',
    ];

    public const CREATED_AT = 'ngay_tao';
    public const UPDATED_AT = 'ngay_cap_nhat';

    public function post(): BelongsTo
    {
        return $this->belongsTo(BaiViet::class, 'bai_viet_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BinhLuan::class, 'binh_luan_cha_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(BinhLuan::class, 'binh_luan_cha_id')->latest('ngay_tao');
    }

    public function nestedChildren(): HasMany
    {
        return $this->children()->with(['user', 'nestedChildren']);
    }
}
