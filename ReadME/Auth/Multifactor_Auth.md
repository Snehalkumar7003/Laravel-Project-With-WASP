# Email OTP Verification Module

## Overview

The Email OTP Verification module provides an additional authentication layer after successful username and password validation.

The module follows OWASP recommendations by requiring users to verify a One-Time Password (OTP) sent to their registered email address before creating an authenticated session.

Features:

- Email Based Two-Factor Authentication (2FA)
- RSA Encrypted OTP Submission
- Session-Based OTP Verification
- OTP Expiration
- OTP Attempt Limiting
- OTP Resend Protection
- Audit Logging
- Automatic Logout on OTP Failure
- AJAX Verification
- CSRF Protection

---

# Authentication Flow

```text
Login
    │
    ▼
Validate Username & Password
    │
    ▼
Generate OTP
    │
    ▼
Store Hashed OTP
    │
    ▼
Email OTP
    │
    ▼
Temporary Login Session
    │
    ▼
OTP Verification Screen
    │
    ▼
Verify OTP
    │
    ▼
Create Authenticated Session
    │
    ▼
Dashboard
```

---

# Folder Structure

```text
app/
│
├── Http/
│   └── Controllers/
│       └── Auth/
│           └── OtpController.php
│
├── Mail/
│   └── LoginOtpMail.php
│
├── Models/
│   ├── MstUserModel.php
│   └── TblLogModel.php
│
├── Services/
│   └── Security/
│       └── RSAService.php
│
resources/
│
├── views/
│   ├── auth/
│   │   └── otp.blade.php
│   │
│   └── emails/
│       └── login-otp.blade.php
│
public/
│
└── assets/
    └── plugins/
        └── js/
            └── auth/
                └── otp.js
```

---

# Security Workflow

```text
Browser
     │
     ▼
Enter OTP
     │
     ▼
RSA Encrypt OTP
     │
     ▼
AJAX Request
     │
     ▼
Laravel Controller
     │
     ▼
RSA Decrypt OTP
     │
     ▼
Validate OTP
     │
     ▼
Verify Expiry
     │
     ▼
Verify Attempts
     │
     ▼
Create Session
     │
     ▼
Dashboard
```

---

# OTP Configuration

| Setting | Value |
|---------|-------|
| OTP Length | 6 Digits |
| OTP Validity | 5 Minutes |
| Max Verification Attempts | 3 |
| Resend Interval | 60 Seconds |
| Max Resends | 5 Per Hour |

---

# OTP Verification Process

```text
Generate OTP
      │
      ▼
Hash OTP
      │
      ▼
Store Database
      │
      ▼
Send Email
      │
      ▼
User Enters OTP
      │
      ▼
Verify Hash
      │
      ▼
Success
```

---

# OTP Expiry

```text
OTP Generated
      │
      ▼
Valid For 5 Minutes
      │
      ▼
Expired
      │
      ▼
User Must Request New OTP
```

---

# OTP Attempt Protection

Maximum verification attempts:

```text
Attempt 1
     │
     ▼
2 Attempts Remaining

Attempt 2
     │
     ▼
1 Attempt Remaining

Attempt 3
     │
     ▼
OTP Invalidated
Session Destroyed
Redirect Login
```

---

# OTP Resend Protection

The system prevents OTP abuse.

Rules:

```text
Maximum 1 Resend Every 60 Seconds

Maximum 5 Resends Per Hour

Each Resend Generates New OTP

Old OTP Immediately Invalid
```

Workflow

```text
Click Resend
      │
      ▼
Rate Limit Check
      │
      ▼
Generate New OTP
      │
      ▼
Store Hash
      │
      ▼
Email OTP
      │
      ▼
Restart Timer
```

---

# Session Flow

Temporary Session

```text
pending_login

pending_user_id

pending_device_fingerprint

pending_password_change
```

Authenticated Session

```text
user_id

username

email

role_id

role_name

is_logged_in

must_change_password
```

Temporary session is destroyed immediately after successful verification.

---

# Security Controls

Implemented

✓ RSA Encryption

✓ CSRF Protection

✓ OTP Hashing

✓ OTP Expiration

✓ OTP Attempt Limiting

✓ OTP Resend Rate Limiting

✓ Device Fingerprint

✓ Session Regeneration

✓ Single Device Login

✓ Audit Logging

✓ AJAX Authentication

✓ Automatic Logout

---

# Automatic Logout

The user is automatically logged out when:

```text
Maximum OTP Attempts Exceeded

OR

OTP Session Expired

OR

User No Longer Exists

OR

Pending Login Session Invalid
```

Workflow

```text
Clear OTP

↓

Destroy Pending Session

↓

Invalidate Session

↓

Regenerate CSRF

↓

Redirect Login
```

---

# Audit Logs

The following events are stored.

```text
LOGIN

OTP_SENT

OTP_RESEND

OTP_VERIFIED

OTP_FAILED

OTP_BLOCKED

LOGOUT
```

Stored In

```text
tbl_logs
```

---

# AJAX Responses

OTP Sent

```json
{
    "success":1,
    "message":"OTP sent successfully."
}
```

OTP Verified

```json
{
    "success":1,
    "redirect":"/app/main-dashboard"
}
```

Invalid OTP

```json
{
    "success":0,
    "message":"Invalid OTP. 2 attempt(s) remaining."
}
```

Maximum Attempts

```json
{
    "success":0,
    "logout":true,
    "redirect":"/login",
    "message":"Maximum OTP attempts exceeded. Please login again."
}
```

---

# Database Fields

```text
mst_users

otp

otp_expiry

otp_attempts

otp_last_sent_at

otp_resend_count
```

---

# Middleware

Guest Middleware

```text
Login Page

Forgot Password
```

OTP Middleware

```text
OTP Screen

OTP Verify

OTP Resend
```

Session Security Middleware

```text
Dashboard

Profile

Settings

Change Password
```

---

# Enterprise Security Features

Implemented

✓ Email Two-Factor Authentication

✓ RSA Encrypted OTP

✓ Hashed OTP Storage

✓ Device Fingerprint Binding

✓ Single Device Login

✓ Password Expiry Support

✓ Password History

✓ Audit Logging

✓ Automatic Session Cleanup

✓ Rate Limiting

✓ Session Regeneration

---

# Future Enhancements

- Microsoft Authenticator Support
- Backup Recovery Codes
- SMS OTP
- Push Notification Approval
- Trusted Devices
- Adaptive Authentication
- Risk-Based Authentication

---

# Developer Information

```text
Developer  : Snehal Vasava
Company    : Pro-TEAM Solutions Pvt. Ltd.
Framework  : Laravel 12
Frontend   : Bootstrap 5 + jQuery
Database   : MySQL
Security   : RSA 4096 + Email OTP + CSRF
Version    : 1.0.0
```