# Login Process

## Overview

The application implements a secure enterprise-grade authentication mechanism following OWASP security guidelines.

Authentication uses:

* RSA-4096 Client-side Encryption
* CSRF Protection
* AJAX Authentication
* Password Hashing (Bcrypt)
* Session Regeneration
* Session Fixation Protection
* Single Active Session (One Device Login)
* Login Attempt Tracking
* Account Lock Protection
* Login OTP Verification
* Forgot Password OTP
* Password History Validation
* Password Expiry Policy
* Email Notification Service
* Audit Logging
* Device & IP Tracking
* Role Based Access Control (RBAC)

---

# Authentication Workflow/Security Architecture

User
 │
 ▼
Open Login Page
 │
 ▼
Load Public RSA Key
 │
 ▼
Validate Form
 │
 ▼
Encrypt Username & Password
 │
 ▼
AJAX Login Request
 │
 ▼
Laravel Login Controller
 │
 ▼
Validate CSRF Token
 │
 ▼
Decrypt Credentials (RSA)
 │
 ▼
Validate Request
 │
 ▼
Check Account Status
 │
 ▼
Check Failed Login Attempts
 │
 ▼
Account Locked?
 ├── Yes
 │      │
 │      ▼
 │   Reject Login
 │
 └── No
        │
        ▼
Find User
        │
        ▼
Verify Password
        │
        ▼
Password Correct?
 ├── No
 │      │
 │      ▼
 │ Record Failed Attempt
 │
 └── Yes
        │
        ▼
Password Expired?
 ├── Yes
 │      │
 │      ▼
 │ Force Password Change
 │
 └── No
        │
        ▼
Generate Login OTP
        │
        ▼
Send OTP Email
        │
        ▼
User Verifies OTP
        │
        ▼
Invalidate Previous Sessions
        │
        ▼
Regenerate Session
        │
        ▼
Store User Session
        │
        ▼
Update Last Login
        │
        ▼
Write Audit Log
        │
        ▼
Dashboard

---
# Email Notification Flow
Event
 │
 ▼
MailService
 │
 ▼
Create Email Log
(PENDING)
 │
 ▼
Send Email
 │
 ▼
Success?
 ├── Yes
 │      │
 │      ▼
 │ Update Log → SENT
 │
 └── No
        │
        ▼
Update Log → FAILED
Store Exception Message


---

# Login Structure

app/
├── Http/
│   ├── Controllers/   
│   └── Middleware/
│
├── Mail/
│   ├── BaseMail.php
│   ├── LoginOtpMail.php
│   ├── ForgotPasswordOtpMail.php
│   ├── WelcomeMail.php
│   ├── PasswordChangedMail.php
│   └── AccountLockedMail.php
│
├── Models/
│   ├── masters/
│   │   ├── MstUserModel.php
│   │   └── MstRoleModel.php
│   │
│   ├── auth/
│   │   ├── TblLoginAttemptModel.php
│   │   ├── TblPasswordHistoryModel.php
│   │   └── TblUserOtpModel.php
│   │
│   └── common/
│       ├── TblLogModel.php
│       └── TblEmailLogModel.php
│
├── Services/
│   ├── Mail/
│   │   └── MailService.php
│   └── Security/
│       ├── RSAService.php
│       └── SessionService.php
bootstrap/
config/
database/
resources/
routes/
storage/
```

---

# Public Assets Structure


public/
│
├── assets/
│   ├── css/
│   ├── img/
│   ├── js/
│   │
│   ├── plugins/
│   │
│   ├── auth/
│   │   ├── login.js
│   │   ├── forgot.password.js
│   │   └── reset.password.js
│   │
│   ├── config/
│   │   └── app.config.js
│   │
│   └── security/
│       ├── security.js
│       └── device-fingerprint.js
│
├── uploads/
│
├── favicon.ico
├── index.php
└── .htaccess
```
---

# RSA Key Structure


storage/
│
└── keys/
    ├── private.pem
    └── public.pem
```


# Generate RSA Keys

## Generate Private Key

```bash
openssl genrsa -out storage/keys/private.pem 4096
```

## Generate Public Key

```bash
openssl rsa -in storage/keys/private.pem -pubout -out storage/keys/public.pem
```

---

# Important Security Rules

## NEVER expose


storage/keys/private.pem
```

## ONLY expose


storage/keys/public.pem
```

Private key must never be accessible through browser.

---

# Step 1 - Open Login Page

User accesses:


/login
```

System loads:


