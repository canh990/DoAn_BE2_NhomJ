<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function members()
    {
        return $this->belongsToMany(User::class, 'thanh_vien_nhom', 'cuoc_tro_chuyen_id', 'nguoi_dung_id')
            ->withPivot(['vai_tro', 'tat_thong_bao', 'ngay_tham_gia', 'doc_den_luc']);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'cuoc_tro_chuyen_id');
    }
}
