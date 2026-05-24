<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaoCao extends Model
{
    use HasFactory;

    protected $table = 'bao_cao';

    public $timestamps = false; // Bảng này chỉ có ngay_tao

    protected $fillable = [
        'nguoi_bao_cao_id',
        'bai_viet_id',
        'binh_luan_id',
        'nguoi_dung_bi_bao_cao_id',
        'ly_do',
        'trang_thai',
        'ngay_tao',
    ];

    protected $casts = [
        'ngay_tao' => 'datetime',
    ];

    public function nguoiBaoCao()
    {
        return $this->belongsTo(User::class, 'nguoi_bao_cao_id');
    }

    public function baiViet()
    {
        return $this->belongsTo(BaiViet::class, 'bai_viet_id');
    }

    public function binhLuan()
    {
        return $this->belongsTo(BinhLuan::class, 'binh_luan_id');
    }

    public function nguoiBiBaoCao()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_bi_bao_cao_id');
    }
}
