<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hashtag extends Model
{
    protected $table = 'hashtag';
    public $timestamps = false;

    protected $fillable = [
        'ten',
        'so_bai_viet',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BaiViet::class, 'bai_viet_hashtag', 'hashtag_id', 'bai_viet_id');
    }
}
