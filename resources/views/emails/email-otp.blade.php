<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Code</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px; background-color:#ffffff; border-radius:16px; overflow:hidden; border:1px solid #e2e8f0;">
                    <tr>
                        <td style="background-color:#006847; padding:24px; text-align:center;">
                            <span style="color:#ffffff; font-size:20px; font-weight:bold; letter-spacing:0.5px;">HeavenlyMatch</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 28px;">
                            <h1 style="margin:0 0 12px; font-size:18px; color:#0f172a;">Verify your email address</h1>
                            <p style="margin:0 0 24px; font-size:14px; line-height:1.6; color:#475569;">
                                Use the 6-digit code below to verify your email and continue your registration.
                            </p>

                            <div style="text-align:center; margin:0 0 24px;">
                                <span style="display:inline-block; background-color:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; font-size:32px; font-weight:bold; letter-spacing:8px; padding:16px 24px; border-radius:12px;">
                                    {{ $code }}
                                </span>
                            </div>

                            <p style="margin:0 0 8px; font-size:13px; color:#475569;">
                                This code expires in <strong>{{ $expiryMinutes }} minutes</strong>.
                            </p>
                            <p style="margin:0; font-size:13px; color:#b91c1c;">
                                ⚠ Do not share this code with anyone. HeavenlyMatch staff will never ask for it.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 28px; border-top:1px solid #f1f5f9; text-align:center;">
                            <p style="margin:0; font-size:11px; color:#94a3b8;">
                                If you did not request this, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
