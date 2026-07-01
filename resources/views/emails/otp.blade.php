@extends('emails.layouts.mail')

@section('content')

<h2>Email Verification</h2>

<p>Hello {{ $user->username }},</p>

<p>
Use the following One-Time Password (OTP) to complete your login.
</p>

<div style="text-align:center;margin:30px 0;">
    <span style="
        font-size:34px;
        font-weight:bold;
        letter-spacing:8px;
        color:#0d6efd;">
        {{ $otp }}
    </span>
</div>
<p>
This OTP is valid for <strong>5 minutes</strong>.
</p>
<p>
If you did not initiate this login, please ignore this email.
</p>

@endsection
