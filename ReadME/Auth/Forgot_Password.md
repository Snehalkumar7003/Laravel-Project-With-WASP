# Forgot Password & Reset Password Module

## Overview

The Forgot Password module allows users to securely reset their password through an email verification link.

The module is designed following OWASP security recommendations and enterprise authentication standards.

---

# Features

- Secure password reset link
- Reset link expires after 10 minutes
- Maximum 3 reset requests within 24 hours
- Only one active reset link at a time
- Reset link can be used only once
- Secure random token generation
- Hashed token storage
- Password policy validation
- Prevent reuse of last 3 passwords
- Password history maintenance
- Password changed notification email
- Email logging
- Audit logging
- Force logout from all active sessions after password reset

---

# Flow

Forgot Password

↓

Enter Registered Email

↓

Validate Email

↓

Check User Exists

↓

Check Reset Request Limit (3 per 24 Hours)

↓

Check Existing Active Link

↓

Generate Secure Token

↓

Hash Token

↓

Store Reset Request

↓

Send Reset Link Email

↓

User Opens Email

↓

Click Reset Link

↓

Validate Token

↓

Show Reset Password Screen

↓

Validate Password Policy

↓

Check Last 3 Password History

↓

Update Password

↓

Update Password History

↓

Invalidate Reset Link

↓

Send Password Changed Email

↓

Redirect Login

---

# Security Features

## Password Reset Link

- Secure random token
- Token stored as hash
- Expires after 10 minutes
- One-time use only

---

## Request Limiting

Maximum:

- 3 reset requests within 24 hours

If limit exceeds:

```
You have reached the maximum password reset requests.
Please try again after 24 hours.
```

---

## Active Reset Link

Only one active reset link is allowed.

If another request is made before expiry:

```
A password reset link has already been sent.
Please wait before requesting another.
```

---

## Password Policy

Password must contain

- Minimum 8 characters
- One uppercase letter
- One lowercase letter
- One number
- One special character

---

## Password History

Users cannot reuse their last 3 passwords.

---

## Session Security

After successful password reset

- Existing login session removed
- Session ID cleared
- User must login again

---

# Database Tables

## tbl_password_resets

Stores

- reset token
- expiry
- IP Address
- User Agent
- Used timestamp

---

## tbl_password_history

Stores

Last 3 passwords

---

## tbl_email_logs

Stores

- email recipient
- template
- body
- provider
- delivery status

---

## tbl_logs

Audit events

- PASSWORD_RESET_REQUEST
- PASSWORD_RESET
- EMAIL_SENT
- EMAIL_FAILED

---

# Controllers

ForgotPasswordController

Functions

```
index()

sendLink()

resetForm()

reset()
```

---

# Email Templates

Forgot Password

```
emails/forgot-password.blade.php
```

Password Changed

```
emails/password-changed.blade.php
```

Master Layout

```
emails/layouts/master.blade.php
```

---

# Mail Classes

```
BaseMail

ForgotPasswordMail

PasswordChangedMail
```

---

# Mail Service

All emails are sent through

```
MailService
```

Responsibilities

- Create Email Log
- Send Email
- Update Status
- Audit Log
- SMTP Integration
- Future Queue Support

---

# Routes

```
GET     /forgot-password

POST    /forgot-password

GET     /reset-password/{token}

POST    /reset-password
```

---

# Password Reset Validation

The reset link is rejected when

- Token not found
- Token expired
- Token already used
- User deleted
- User inactive

---

# Email Notification

After successful password reset

An email notification is sent containing

- Username
- Date & Time
- IP Address
- Browser Information

---

# Audit Log Actions

```
PASSWORD_RESET_REQUEST

PASSWORD_RESET

EMAIL_SENT

EMAIL_FAILED
```

---

# Error Messages

Invalid email

```
Please enter a valid email address.
```

Expired Link

```
This password reset link has expired.
```

Invalid Link

```
Invalid password reset link.
```

Password Reused

```
You cannot reuse your last 3 passwords.
```

Reset Limit

```
Maximum password reset requests exceeded.
```

---

# Future Improvements

- Background Email Queue
- SMS Reset
- Multi-Factor Password Reset
- Security Questions
- Account Recovery Approval
- Geo-location Tracking
- Device Recognition

---

# Technologies

Laravel 12

Bootstrap 5

jQuery

RSA Encryption

SMTP Mail Service

MySQL

OWASP Security Standards

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