<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã xác thực OTP - NHOMJ</title>
</head>
<body style="margin:0; padding:0; background-color:#0a0e1a; font-family:'Segoe UI',Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#0a0e1a; padding:40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellspacing="0" cellpadding="0" style="background-color:#141c2e; border-radius:16px; border:1px solid rgba(125,211,252,0.15); overflow:hidden;">
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, rgba(125,211,252,0.15), rgba(200,160,240,0.1)); padding:32px 40px; text-align:center;">
                            <h1 style="margin:0; color:#7dd3fc; font-size:28px; font-weight:800; letter-spacing:-0.5px;">NHOMJ</h1>
                            <p style="margin:8px 0 0; color:#a0b4c4; font-size:13px; text-transform:uppercase; letter-spacing:2px;">Xác thực tài khoản</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:40px;">
                            <p style="margin:0 0 8px; color:#e0e8f0; font-size:18px; font-weight:600;">
                                Xin chào {{ $tenDangNhap }},
                            </p>
                            <p style="margin:0 0 28px; color:#a0b4c4; font-size:14px; line-height:1.6;">
                                Cảm ơn bạn đã đăng ký tài khoản NHOMJ! Vui lòng sử dụng mã OTP bên dưới để xác thực email của bạn.
                            </p>

                            {{-- OTP Code --}}
                            <div style="text-align:center; margin:0 0 28px;">
                                <div style="display:inline-block; background:rgba(125,211,252,0.08); border:2px dashed rgba(125,211,252,0.3); border-radius:12px; padding:20px 48px;">
                                    <span style="font-size:36px; font-weight:800; letter-spacing:12px; color:#7dd3fc;">{{ $otpCode }}</span>
                                </div>
                            </div>

                            <p style="margin:0 0 8px; color:#a0b4c4; font-size:13px; line-height:1.6; text-align:center;">
                                Mã này sẽ hết hạn sau <strong style="color:#e0e8f0;">10 phút</strong>.
                            </p>
                            <p style="margin:0; color:#a0b4c4; font-size:13px; line-height:1.6; text-align:center;">
                                Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:24px 40px; border-top:1px solid rgba(125,211,252,0.08); text-align:center;">
                            <p style="margin:0; color:#4a6070; font-size:11px;">
                                © {{ date('Y') }} NHOMJ. Mọi quyền được bảo lưu.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
