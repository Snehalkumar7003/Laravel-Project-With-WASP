# Change Password Module

## Overview

The Change Password module provides a secure mechanism for authenticated users to update their account password while following OWASP security guidelines.

The module ensures:

- Current password verification
- RSA encrypted password transmission
- Strong password policy
- Password history validation
- Password expiry support
- First login password change
- Audit logging
- Email notification
- Forced logout after password change

---

# Security Workflow

```text
Authenticated User
        │
        ▼
Validate Form
        │
        ▼
RSA Decrypt Passwords
        │
        ▼
Validate Current Password
        │
        ▼
Validate Password Policy
        │
        ▼
Check Password History
        │
        ▼
Update Password
        │
        ▼
Insert Password History
        │
        ▼
Write Audit Log
        │
        ▼
Send Email Notification
        │
        ▼
Logout User
        │
        ▼
Redirect Login
```

---

# Folder Structure

```text
app
├── Http
│   ├── Controllers
│   │    └── Auth
│   │       └── ChangePasswordController.php
│   └── Middleware
│        └── OtpMiddleware.php
├── Mail
│   └── PasswordChangedMail.php
│
├── Models
│   ├── MstUserModel.php
│   ├── TblLogModel.php
│   └── auth
│       └── TblPasswordHistoryModel.php
│
├── Services
│   ├── Mail
│   │   └── MailService.php
│   └── Security
│       └── RSAService.php
│
resources
│
├── views
│   ├── auth
│   │   └── change-password.blade.php
│   │
│   └── emails
│       └── password-changed.blade.php
│
public
│
└── assets
    └── plugins
        └── js
            └── auth
                └── change-password.js
```

---

# Features

Implemented

- RSA 4096 Encryption
- Current Password Validation
- Strong Password Validation
- Password Confirmation
- Password History Validation
- Password Expiry Support
- First Login Password Change
- Password Strength Meter
- Live Password Policy
- Device Fingerprint
- AJAX Request
- CSRF Protection
- Audit Logging
- Email Notification
- Automatic Logout
- Session Invalidation

---

# Password Policy

Password must contain:

```text
✓ Minimum 8 Characters

✓ One Uppercase Letter

✓ One Lowercase Letter

✓ One Numeric Character

✓ One Special Character
```

Example

```text
Admin@123
```

---

# Password History

The system stores password history.

Rules

```text
Current Password

↓

New Password

↓

Compare With Last 3 Passwords

↓

Reject If Match Found

↓

Store New Password Hash
```

The user cannot reuse the last **3 passwords**.

---

# Password Expiry

Password expires every **90 days**.

When expired:

```text
Login

↓

Password Expired

↓

Redirect Change Password

↓

Dashboard Access Blocked
```

---

# First Login Flow

```text
User Created

↓

is_first_login = 1

↓

Login

↓

Redirect Change Password

↓

Update Password

↓

is_first_login = 0

↓

Dashboard
```

---

# Email Notification

After successful password change an email notification is sent.

Example

```text
Subject

Password Changed Successfully

Dear User,

Your account password has been changed successfully.

If you did not perform this action,
please contact the administrator immediately.
```

---

# Database Tables

```text
mst_users

tbl_password_history

tbl_logs

sessions
```

---

# Audit Logs

The following event is stored.

```text
PASSWORD_CHANGED
```

Example

```text
Module

AUTH

Action

PASSWORD_CHANGED

IP Address

192.168.1.10

Remarks

Password changed successfully.
```

---

# Session Security

After password change

```text
Update Password

↓

Commit Transaction

↓

Send Email

↓

Invalidate Session

↓

Regenerate CSRF

↓

Redirect Login
```

This forces the user to authenticate again using the new password.

---

# Validation Flow

```text
Validate Form

↓

Decrypt Passwords

↓

Current Password Check

↓

Password Policy Check

↓

Confirm Password Check

↓

Password History Check

↓

Begin Transaction

↓

Update Password

↓

Insert Password History

↓

Write Audit Log

↓

Commit Transaction

↓

Send Email

↓

Logout User
```

---

# AJAX Response

Success

```json
{
    "success":1,
    "message":"Password changed successfully.",
    "redirect":"/login"
}
```

Validation Error

```json
{
    "success":0,
    "message":"Current password is incorrect."
}
```

Password History

```json
{
    "success":0,
    "message":"You cannot reuse your last 3 passwords."
}
```

---

# OWASP Controls

Implemented

✓ RSA Encryption

✓ CSRF Protection

✓ Password Hashing

✓ Password History

✓ Password Expiry

✓ Session Invalidation

✓ Session Regeneration

✓ SQL Injection Protection

✓ XSS Protection

✓ Audit Logging

✓ Device Fingerprint

✓ Strong Password Policy

---

# Future Enhancements

- OTP Verification Before Password Change
- Multi-Factor Authentication (MFA)
- Password Breach Detection
- Password Dictionary Validation
- Password Complexity Configuration
- Admin Password Reset Workflow
- Security Event Notification
- Concurrent Session Revocation

---

# Developer Information

```text
Developer  : Snehal Vasava
Company    : Pro-TEAM Solutions Pvt. Ltd.
Framework  : Laravel 12
Frontend   : Bootstrap 5 + jQuery
Backend    : Laravel
Database   : MySQL
Security   : RSA 4096 + Bcrypt + CSRF
Version    : 1.0.0
```