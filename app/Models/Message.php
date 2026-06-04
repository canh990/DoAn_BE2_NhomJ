<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'tin_nhan';

    public const CREATED_AT = 'ngay_tao';
    public const UPDATED_AT = null;

    protected $fillable = [
        'cuoc_tro_chuyen_id',
        'nguoi_gui_id',
        'noi_dung',
        'trang_thai',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'cuoc_tro_chuyen_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'nguoi_gui_id');
    }

    // THÊM ĐOẠN NÀY
    public function media()
    {
        return $this->hasMany(MessageMedia::class, 'tin_nhan_id');
    }
}