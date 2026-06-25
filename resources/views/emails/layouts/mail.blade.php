<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f4f7fb">
<tr>
<td align="center">

<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;margin:30px 0;">

    <!-- Header -->
    <tr>
        <td style="background:#0d6efd;padding:30px;text-align:center;">
            <img src="{{ asset('img/logo.png') }}" height="50">
            <h2 style="color:#fff;margin-top:15px;">
                {{ config('app.name') }}
            </h2>
        </td>
    </tr>

    <!-- Body -->
    <tr>
        <td style="padding:40px;">
            @yield('content')
        </td>
    </tr>

    <!-- Security Notice -->
    <tr>
        <td style="background:#fff8e1;padding:20px;font-size:13px;color:#555;">
            🔒 This is an automated security email.
            Please do not reply to this message.
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="background:#f7f7f7;padding:25px;text-align:center;font-size:12px;color:#999;">
            © {{ date('Y') }} {{ config('app.name') }}<br>
            All Rights Reserved.
        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>