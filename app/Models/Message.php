<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Message đại diện cho một tin nhắn trong cuộc trò chuyện riêng tư hoặc nhóm.
 */
class Message extends Model
{
    protected $table = 'tin_nhan';

    // 1. TẮT HOÀN TOÀN TIMESTAMPS: Ngăn Laravel tự thêm 'created_at' hay 'updated_at' vào câu lệnh SQL
    public $timestamps = false; 

    // 2. Thêm 'ngay_tao' vào fillable để Laravel cho phép chèn dữ liệu bằng hàm create()
    protected $fillable = [
        'cuoc_tro_chuyen_id',
        'nguoi_gui_id',
        'noi_dung',
        'trang_thai',
        'da_thu_hoi',
        'kieu_xoa',
        'ngay_tao', // <-- Thêm dòng này vào đây
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'cuoc_tro_chuyen_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'nguoi_gui_id');
    }

    public function media()
    {
        return $this->hasMany(MessageMedia::class, 'tin_nhan_id');
    }
}