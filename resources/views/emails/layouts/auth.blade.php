<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>

<body style="margin: 0; padding: 0; background-color: #F9F8F6; color: #0C0C0C; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.6;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
        style="width: 100%; background-color: #F9F8F6; padding: 32px 16px;">

        <tr>
            <td align="center">

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                    style="max-width: 560px; width: 100%; background-color: #FFFFFF; border: 1px solid #E8E6E2; border-collapse: collapse;">

                    {{-- Header --}}
                    <tr>
                        <td style="padding: 28px 32px 24px; background-color: #F9F8F6; text-align: center;">

                            <div style="font-size: 25px; font-weight: 800; line-height: 1.3; letter-spacing: 1px; color: #0C0C0C;">
                                Verin Group
                            </div>

                            <div style="font-size: 13px; color: #555555; margin-top: 6px;">
                                Secure account verification
                            </div>

                        </td>
                    </tr>

                    {{-- Main Content --}}
                    <tr>
                        <td style="padding: 32px; background-color: #FFFFFF;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 22px 32px; background-color: #F9F8F6; border-top: 1px solid #E8E6E2; color: #555555; font-size: 12px; line-height: 1.8; text-align: center;">

                            <p style="margin: 0 0 8px;">
                                If you did not request this email, you can safely ignore it.
                            </p>

                            <p style="margin: 0;">
                                <a href="https://veringroup.com"
                                    target="_blank"
                                    style="color: #0C0C0C; font-weight: 700; text-decoration: underline;">
                                    veringroup.com
                                </a>
                            </p>

                            <p style="margin: 12px 0 0; color: #777777; font-size: 11px;">
                                &copy; {{ date('Y') }} Verin Group. All rights reserved.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>

    </table>

</body>
</html>