<!DOCTYPE html>
<html>
<head>
    <style>
        .email-container { font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; }
        .header { text-align: center; color: #0ea5e9; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
        .otp-box { background: #f1f5f9; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #1e293b; border-radius: 8px; margin: 20px 0; }
        .footer { font-size: 12px; color: #64748b; text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">NHOMJ</div>
        <p>Xin chào <strong>{{ $userName }}</strong>,</p>
        @if($type === 'register')
            <p>Cảm ơn bạn đã đăng ký tài khoản. Vui lòng sử dụng mã OTP dưới đây để xác thực địa chỉ email của bạn:</p>
        @else
            <p>Chúng tôi đã nhận được yêu cầu khôi phục mật khẩu từ bạn. Vui lòng sử dụng mã OTP dưới đây để tiếp tục:</p>
        @endif
        
        <div class="otp-box">
            {{ $otp }}
        </div>
        
        @if($type === 'register')
            <p>Mã này có hiệu lực trong vòng <strong>10 phút</strong>. Nếu bạn không thực hiện đăng ký này, bạn có thể bỏ qua email này.</p>
        @else
            <p>Mã này có hiệu lực trong vòng <strong>5 phút</strong>. Nếu bạn không yêu cầu thay đổi này, bạn có thể bỏ qua email này.</p>
        @endif
        
        <div class="footer">
            Đây là email tự động, vui lòng không phản hồi.<br>
            &copy; {{ date('Y') }} NHOMJ Team.
        </div>
    </div>
</body>
</html>