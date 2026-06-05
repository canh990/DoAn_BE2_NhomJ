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
        'ten_hien_thi',
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
        'remember_token',
        'role',
    ];

    protected $hidden = [
        'mat_khau_hash',
        'remember_token',
    ];

    protected $appends = [
        'avatar_url',
        'is_online',
        'status_text',
    ];

    // ✅ Trỏ đúng cột mật khẩu
    public function getAuthPassword()
    {
        return $this->mat_khau_hash;
    }

    // ✅ Bật remember token để hỗ trợ "Ghi nhớ đăng nhập"
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
    // Trả về tên hiển thị: ưu tiên ten_hien_thi, fallback về ten_dang_nhap
    public function getNameAttribute()
    {
        return $this->ten_hien_thi ?: $this->ten_dang_nhap;
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

    /**
     * Danh sách các dòng dữ liệu Bookmark.
     */
    public function bookmarks()
    {
        return $this->hasMany(BaiVietDaLuu::class, 'nguoi_dung_id');
    }

    /**
     * Danh sách các bài viết mà User đã lưu.
     */
    public function bookmarkedPosts()
    {
        return $this->belongsToMany(BaiViet::class, 'bai_viet_da_luu', 'nguoi_dung_id', 'bai_viet_id')
                    ->withPivot('ngay_tao');
    }

    // Danh sách những người tôi đã chặn
    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'chan', 'nguoi_chan_id', 'nguoi_bi_chan_id')
                    ->withPivot('ngay_tao');
    }

    // Danh sách những người đã chặn tôi
    public function blockedByUsers()
    {
        return $this->belongsToMany(User::class, 'chan', 'nguoi_bi_chan_id', 'nguoi_chan_id')
                    ->withPivot('ngay_tao');
    }

    // Kiểm tra tôi có chặn ai đó không
    public function hasBlocked($userId)
    {
        return $this->blockedUsers()->where('nguoi_bi_chan_id', $userId)->exists();
    }

    // Kiểm tra ai đó có chặn tôi không
    public function isBlockedBy($userId)
    {
        return $this->blockedByUsers()->where('nguoi_chan_id', $userId)->exists();
    }

    // Kiểm tra giữa 2 người có bất kỳ mối quan hệ chặn nào không
    public function hasAnyBlockRelationship($userId)
    {
        return $this->hasBlocked($userId) || $this->isBlockedBy($userId);
    }

    /**
     * Kiểm tra người dùng có đang hoạt động hay không.
     */
    public function isOnline()
    {
        return \Illuminate\Support\Facades\Cache::has('user-is-online-' . $this->id);
    }

    /**
     * Accessor cho is_online.
     */
    public function getIsOnlineAttribute()
    {
        return $this->isOnline();
    }

    /**
     * Accessor cho status_text.
     */
    public function getStatusTextAttribute()
    {
        $isOnline = $this->isOnline();
        if ($isOnline) {
            return 'Online';
        }
        
        $lastSeen = \Illuminate\Support\Facades\Cache::get('user-last-seen-' . $this->id);
        if ($lastSeen) {
            $diff = now()->timestamp - $lastSeen;
            if ($diff < 60) {
                return 'Vừa hoạt động';
            } elseif ($diff < 3600) {
                return 'Hoạt động ' . round($diff / 60) . ' phút trước';
            } elseif ($diff < 86400) {
                return 'Hoạt động ' . round($diff / 3600) . ' giờ trước';
            } else {
                return 'Hoạt động ' . round($diff / 86400) . ' ngày trước';
            }
        }
        
        return 'Offline';
    }

    /**
     * Lấy URL ảnh đại diện đầy đủ và chính xác (hỗ trợ cả URL ngoại vi và đường dẫn lưu trữ cục bộ).
     */
    public function getAvatarUrlAttribute()
    {
        if (!$this->anh_dai_dien) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->ten_hien_thi ?: $this->ten_dang_nhap) . '&background=random';
        }
        
        if (filter_var($this->anh_dai_dien, FILTER_VALIDATE_URL)) {
            return $this->anh_dai_dien;
        }
        
        return asset('storage/' . $this->anh_dai_dien);
    }
}
