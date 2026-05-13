<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaiViet;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    // ✅ Trỏ đúng tên bảng
    protected $table = 'nguoi_dung';

    // ✅ Tên cột xóa mềm
    const DELETED_AT = 'ngay_xoa';

    protected $fillable = [
        'ten_dang_nhap',
        'email',
        'so_dien_thoai',
        'mat_khau_hash',
        'anh_dai_dien',
        'anh_bia',
        'tieu_su',
        'ngay_sinh',
        'noi_o',
        'quyen_rieng_tu',
        'da_xac_thuc',
        'otp_code',
        'otp_het_han',
        'con_hoat_dong',
        'nha_cung_cap_oauth',
        'id_oauth',
    ];

    protected $hidden = [
        'mat_khau_hash',
    ];

    // ✅ Trỏ đúng cột mật khẩu
    public function getAuthPassword()
    {
        return $this->mat_khau_hash;
    }

    public function getRememberTokenName()
    {
        return null;
    }
    // Nếu bạn muốn dùng 'ten_dang_nhap' làm tên hiển thị chính
    public function getNameAttribute()
    {
        return $this->ten_dang_nhap;
    }

    // Người theo dõi tôi
    public function followers()
    {
        return $this->belongsToMany(User::class, 'theo_doi', 'nguoi_duoc_theo_doi_id', 'nguoi_theo_doi_id')
                    ->withPivot('trang_thai', 'ngay_tao');
    }

    // Những người tôi đang theo dõi
    public function following()
    {
        return $this->belongsToMany(User::class, 'theo_doi', 'nguoi_theo_doi_id', 'nguoi_duoc_theo_doi_id')
                    ->withPivot('trang_thai', 'ngay_tao');
    }

    public function posts()
    {
        return $this->hasMany(BaiViet::class, 'nguoi_dung_id');
    }

    public function thongBaos()
    {
        return $this->hasMany(ThongBao::class, 'nguoi_dung_id');
    }

    public function unreadThongBaos()
    {
        return $this->hasMany(ThongBao::class, 'nguoi_dung_id')->where('da_doc', false);
    }
}
    public function isFriendsWith($otherUserId)
    {
        if (!$otherUserId) return false;

        // Check if I follow them and they follow me (both accepted)
        $following = $this->following()
            ->where('nguoi_duoc_theo_doi_id', $otherUserId)
            ->where('trang_thai', 'da_chap_nhan')
            ->exists();

        $follower = $this->followers()
            ->where('nguoi_theo_doi_id', $otherUserId)
            ->where('trang_thai', 'da_chap_nhan')
            ->exists();

        return $following && $follower;
    }
}
