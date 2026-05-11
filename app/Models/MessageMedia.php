<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageMedia extends Model
{
    protected $table = 'media_tin_nhan';

    public const CREATED_AT = 'ngay_tao';
    public const UPDATED_AT = null;

    protected $fillable = [
        'tin_nhan_id',
        'loai',
        'duong_dan',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class, 'tin_nhan_id');
    }
}
