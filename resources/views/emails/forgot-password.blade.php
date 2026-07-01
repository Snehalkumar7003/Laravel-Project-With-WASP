@extends('emails.layouts.mail')

@section('content')

<h2 style="margin:0 0 20px;color:#1f2937;">
    Reset Your Password
</h2>

<p style="margin-bottom:15px;">
    Hello <strong>{{ $user->username }}</strong>,
</p>

<p style="margin-bottom:15px;">
    We received a request to reset your account password.
    If you made this request, click the button below to create a new password.
</p>

<div style="text-align:center;margin:35px 0;">

    <a href="{{ $resetLink }}"
       style="
            background:#2563eb;
            color:#ffffff;
            text-decoration:none;
            padding:14px 30px;
            border-radius:6px;
            display:inline-block;
            font-weight:600;">
        Reset Password
    </a>

</div>

<p style="margin-bottom:15px;">
    This password reset link will expire in
    <strong>{{ $expiry }} minutes</strong>.
</p>

<p style="margin-bottom:15px;">
    If you did not request a password reset, you can safely ignore this email.
    Your account remains secure.
</p>

<hr style="margin:30px 0;border:none;border-top:1px solid #e5e7eb;">

<p style="font-size:13px;color:#6b7280;margin:0;">
    If the button above does not work, copy and paste the following URL into your browser:
</p>

<p style="word-break:break-all;font-size:13px;margin-top:10px;">
    <a href="{{ $resetLink }}">
        {{ $resetLink }}
    </a>
</p>
@endsection
