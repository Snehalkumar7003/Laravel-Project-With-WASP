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
```text
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
```

---

# Login Structure
```text
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

```text
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

```text
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

```text
storage/keys/private.pem
```

## ONLY expose

```text
storage/keys/public.pem
```

Private key must never be accessible through browser.

---

# Step 1 - Open Login Page

User accesses:

```text
/login
```

System loads:

```text
login.blade.php
```

Resources loaded:

```text
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

```text
/auth/public-key
```

Response:

```json
{
  "public_key": "-----BEGIN PUBLIC KEY-----"
}
```

Purpose:

```text
Encrypt sensitive information before transmission.
```

---

# Step 3 - Client Side Validation

Before submission:

```text
Email Required
Email Format Validation
Password Required
```

Validation Library:

```text
jQuery Validation
```

---

# Step 4 - RSA Encryption

Sensitive fields:

```text
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

```text
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

```text
RSAService.php
```

Operations:

```php
RSAService::decrypt($request->username);

RSAService::decrypt($request->password);
```

Private Key:

```text
storage/keys/private.pem
```

---

# Step 8 - Input Validation

Validation Rules:

```text
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

```text
tbl_login_attempts
```

Query:

```text
Count failed attempts
within previous 1 hour
```

Rules:

```text
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

```text
mst_users
```

Conditions:

```text
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

```text
Bcrypt Hash
```

Verification:

```php
Hash::check()
```

Failure Actions:

```text
Record Failed Attempt
Write Audit Log
Increase Lock Counter
```

Table:

```text
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

```text
tbl_login_attempts
```

Data:

```text
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

```text
Prevent Session Fixation Attacks
```

---

# Step 14 - Store Session Data

Session Variables:

```text
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

```text
mst_users
```

Column:

```text
last_login
```

Purpose:

```text
Track user activity
```

---

# Step 16 - Audit Logging

Table:

```text
tbl_logs
```

Event:

```text
LOGIN
```

Stored Information:

```text
User ID
IP Address
Action
Timestamp
Remarks
```

Example:

```text
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

```text
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