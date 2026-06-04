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
        'da_ghim',
    ];

    public function getFormattedContentAttribute()
    {
        return resolve(\App\Services\MentionService::class)->highlightMentions($this->noi_dung);
    }

    public const CREATED_AT = 'ngay_tao';
    public const UPDATED_AT = 'ngay_cap_nhat';

    protected static function booted()
    {
        static::deleting(function ($comment) {
            // Xóa tất cả các bình luận con qua Eloquent để kích hoạt sự kiện deleting của chúng và xóa file vật lý
            foreach ($comment->children as $child) {
                $child->delete();
            }

            // Xóa file vật lý media của bình luận này
            foreach ($comment->media as $media) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($media->duong_dan)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($media->duong_dan);
                }
            }
        });
    }

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
        return $this->children()->with(['user', 'media', 'nestedChildren']);
    }

    public function media(): HasMany
    {
        return $this->hasMany(MediaBinhLuan::class, 'binh_luan_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(CamXuc::class, 'binh_luan_id');
    }
}
