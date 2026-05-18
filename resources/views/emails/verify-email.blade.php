<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your HeavenlyMatch email</title>
</head>
<body style="margin:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8fafc;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border-radius:24px;overflow:hidden;border:1px solid #f1f5f9;box-shadow:0 20px 60px rgba(52,16,68,0.12);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#7a1b7e,#e12f83);padding:34px 28px;text-align:center;color:#ffffff;">
                            <div style="font-size:28px;font-weight:800;line-height:1.2;">HeavenlyMatch</div>
                            <div style="margin-top:8px;font-size:14px;opacity:.85;">Secure email verification</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 28px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;line-height:1.3;color:#0f172a;">Hello, {{ $name }}</h1>
                            <p style="margin:12px 0 0;font-size:15px;line-height:1.7;color:#475569;">Use this 6-digit code to verify your email address and continue your matrimony account setup.</p>

                            <div style="display:inline-block;margin:26px 0 20px;padding:16px 24px;border-radius:16px;background:#fff5fb;border:1px dashed #e12f83;color:#7a1b7e;font-size:34px;font-weight:800;letter-spacing:8px;">{{ $code }}</div>

                            @if(!empty($verifyUrl))
                                <div style="margin-top:12px;">
                                    <a href="{{ $verifyUrl }}" style="display:inline-block;background:#e12f83;color:#ffffff;text-decoration:none;border-radius:14px;padding:14px 22px;font-size:15px;font-weight:800;">Verify email now</a>
                                </div>
                            @endif

                            <p style="margin:24px 0 0;font-size:13px;line-height:1.7;color:#64748b;">This code and link expire after 24 hours. If you did not create an account, you can safely ignore this email.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 28px;text-align:center;background:#f8fafc;color:#94a3b8;font-size:12px;">
                            &copy; {{ now()->year }} HeavenlyMatch. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
