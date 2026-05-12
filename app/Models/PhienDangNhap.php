<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhienDangNhap extends Model
{
    protected $table = 'phien_dang_nhap';

    public $timestamps = false;

    protected $fillable = [
        'nguoi_dung_id',
        'token_hash',
        'ten_thiet_bi',
        'trinh_duyet',
        'he_dieu_hanh',
        'user_agent',
        'dia_chi_ip',
        'lan_hoat_dong_cuoi',
        'dang_xuat_luc',
        'la_phien_hien_tai',
        'het_han',
        'ngay_tao',
    ];

    protected $casts = [
        'het_han' => 'datetime',
        'ngay_tao' => 'datetime',
        'lan_hoat_dong_cuoi' => 'datetime',
        'dang_xuat_luc' => 'datetime',
        'la_phien_hien_tai' => 'boolean',
    ];
}