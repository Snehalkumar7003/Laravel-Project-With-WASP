# Laravel Enterprise Authentication System

## Overview

Enterprise-grade authentication system built with Laravel.

Features:

* RSA 4096 Encryption
* CSRF Protection
* AJAX Authentication
* Session-Based Login
* OWASP Security Guidelines
* Audit Logging
* jQuery Validation
* Password Hashing (Bcrypt)
* Single Entry Security Layer

---

# Login Structure

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Auth/
│   │       ├── LoginController.php
│   │       ├── ForgotPasswordController.php
│   │       └── ResetPasswordController.php
│   │
│   └── Middleware/
│
├── Models/
|   ├──Masters
│   │  ├── MstUserModel.php
│   │  └── MstRole.php
│   ├──Common
│   │  └── TblLogModel.php
│   └──auth
│      └── TblLoginAttemptModel.php
├── Services/
│   └── Security/
│       └── RSAService.php
│
├── Helpers/
│
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
│       └── security.js
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

---

# Security Architecture

```text
Browser
   │
   ▼
Load Public Key
   │
   ▼
RSA Encrypt Username & Password
   │
   ▼
AJAX Request
   │
   ▼
Laravel Controller
   │
   ▼
RSA Decrypt Credentials
   │
   ▼
Validate User
   │
   ▼
Password Hash Verification
   │
   ▼
Create Session
   │
   ▼
Write Audit Log
   │
   ▼
Dashboard
```

---

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

# Authentication Flow

```text
Login Page
    ↓
Validate Form
    ↓
Encrypt Username
    ↓
Encrypt Password
    ↓
AJAX Submit
    ↓
Decrypt Server Side
    ↓
Verify Password Hash
    ↓
Regenerate Session
    ↓
Update Last Login
    ↓
Write Audit Log
```

---

# Database Tables

```text
mst_users
mst_roles
sessions
tbl_logs
password_reset_tokens
```

---

# Frontend Libraries

```text
Bootstrap 5
jQuery 3.7.1
jQuery Validation 1.21.0
JSEncrypt
Lucide Icons
```

---

# Environment Setup

## Development

```bash
composer install

php artisan key:generate

php artisan migrate

php artisan db:seed

php artisan serve
```

---

# Default Credentials

```text
Email    : admin@admin.com
Password : Admin@123
Role     : Super Admin
```

Change password immediately after first login.

---

# OWASP Security Controls

Implemented:

✓ CSRF Protection

✓ Session Regeneration

✓ Password Hashing

✓ SQL Injection Protection

✓ XSS Protection

✓ Audit Logging

✓ RSA Encryption Layer

✓ Authentication Middleware

✓ Role-Based Access Control

Planned:

□ Login Rate Limiting

□ Multi-Factor Authentication

□ Device Fingerprinting

□ Single Session Enforcement

□ Security Headers Middleware

---

# Audit Logging

All authentication events are recorded.

```text
LOGIN
LOGOUT
PASSWORD_RESET
FORGOT_PASSWORD
USER_CREATE
USER_UPDATE
USER_DELETE
```

Stored in:

```text
tbl_logs
```

---

# Developer Information

```text
Developer  : Snehal Vasava
Company    : Pro-TEAM Solutions Pvt. Ltd.
Framework  : Laravel 12
Frontend   : Bootstrap 5 + jQuery
Database   : MySQL
Security   : RSA 4096 + Bcrypt + CSRF
Version    : 1.0.0
```