login.blade.php
```

Resources loaded:


Bootstrap 5
jQuery
jQuery Validation
JSEncrypt
security.js
login.js
```

---

# Step 2 - Load Public Key

Frontend requests:


/auth/public-key
```

Response:

```json
{
  "public_key": "-----BEGIN PUBLIC KEY-----"
}
```

Purpose:


Encrypt sensitive information before transmission.
```

---

# Step 3 - Client Side Validation

Before submission:


Email Required
Email Format Validation
Password Required
```

Validation Library:


jQuery Validation
```

---

# Step 4 - RSA Encryption

Sensitive fields:


username
password
```

Encryption:

```javascript
SecurityManager.encryptForm()
```

Result:

```json
{
  "username": "EncryptedValue",
  "password": "EncryptedValue"
}
```

---

# Step 5 - AJAX Authentication Request

Request:

```http
POST /login
```

Headers:

```http
X-CSRF-TOKEN
```

Payload:

```json
{
  "username": "EncryptedValue",
  "password": "EncryptedValue"
}
```

---

# Step 6 - CSRF Validation

Laravel validates:


CSRF Token
Session
Request Origin
```

Failure Response:

```json
{
  "success": 0,
  "message": "CSRF token mismatch"
}
```

---

# Step 7 - RSA Decryption

Backend service:


RSAService.php
```

Operations:

```php
RSAService::decrypt($request->username);

RSAService::decrypt($request->password);
```

Private Key:


storage/keys/private.pem
```

---

# Step 8 - Input Validation

Validation Rules:


Email Required
Valid Email Format
Password Required
```

Example:

```php
username => required|email

password => required
```

---

# Step 9 - Check Login Attempts

Table:


tbl_login_attempts
```

Query:


Count failed attempts
within previous 1 hour
```

Rules:


Maximum Attempts : 3
Lock Duration    : 1 Hour
```

Failure Response:

```json
{
  "success": 0,
  "message": "Account locked. Please try again after 1 hour."
}
```

---

# Step 10 - User Verification

Table:


mst_users
```

Conditions:


is_active = 1
is_delete = 0
email exists
```

Failure:

```json
{
  "success": 0,
  "message": "Invalid email or password"
}
```

---

# Step 11 - Password Verification

Password Storage:


Bcrypt Hash
```

Verification:

```php
Hash::check()
```

Failure Actions:


Record Failed Attempt
Write Audit Log
Increase Lock Counter
```

Table:


tbl_login_attempts
```

Example:


username
ip_address
is_success = 0
timestamp
```

---

# Step 12 - Successful Login

Record Success:


tbl_login_attempts
```

Data:


username
ip_address
is_success = 1
timestamp
```

---

# Step 13 - Session Regeneration

Security Protection:

```php
$request->session()->invalidate();

$request->session()->regenerateToken();

$request->session()->regenerate();
```

Purpose:


Prevent Session Fixation Attacks
```

---

# Step 14 - Store Session Data

Session Variables:


user_id
username
email
role_id
is_logged_in
```

Example:

```php
session([
    'user_id' => 1,
    'username' => 'Admin',
    'email' => 'admin@admin.com',
    'role_id' => 1,
    'is_logged_in' => true
]);
```

---

# Step 15 - Update Last Login

Table:


mst_users
```

Column:


last_login
```

Purpose:


Track user activity
```

---

# Step 16 - Audit Logging

Table:


tbl_logs
```

Event:


LOGIN
```

Stored Information:


User ID
IP Address
Action
Timestamp
Remarks
```

Example:


AUTH
LOGIN
Login successful
```

---

# Step 17 - Redirect User

Response:

```json
{
  "success": 1,
  "message": "Login successful",
  "redirect": "/dashboard"
}
```

Frontend:

```javascript
window.location.href =
    response.redirect;
```

---

# Security Controls Implemented

✓ RSA 4096 Encryption

✓ CSRF Protection

✓ Password Hash Verification

✓ MFA

✓ Session Regeneration

✓ Session Fixation Protection

✓ Login Attempt Tracking

✓ Account Locking

✓ Audit Logging

✓ SQL Injection Protection

✓ XSS Protection

✓ Authentication Middleware

✓ Role Based Access Control

---

# Database Tables Involved


mst_users
mst_roles
sessions
tbl_logs
tbl_login_attempts
tbl_email_logs
```

# Default Credentials


Email    : admin@admin.com
Password : Admin@123
Role     : Super Admin
```

Change password immediately after first login.

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