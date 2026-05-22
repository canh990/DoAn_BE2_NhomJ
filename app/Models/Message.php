<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Message đại diện cho một tin nhắn trong cuộc trò chuyện riêng tư hoặc nhóm.
 */
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
        'da_thu_hoi',
        'kieu_xoa',
    ];

    /**
     * Cuộc trò chuyện mà tin nhắn này thuộc về.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'cuoc_tro_chuyen_id');
    }

    /**
     * Người dùng đã gửi tin nhắn này.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'nguoi_gui_id');
    }

    /**
     * Các tệp đính kèm liên quan đến tin nhắn này.
     */
    public function media()
    {
        return $this->hasMany(MessageMedia::class, 'tin_nhan_id');
    }
}
