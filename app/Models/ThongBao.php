<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    use HasFactory;

    protected $table = 'thong_bao';
    
    // Bảng này không có updated_at, chỉ có ngay_tao (kiểu timestamp)
    public $timestamps = false;
    
    protected $fillable = [
        'nguoi_dung_id',
        'nguoi_thuc_hien_id',
        'loai',
        'bai_viet_id',
        'binh_luan_id',
        'cuoc_tro_chuyen_id',
        'da_doc',
        'ngay_tao',
    ];

    protected $casts = [
        'da_doc' => 'boolean',
        'ngay_tao' => 'datetime',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function nguoiThucHien()
    {
        return $this->belongsTo(User::class, 'nguoi_thuc_hien_id');
    }

    public function baiViet()
    {
        return $this->belongsTo(BaiViet::class, 'bai_viet_id');
    }

    public function binhLuan()
    {
        return $this->belongsTo(BinhLuan::class, 'binh_luan_id');
    }

    public function cuocTroChuyen()
    {
        return $this->belongsTo(Conversation::class, 'cuoc_tro_chuyen_id');
    }
}
