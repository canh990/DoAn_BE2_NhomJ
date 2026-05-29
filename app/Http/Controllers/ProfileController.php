<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\BaiViet;
use App\Models\MediaBaiViet;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->loadCount(['followers', 'following']);

        $posts = $user->posts()
            ->with(['user', 'media', 'originalPost.user', 'originalPost.media', 'poll.options.votes', 'poll.votes'])
            ->withCount(['reactions', 'comments', 'shares'])
            ->with(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
            }, 'bookmarks' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }])
            ->whereIn('loai', ['van_ban', 'hinh_anh', 'chia_se', 'binh_chon'])
            ->where('da_xoa', false)
            ->orderBy('da_ghim', 'desc')
            ->latest()
            ->take(20)
            ->get();

        $stories = \App\Models\Tin24h::with('user')
            ->where('nguoi_dung_id', $user->id)
            ->conHan()
            ->latest('ngay_tao')
            ->get();

        $userMedia = \App\Models\MediaBaiViet::whereIn('bai_viet_id', $user->posts()->pluck('id'))
            ->latest('ngay_tao')
            ->get();

        // Bạn bè = những người theo dõi lẫn nhau (mutual follow)
        $followerIds = $user->followers()->wherePivot('trang_thai', 'da_chap_nhan')->pluck('nguoi_dung.id');
        $followingIds = $user->following()->wherePivot('trang_thai', 'da_chap_nhan')->pluck('nguoi_dung.id');
        $mutualFriendIds = $followerIds->intersect($followingIds);
        $mutualFriends = User::whereIn('id', $mutualFriendIds)->get();

        $likedPosts = auth()->check()
            ? BaiViet::query()
                ->whereHas('reactions', function ($query) {
                    $query->where('nguoi_dung_id', auth()->id());
                })
                ->where('da_xoa', false)
                ->with(['user', 'media'])
                ->latest()
                ->take(20)
                ->get()
            : collect();

        $myComments = auth()->check()
            ? \App\Models\BinhLuan::query()
                ->where('nguoi_dung_id', auth()->id())
                ->where('da_xoa', false)
                ->with(['post.user'])
                ->latest('ngay_tao')
                ->take(20)
                ->get()
            : collect();

        $savedPosts = auth()->check()
            ? \App\Models\BaiVietDaLuu::query()
                ->where('nguoi_dung_id', auth()->id())
                ->with(['post.user', 'post.media'])
                ->latest('ngay_tao')
                ->take(20)
                ->get()
            : collect();
            
        return view('profile.profile', [
            'user' => $user,
            'posts' => $posts,
            'stories' => $stories,
            'userMedia' => $userMedia,
            'mutualFriends' => $mutualFriends,
            'likedPosts' => $likedPosts,
            'myComments' => $myComments,
            'savedPosts' => $savedPosts,
        ]);
    }

    public function showByUsername(string $username)
    {
        $user = User::query()
            ->where('ten_dang_nhap', $username)
            ->where('con_hoat_dong', true)
            ->withCount(['followers', 'following'])
            ->first();

        if (!$user) {
            return redirect()->route('home')->with('error', 'Không tìm thấy tài khoản người dùng.');
        }

        if (auth()->check() && auth()->user()->hasAnyBlockRelationship($user->id)) {
            return redirect()->route('home')->with('error', 'Bạn không thể xem trang cá nhân của người này.');
        }

        $posts = $user->posts()
            ->with(['user', 'media', 'originalPost.user', 'originalPost.media', 'poll.options.votes', 'poll.votes'])
            ->withCount(['reactions', 'comments', 'shares'])
            ->with(['reactions' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }, 'comments' => function ($query) {
                $query->whereNull('binh_luan_cha_id')->with(['user', 'nestedChildren'])->latest('ngay_tao');
            }, 'bookmarks' => function ($query) {
                $query->where('nguoi_dung_id', auth()->id());
            }])
            ->whereIn('loai', ['van_ban', 'hinh_anh', 'chia_se', 'binh_chon'])
            ->where('da_xoa', false)
            ->orderBy('da_ghim', 'desc')
            ->latest()
            ->take(20)
            ->get();

        $stories = \App\Models\Tin24h::with('user')
            ->where('nguoi_dung_id', $user->id)
            ->conHan()
            ->latest('ngay_tao')
            ->get();

        $userMedia = \App\Models\MediaBaiViet::whereIn('bai_viet_id', $user->posts()->pluck('id'))
            ->latest('ngay_tao')
            ->get();

        $isOwnProfile = auth()->check() && auth()->id() === $user->id;

        // Bạn bè = những người theo dõi lẫn nhau (mutual follow)
        $followerIds = $user->followers()->wherePivot('trang_thai', 'da_chap_nhan')->pluck('nguoi_dung.id');
        $followingIds = $user->following()->wherePivot('trang_thai', 'da_chap_nhan')->pluck('nguoi_dung.id');
        $mutualFriendIds = $followerIds->intersect($followingIds);
        $mutualFriends = User::whereIn('id', $mutualFriendIds)->get();
        
        $likedPosts = $isOwnProfile
            ? BaiViet::query()
                ->whereHas('reactions', function ($query) {
                    $query->where('nguoi_dung_id', auth()->id());
                })
                ->where('da_xoa', false)
                ->with(['user', 'media'])
                ->latest()
                ->take(20)
                ->get()
            : collect();

        $myComments = $isOwnProfile
            ? \App\Models\BinhLuan::query()
                ->where('nguoi_dung_id', auth()->id())
                ->where('da_xoa', false)
                ->with(['post.user'])
                ->latest('ngay_tao')
                ->take(20)
                ->get()
            : collect();

        $savedPosts = $isOwnProfile
            ? \App\Models\BaiVietDaLuu::query()
                ->where('nguoi_dung_id', auth()->id())
                ->with(['post.user', 'post.media'])
                ->latest('ngay_tao')
                ->take(20)
                ->get()
            : collect();

        return view('profile.profile', [
            'user' => $user,
            'posts' => $posts,
            'stories' => $stories,
            'userMedia' => $userMedia,
            'mutualFriends' => $mutualFriends,
            'likedPosts' => $likedPosts,
            'myComments' => $myComments,
            'savedPosts' => $savedPosts,
        ]);
    }

    public function edit()
    {
        $user = auth()->user();
        $blockedUsers = $user->blockedUsers()->get();

        return view('profile.profile-edit', [
            'user' => $user,
            'blockedUsers' => $blockedUsers,
        ]);
    }

  public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'ten_dang_nhap' => [
                'required',
                'string',
                'max:50',
                Rule::unique('nguoi_dung', 'ten_dang_nhap')->ignore($user->id),
            ],
            'so_dien_thoai' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('nguoi_dung', 'so_dien_thoai')->ignore($user->id),
            ],
            'tieu_su' => ['nullable', 'string', 'max:1000'],
            'ngay_sinh' => ['nullable', 'date', 'before_or_equal:today'], // Quy tắc validate
            'noi_o' => ['nullable', 'string', 'max:255'],
            'quyen_rieng_tu' => ['required', Rule::in(['cong_khai', 'ban_be', 'rieng_tu'])],
            'anh_dai_dien' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'anh_bia' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            // Tin nhắn lỗi tiếng Việt tương ứng
            'ngay_sinh.date' => 'Ngày sinh không đúng định dạng ngày tháng.',
            'ngay_sinh.before_or_equal' => 'Ngày,Tháng,Năm sinh không thể lớn hơn hiện tại.',
            'ten_dang_nhap.unique' => 'Tên đăng nhập này đã được sử dụng.',
            'so_dien_thoai.unique' => 'Số điện thoại này đã được sử dụng.',
            'anh_dai_dien.image' => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'anh_dai_dien.mimes' => 'Ảnh đại diện chỉ chấp nhận định dạng: jpg, jpeg, png, webp.',
            'anh_dai_dien.max' => 'Kích thước ảnh đại diện không được vượt quá 2MB.',
            'anh_bia.image' => 'Ảnh bìa phải là một tệp hình ảnh.',
            'anh_bia.mimes' => 'Ảnh bìa chỉ chấp nhận định dạng: jpg, jpeg, png, webp.',
            'anh_bia.max' => 'Kích thước ảnh bìa không được vượt quá 4MB.',
        ]);

        // Xử lý ảnh đại diện
        if ($request->hasFile('anh_dai_dien')) {
            if ($user->anh_dai_dien) {
                Storage::disk('public')->delete($user->anh_dai_dien);
            }
            $validated['anh_dai_dien'] = $request->file('anh_dai_dien')->store('avatars', 'public');
        } elseif ($request->input('remove_avatar') == '1') {
            if ($user->anh_dai_dien) {
                Storage::disk('public')->delete($user->anh_dai_dien);
            }
            $validated['anh_dai_dien'] = null;

            // Xóa các bài đăng thông báo cập nhật ảnh đại diện trước đó
            BaiViet::where('nguoi_dung_id', $user->id)
                ->where('noi_dung', 'vừa cập nhật ảnh đại diện mới.')
                ->delete();
        }

        // Xử lý ảnh bìa
        if ($request->hasFile('anh_bia')) {
            if ($user->anh_bia) {
                Storage::disk('public')->delete($user->anh_bia);
            }
            $validated['anh_bia'] = $request->file('anh_bia')->store('covers', 'public');
        } elseif ($request->input('remove_cover') == '1') {
            if ($user->anh_bia) {
                Storage::disk('public')->delete($user->anh_bia);
            }
            $validated['anh_bia'] = null;

            // Xóa các bài đăng thông báo cập nhật ảnh bìa trước đó
            BaiViet::where('nguoi_dung_id', $user->id)
                ->where('noi_dung', 'vừa cập nhật ảnh bìa mới.')
                ->delete();
        }

        $user->update($validated);

        // Tạo bài viết thông báo cập nhật ảnh đại diện
        if ($request->hasFile('anh_dai_dien')) {
            $post = BaiViet::create([
                'nguoi_dung_id' => $user->id,
                'loai' => 'hinh_anh',
                'noi_dung' => 'vừa cập nhật ảnh đại diện mới.',
                'quyen_rieng_tu' => 'cong_khai',
            ]);

            MediaBaiViet::create([
                'bai_viet_id' => $post->id,
                'loai' => 'hinh_anh',
                'duong_dan' => $user->anh_dai_dien,
                'ngay_tao' => now(),
            ]);

            // Thông báo cho người theo dõi
            $this->notifyFollowers($user, $post);
        }

        // Tạo bài viết thông báo cập nhật ảnh bìa
        if ($request->hasFile('anh_bia')) {
            $post = BaiViet::create([
                'nguoi_dung_id' => $user->id,
                'loai' => 'hinh_anh',
                'noi_dung' => 'vừa cập nhật ảnh bìa mới.',
                'quyen_rieng_tu' => 'cong_khai',
            ]);

            MediaBaiViet::create([
                'bai_viet_id' => $post->id,
                'loai' => 'hinh_anh',
                'duong_dan' => $user->anh_bia,
                'ngay_tao' => now(),
            ]);

            // Thông báo cho người theo dõi
            $this->notifyFollowers($user, $post);
        }

        return redirect()
            ->route('profile')
            ->with('success', 'Đã cập nhật hồ sơ thành công.');
    }

    /**
     * Thông báo cho tất cả người theo dõi về bài viết mới
     */
    private function notifyFollowers($user, $post)
    {
        $followers = $user->followers()->where('trang_thai', 'da_chap_nhan')->get();
        foreach ($followers as $follower) {
            \App\Models\ThongBao::create([
                'nguoi_dung_id' => $follower->id,
                'nguoi_thuc_hien_id' => $user->id,
                'loai' => 'dang_bai',
                'bai_viet_id' => $post->id,
                'ngay_tao' => now(),
            ]);
        }
    }

    public function removeAvatar()
    {
        $user = auth()->user();
        if ($user->anh_dai_dien) {
            Storage::disk('public')->delete($user->anh_dai_dien);
            $user->update(['anh_dai_dien' => null]);
            return response()->json(['success' => true, 'message' => 'Đã xóa ảnh đại diện.']);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy ảnh đại diện để xóa.']);
    }

    public function removeCover()
    {
        $user = auth()->user();
        if ($user->anh_bia) {
            Storage::disk('public')->delete($user->anh_bia);
            $user->update(['anh_bia' => null]);
            return response()->json(['success' => true, 'message' => 'Đã xóa ảnh bìa.']);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy ảnh bìa để xóa.']);
    }

    /**
     * Gửi OTP đến email để xác minh tài khoản (lấy tích xanh).
     */
    public function sendVerifyEmailOtp()
    {
        $user = auth()->user();

        if ($user->da_xac_thuc) {
            return response()->json(['success' => false, 'message' => 'Email của bạn đã được xác minh.']);
        }

        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'    => $otpCode,
            'otp_het_han' => \Carbon\Carbon::now()->addMinutes(10),
        ]);

        \Illuminate\Support\Facades\Mail::to($user->email)
            ->send(new \App\Mail\OtpMail($otpCode, $user->ten_dang_nhap, 'verify_email'));

        return response()->json(['success' => true, 'message' => 'Mã OTP đã được gửi đến ' . $user->email]);
    }

    /**
     * Xác minh OTP để cấp tích xanh.
     */
    public function verifyEmailOtp(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.size'     => 'Mã OTP phải có đúng 6 chữ số.',
        ]);

        $user = auth()->user();

        if ($user->da_xac_thuc) {
            return response()->json(['success' => false, 'message' => 'Email đã được xác minh trước đó.']);
        }

        if (!$user->otp_het_han || \Carbon\Carbon::now()->greaterThan($user->otp_het_han)) {
            return response()->json(['success' => false, 'message' => 'Mã OTP đã hết hạn. Vui lòng gửi lại.']);
        }

        if ($request->otp !== $user->otp_code) {
            return response()->json(['success' => false, 'message' => 'Mã OTP không chính xác.']);
        }

        $user->update([
            'da_xac_thuc' => true,
            'otp_code'    => null,
            'otp_het_han' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Email đã được xác minh! Bạn nhận được tích xanh ✓']);
    }

    /**
     * (OAuth users only) Gửi OTP đến email hiện tại để xác nhận trước khi đổi sang email mới.
     */
    public function sendChangeEmailOtp(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();

        // Chặn tài khoản OAuth (Google/Facebook) đổi email
        if ($user->nha_cung_cap_oauth) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản ' . ucfirst($user->nha_cung_cap_oauth) . ' không thể thay đổi địa chỉ email.',
            ], 403);
        }

        $request->validate([
            'new_email' => ['required', 'email', 'unique:nguoi_dung,email'],
        ], [
            'new_email.required' => 'Vui lòng nhập email mới.',
            'new_email.email'    => 'Email không hợp lệ.',
            'new_email.unique'   => 'Email này đã được sử dụng.',
        ]);

        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'    => $otpCode,
            'otp_het_han' => \Carbon\Carbon::now()->addMinutes(10),
        ]);

        // Store pending new email in session
        session(['pending_new_email' => $request->new_email]);

        \Illuminate\Support\Facades\Mail::to($user->email)
            ->send(new \App\Mail\OtpMail($otpCode, $user->ten_dang_nhap, 'change_email'));

        return response()->json(['success' => true, 'message' => 'Mã OTP đã được gửi đến ' . $user->email]);
    }

    /**
     * Đổi email:
     * - Người dùng thường (email/password): xác thực bằng mật khẩu hiện tại
     * - Người dùng OAuth (Google/Facebook): xác thực bằng OTP
     */
    public function changeEmail(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();

        // Chặn tài khoản OAuth (Google/Facebook) đổi email
        if ($user->nha_cung_cap_oauth) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản ' . ucfirst($user->nha_cung_cap_oauth) . ' không thể thay đổi địa chỉ email.',
            ], 403);
        }

        // Validate new email
        $request->validate([
            'new_email' => ['required', 'email', 'unique:nguoi_dung,email,' . $user->id],
        ], [
            'new_email.required' => 'Vui lòng nhập email mới.',
            'new_email.email'    => 'Email không hợp lệ.',
            'new_email.unique'   => 'Email này đã được sử dụng.',
        ]);

        if ($user->nha_cung_cap_oauth) {
            // ── OAuth flow: verify OTP ──
            $request->validate([
                'otp' => ['required', 'string', 'size:6'],
            ], [
                'otp.required' => 'Vui lòng nhập mã OTP.',
                'otp.size'     => 'Mã OTP phải có 6 chữ số.',
            ]);

            if (!$user->otp_het_han || \Carbon\Carbon::now()->greaterThan($user->otp_het_han)) {
                return response()->json(['success' => false, 'message' => 'Mã OTP đã hết hạn. Vui lòng gửi lại.']);
            }

            if ($request->otp !== $user->otp_code) {
                return response()->json(['success' => false, 'message' => 'Mã OTP không chính xác.']);
            }

            $user->update([
                'email'       => $request->new_email,
                'da_xac_thuc' => false, // reset – cần xác minh email mới
                'otp_code'    => null,
                'otp_het_han' => null,
            ]);

            session()->forget('pending_new_email');

            return response()->json([
                'success' => true,
                'message' => 'Đổi email thành công! Vui lòng xác minh email mới để nhận lại tích xanh.',
            ]);
        } else {
            // ── Normal flow: verify password ──
            $request->validate([
                'password' => ['required', 'string'],
            ], [
                'password.required' => 'Vui lòng nhập mật khẩu.',
            ]);

            if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->mat_khau_hash)) {
                return response()->json(['success' => false, 'message' => 'Mật khẩu không chính xác.']);
            }

            $user->update([
                'email'       => $request->new_email,
                'da_xac_thuc' => false, // reset – cần xác minh email mới
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đổi email thành công! Vui lòng xác minh email mới để nhận lại tích xanh.',
            ]);
        }
    }

    public function toggleFollow(User $user)
    {
        $me = auth()->user();
        if ($me->id === $user->id) {
            return response()->json(['message' => 'Bạn không thể tự theo dõi chính mình.'], 400);
        }

        $trangThai = $user->quyen_rieng_tu === 'rieng_tu' ? 'cho_chap_nhan' : 'da_chap_nhan';
        
        $status = $me->following()->toggle([
            $user->id => [
                'trang_thai' => $trangThai,
                'ngay_tao' => now(),
            ]
        ]);
        
        $isFollowing = count($status['attached']) > 0;
        
        $isMutual = $isFollowing && 
            ($trangThai === 'da_chap_nhan') && 
            $me->followers()->where('nguoi_theo_doi_id', $user->id)->where('theo_doi.trang_thai', 'da_chap_nhan')->exists();

        if ($isFollowing) {
            // Tạo hoặc cập nhật thông báo cho người được theo dõi
            \App\Models\ThongBao::updateOrCreate(
                [
                    'nguoi_dung_id' => $user->id,
                    'nguoi_thuc_hien_id' => $me->id,
                    'loai' => 'theo_doi',
                ],
                [
                    'da_doc' => false,
                    'ngay_tao' => now(),
                ]
            );
        } else {
            // Xóa thông báo nếu bỏ theo dõi
            \App\Models\ThongBao::where([
                'nguoi_dung_id' => $user->id,
                'nguoi_thuc_hien_id' => $me->id,
                'loai' => 'theo_doi',
            ])->delete();
        }

        return response()->json([
            'is_following' => $isFollowing,
            'status' => $isFollowing ? $trangThai : null,
            'is_mutual' => $isMutual,
            'followers_count' => $user->followers()->count()
        ]);
    }

    public function acceptFollow(User $follower)
    {
        $me = auth()->user();
        
        $me->followers()->updateExistingPivot($follower->id, [
            'trang_thai' => 'da_chap_nhan'
        ]);

        // Cập nhật thông báo nếu có
        \App\Models\ThongBao::where([
            'nguoi_dung_id' => $me->id,
            'nguoi_thuc_hien_id' => $follower->id,
            'loai' => 'theo_doi',
        ])->update(['da_doc' => true]);
        
        return response()->json(['success' => true, 'message' => 'Đã chấp nhận yêu cầu theo dõi.']);
    }

    public function declineFollow(User $follower)
    {
        $me = auth()->user();
        
        $me->followers()->detach($follower->id);

        // Xóa thông báo nếu có
        \App\Models\ThongBao::where([
            'nguoi_dung_id' => $me->id,
            'nguoi_thuc_hien_id' => $follower->id,
            'loai' => 'theo_doi',
        ])->delete();
        
        return response()->json(['success' => true, 'message' => 'Đã từ chối yêu cầu theo dõi.']);
    }

    public function followers(string $username)
    {
        $user = User::query()
            ->where('ten_dang_nhap', $username)
            ->where('con_hoat_dong', true)
            ->withCount(['followers', 'following'])
            ->first();

        if (!$user) {
            return redirect()->route('home')->with('error', 'Không tìm thấy tài khoản người dùng.');
        }

        $connections = $user->followers()->paginate(20);

        return view('profile.connections', [
            'user' => $user,
            'connections' => $connections,
            'type' => 'followers',
        ]);
    }

    public function following(string $username)
    {
        $user = User::query()
            ->where('ten_dang_nhap', $username)
            ->where('con_hoat_dong', true)
            ->withCount(['followers', 'following'])
            ->first();

        if (!$user) {
            return redirect()->route('home')->with('error', 'Không tìm thấy tài khoản người dùng.');
        }

        $connections = $user->following()->paginate(20);

        return view('profile.connections', [
            'user' => $user,
            'connections' => $connections,
            'type' => 'following',
        ]);
    }

    public function sendActionOtp(Request $request)
    {
        $user = auth()->user();

        if (empty($user->nha_cung_cap_oauth)) {
            return response()->json(['success' => false, 'message' => 'Tài khoản này không sử dụng OTP cho chức năng này.']);
        }

        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'    => $otpCode,
            'otp_het_han' => \Carbon\Carbon::now()->addMinutes(5),
        ]);

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otpCode, $user->ten_dang_nhap, 'account_action'));

        return response()->json(['success' => true, 'message' => 'Mã OTP đã được gửi đến email của bạn.']);
    }

    public function deactivate(Request $request)
    {
        $user = auth()->user();

        if (empty($user->nha_cung_cap_oauth)) {
            $request->validate([
                'password_deactivate' => ['required', 'string'],
            ], [
                'password_deactivate.required' => 'Vui lòng nhập mật khẩu.',
            ]);

            if (!\Illuminate\Support\Facades\Hash::check($request->password_deactivate, $user->mat_khau_hash)) {
                return back()->withErrors(['password_deactivate' => 'Mật khẩu không chính xác.']);
            }
        } else {
            $request->validate([
                'otp_deactivate' => ['required', 'string', 'size:6'],
            ], [
                'otp_deactivate.required' => 'Vui lòng nhập mã OTP.',
                'otp_deactivate.size' => 'Mã OTP phải có 6 chữ số.',
            ]);

            if ($request->otp_deactivate !== $user->otp_code || !$user->otp_het_han || \Carbon\Carbon::now()->greaterThan($user->otp_het_han)) {
                return back()->withErrors(['otp_deactivate' => 'Mã OTP không hợp lệ hoặc đã hết hạn.']);
            }

            // Xóa OTP sau khi dùng
            $user->update(['otp_code' => null, 'otp_het_han' => null]);
        }

        // Chỉ ẩn (tạm khóa) người dùng bằng cách set con_hoat_dong = false
        $user->update(['con_hoat_dong' => false]);

        auth()->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Tài khoản của bạn đã được vô hiệu hóa. Bạn có thể đăng nhập lại để khôi phục.');
    }

    public function destroy(Request $request)
    {
        $user = auth()->user();

        if (empty($user->nha_cung_cap_oauth)) {
            $request->validate([
                'password_delete' => ['required', 'string'],
            ], [
                'password_delete.required' => 'Vui lòng nhập mật khẩu để xác nhận.',
            ]);

            if (!\Illuminate\Support\Facades\Hash::check($request->password_delete, $user->mat_khau_hash)) {
                return back()->withErrors(['password_delete' => 'Mật khẩu không chính xác.']);
            }
        } else {
            $request->validate([
                'otp_delete' => ['required', 'string', 'size:6'],
            ], [
                'otp_delete.required' => 'Vui lòng nhập mã OTP.',
                'otp_delete.size' => 'Mã OTP phải có 6 chữ số.',
            ]);

            if ($request->otp_delete !== $user->otp_code || !$user->otp_het_han || \Carbon\Carbon::now()->greaterThan($user->otp_het_han)) {
                return back()->withErrors(['otp_delete' => 'Mã OTP không hợp lệ hoặc đã hết hạn.']);
            }

            // Xóa OTP sau khi dùng
            $user->update(['otp_code' => null, 'otp_het_han' => null]);
        }

        auth()->logout();
        
        // Permanent delete
        $user->forceDelete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Tài khoản của bạn đã được xóa vĩnh viễn khỏi hệ thống.');
    }

    public function blockUser(Request $request, User $user)
    {
        $currentUser = auth()->user();
        if ($currentUser->id === $user->id) {
            return response()->json(['error' => 'Bạn không thể tự chặn chính mình.'], 400);
        }

        // Tạo bản ghi chặn
        \App\Models\Chan::firstOrCreate([
            'nguoi_chan_id' => $currentUser->id,
            'nguoi_bi_chan_id' => $user->id,
        ]);

        // Hủy mọi mối quan hệ theo dõi giữa hai người
        \DB::table('theo_doi')->where(function ($query) use ($currentUser, $user) {
            $query->where('nguoi_theo_doi_id', $currentUser->id)
                  ->where('nguoi_duoc_theo_doi_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('nguoi_theo_doi_id', $user->id)
                  ->where('nguoi_duoc_theo_doi_id', $currentUser->id);
        })->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã chặn người dùng này thành công.',
                'redirect_url' => route('profile')
            ]);
        }

        return redirect()->route('profile')->with('success', 'Đã chặn người dùng thành công.');
    }

    public function unblockUser(Request $request, User $user)
    {
        $currentUser = auth()->user();

        \App\Models\Chan::where('nguoi_chan_id', $currentUser->id)
            ->where('nguoi_bi_chan_id', $user->id)
            ->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã bỏ chặn người dùng thành công.'
            ]);
        }

        return back()->with('success', 'Đã bỏ chặn người dùng thành công.');
    }
}
