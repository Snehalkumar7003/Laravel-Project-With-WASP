@extends('emails.layouts.mail')
@section('content')
<h2>Password Changed Successfully</h2>
<p>Hello {{ $user->username }},</p>
<p>
Your account password was changed successfully.
</p>
<table cellpadding="6">
    <tr>
        <td><strong>Date</strong></td>
        <td>{{ now()->format('d M Y H:i') }}</td>
    </tr>
    <tr>
        <td><strong>IP Address</strong></td>
        <td>{{ $ipAddress }}</td>
    </tr>
    <tr>
        <td><strong>Device</strong></td>
        <td>{{ $userAgent }}</td>
    </tr>
</table>
<p>
    If you did not make this change, please contact your administrator immediately.
</p>

@endsection