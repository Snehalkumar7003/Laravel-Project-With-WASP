# Personal Information Module

---

# Module

**Profile → Personal Information**

This module allows an authenticated user to securely update their personal profile information.

---

# Features

- Update Name
- Update Mobile Number
- Upload / Replace Profile Photo
- Live Image Preview
- Client-side Validation
- Server-side Validation
- RSA Encryption for Sensitive Fields
- CSRF Protection
- Device Fingerprint Validation
- Audit Logging
- Secure File Upload
- Transaction Management
- AJAX Based Form Submission

---

# Screen

```
Profile
    │
    ├── Profile Photo
    ├── Name
    ├── Email (Read Only)
    ├── Mobile
    └── Save Changes
```

---

# Folder Structure

```
app/
├── Http/
│   └── Controllers/
│       └── Profile/
│           └── ProfileController.php
│
├── Models/
│   ├── masters/
│   │   └── MstUserModel.php
│   │
│   └── common/
│       └── TblLogModel.php
│
├── Services/
│   └── Security/
│       └── RSAService.php
│
resources/
└── views/
    └── profile/
        └── profile.blade.php

public/
└── uploads/
    └── profile/

public/
└── assets/
    └── plugins/
        └── js/
            └── profile.js
```

---

# Security Flow

```
User
   │
   ▼
Profile Form
   │
   ▼
Client Validation
   │
   ▼
RSA Encrypt Email & Mobile
   │
   ▼
AJAX Request
   │
   ▼
Laravel Controller
   │
   ▼
RSA Decrypt
   │
   ▼
Server Validation
   │
   ▼
Upload Image
   │
   ▼
Audit Log
   │
   ▼
Database Commit
```

---

# Fields

| Field | Required | Editable |
|--------|----------|----------|
| Profile Photo | No | Yes |
| Name | Yes | Yes |
| Email | Yes | No |
| Mobile | Yes | Yes |

---

# Client Validation

## Name

- Required
- Minimum 3 characters
- Maximum 150 characters
- Letters and spaces only

Regex

```
^[A-Za-z]+(?: [A-Za-z]+)*$
```

---

## Mobile

- Required
- Numeric only
- Minimum 10 digits
- Maximum 13 digits

---

## Profile Photo

Allowed Types

- JPG
- JPEG
- PNG
- GIF
- WEBP

Maximum Size

```
2 MB
```

---

# Server Validation

```php
username
required
min:3
max:150
regex:/^[A-Za-z]+(?: [A-Za-z]+)*$/

email
required
email

mobile
required
digits_between:10,13

profile_image
nullable
image
mimes:jpg,jpeg,png,gif,webp
max:2048
```

---

# Image Upload

Location

```
public/uploads/profile/
```

Filename Format

```
USR_{USER_ID}_{TIMESTAMP}.jpg
```

Example

```
USR_15_20260704103520.jpg
```

---

# Existing Image Handling

Whenever a new profile photo is uploaded

```
Delete Old Image
        │
        ▼
Upload New Image
        │
        ▼
Save Filename
```

This prevents unused images from accumulating on the server.

---

# Audit Log

Every successful profile update creates an audit record.

Module

```
PROFILE
```

Action

```
PROFILE_UPDATED
```

Remarks

```
Profile updated successfully.
```

---

# Database Transaction

The update process runs inside a database transaction.

```
Begin Transaction
        │
        ▼
Validate Request
        │
        ▼
Upload Image
        │
        ▼
Update User
        │
        ▼
Create Audit Log
        │
        ▼
Commit
```

If any exception occurs

```
Rollback Transaction
```

---

# AJAX Response

## Success

```json
{
    "success":1,
    "message":"Profile updated successfully."
}
```

---

## Validation Error

```json
{
    "success":0,
    "message":"Please enter your name."
}
```

---

## Server Error

```json
{
    "success":0,
    "message":"Unable to update profile."
}
```

---

# Security

Implemented security controls

- Laravel Session Authentication
- RSA 4096 Encryption
- CSRF Protection
- Device Fingerprint Validation
- Server-side Validation
- MIME Type Validation
- File Size Validation
- Audit Logging
- Exception Handling
- Transaction Management

---

# Files

## Controller

```
ProfileController.php
```

Methods

```
index()

update()
```

---

## JavaScript

```
profile.js
```

Responsibilities

- Avatar Preview
- File Validation
- Form Validation
- RSA Encryption
- AJAX Submission
- Success/Error Messages

The profile JavaScript handles avatar preview, client-side validation, RSA encryption, AJAX submission, and success/error notifications. :contentReference[oaicite:0]{index=0}

---

## View

```
profile.blade.php
```

Contains

- Personal Information Form
- Avatar Upload
- Profile Preview
- Alert Container

---

# Future Enhancements

- Crop Profile Image
- Drag & Drop Upload
- Image Compression
- Remove Profile Photo
- Camera Capture Support
- Activity Timeline Integration
- Profile Completion Percentage
- Email Change Verification
- Mobile OTP Verification
- Cloud Storage (AWS S3 / Azure Blob)

---

## Developer Information

```text
Developer  : Snehal Vasava
Company    : Pro-TEAM Solutions Pvt. Ltd.
Framework  : Laravel 12
Frontend   : Bootstrap 5 + jQuery
Database   : MySQL (InnoDB)
Security   : RSA 4096 + Email OTP + CSRF
Version    : 1.0.0
```