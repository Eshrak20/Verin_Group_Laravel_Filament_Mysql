<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="margin: 0; padding: 0; background: #f4f7fb; color: #1f2937; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #f4f7fb; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 560px; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
                    <tr>
                        <td style="padding: 28px 32px 18px; background: #111827; color: #ffffff;">
                            <div style="font-size: 18px; font-weight: 700; line-height: 1.3;">{{ config('app.name') }}</div>
                            <div style="font-size: 13px; color: #cbd5e1; margin-top: 6px;">Secure account verification</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px;">
                            @yield('content')
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 32px; background: #f9fafb; color: #6b7280; font-size: 12px; line-height: 1.6;">
                            If you did not request this email, you can safely ignore it.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>