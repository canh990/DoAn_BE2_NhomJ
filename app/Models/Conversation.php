<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Conversation là cuộc trò chuyện riêng tư ('ca_nhan') hoặc nhóm ('nhom').
 */
class Conversation extends Model
{
    protected $table = 'cuoc_tro_chuyen';

    public const CREATED_AT = 'ngay_tao';
    public const UPDATED_AT = 'ngay_cap_nhat';

    protected $fillable = [
        'loai',
        'ten_nhom',
        'anh_nhom',
    ];

    /**
     * Những người dùng thuộc cuộc trò chuyện này.
     * Bảng pivot lưu vai trò, trạng thái tắt thông báo, thời gian tham gia và thông tin đã đọc.
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'thanh_vien_nhom', 'cuoc_tro_chuyen_id', 'nguoi_dung_id')
            ->withPivot(['vai_tro', 'tat_thong_bao', 'ngay_tham_gia', 'doc_den_luc']);
    }

    /**
     * Tất cả tin nhắn đã gửi trong cuộc trò chuyện này.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'cuoc_tro_chuyen_id');
    }
}
