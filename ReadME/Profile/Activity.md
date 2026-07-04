# Activity Timeline Module

---

# Module

**Profile → Recent Activity Timeline**

This module displays the authenticated user's recent activities in a vertical timeline with **infinite scrolling**. Every important action performed by the user is recorded in the audit log and displayed in chronological order.

---

# Features

- Infinite Scroll
- Activity Timeline UI
- AJAX Based Loading
- User Specific Activity
- Audit Log Integration
- Dynamic Icons
- Dynamic Colors
- Human Readable Date & Time
- Secure Access
- CSRF Token Refresh
- Scrollable Timeline
- Responsive Design

---

# Screen

```
Profile
     │
     ▼
Recent Activity
     │
     ├── Login
     ├── Logout
     ├── OTP Verified
     ├── Password Changed
     ├── Profile Updated
     ├── Email Sent
     └── Load More...
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
│   └── common/
│       └── TblLogModel.php
│
resources/
└── views/
    └── profile/
        └── profile.blade.php

public/
└── assets/
    └── plugins/
        └── js/
            └── activity.js
```

---

# Database Table

```
tbl_logs
```

Important Columns

| Column | Description |
|----------|-------------|
| tbl_logs_id | Primary Key |
| mst_users_id | Logged In User |
| module | Module Name |
| module_id | Module Record ID |
| action | Activity Action |
| remarks | Activity Description |
| ip_address | Client IP Address |
| create_date | Activity Date |

---

# Supported Actions

| Action | Description |
|---------|-------------|
| LOGIN | User Login |
| LOGOUT | User Logout |
| OTP_LOGOUT | Logout Before OTP Verification |
| OTP_VERIFIED | OTP Successfully Verified |
| PASSWORD_CHANGED | Password Changed |
| PASSWORD_RESET_REQUEST | Password Reset Email Requested |
| PASSWORD_RESET | Password Successfully Reset |
| PROFILE_UPDATED | Personal Information Updated |
| EMAIL_SENT | Email Successfully Sent |

---

# Timeline Colors

| Action | Color |
|----------|--------|
| LOGIN | Emerald |
| LOGOUT | Rose |
| OTP_LOGOUT | Rose |
| OTP_VERIFIED | Green |
| PASSWORD_CHANGED | Amber |
| PASSWORD_RESET | Violet |
| PASSWORD_RESET_REQUEST | Blue |
| PROFILE_UPDATED | Cyan |
| EMAIL_SENT | Indigo |

---

# Timeline Icons

| Action | Lucide Icon |
|----------|-------------|
| LOGIN | log-in |
| LOGOUT | log-out |
| OTP_LOGOUT | log-out |
| OTP_VERIFIED | shield-check |
| PASSWORD_CHANGED | key |
| PASSWORD_RESET | refresh-ccw |
| PASSWORD_RESET_REQUEST | mail |
| PROFILE_UPDATED | user |
| EMAIL_SENT | mail |

---

# Request Flow

```
User Opens Profile
          │
          ▼
Load First 20 Records
          │
          ▼
Display Timeline
          │
          ▼
User Scrolls Down
          │
          ▼
AJAX Request
          │
          ▼
Next 20 Records
          │
          ▼
Append Timeline
          │
          ▼
Repeat Until Finished
```

---

# Controller

```
ProfileController
```

Methods

```
activity()

icon()

color()
```

---

# Query

```php
TblLogModel::where(
    'mst_users_id',
    session('user_id')
)
->orderByDesc('tbl_logs_id')
->offset($offset)
->limit(20);
```

---

# Pagination

Infinite scrolling uses Offset based pagination.

```
Request 1

Offset = 0

Returns 20 Rows

↓

Request 2

Offset = 20

Returns Next 20 Rows

↓

Request 3

Offset = 40

...
```

---

# AJAX Response

```json
{
    "success":1,
    "data":[
        {
            "remarks":"Profile updated successfully.",
            "icon":"user",
            "color":"cyan",
            "display_date":"04-07-2026 10:45 AM"
        }
    ],
    "hasMore":true,
    "csrfHash":"..."
}
```

---

# Timeline Card

Each activity contains

- Icon
- Background Color
- Remarks
- Activity Date
- Timeline Connector

Example

```
●
│
│
Profile updated successfully.

04-07-2026 10:45 AM
```

---

# Security

- Authenticated Users Only
- Session Middleware Protected
- User Specific Activity Only
- CSRF Protection
- AJAX Validation
- No Direct Database Exposure

---

# Performance

Current Implementation

- Offset Pagination
- 20 Records Per Request

Recommended for Future

```
Cursor Pagination
```

Benefits

- Faster
- Better for Large Tables
- No Offset Performance Issues

---

# Scroll Behaviour

Timeline Container

```
Fixed Height

↓

Vertical Scrollbar

↓

Infinite Loading
```

---

# Error Handling

If loading fails

```
Unable to load activity.
```

If no records exist

```
No activity found.
```

---

# Files

## Controller

```
ProfileController.php
```

Methods

```
activity()

icon()

color()
```

---

## JavaScript

```
activity.js
```

Responsibilities

- Load Activities
- Infinite Scroll
- AJAX Requests
- Dynamic Timeline
- CSRF Refresh
- Dynamic Icons
- Dynamic Colors
- Error Handling

---

## View

```
profile.blade.php
```

Contains

- Timeline Container
- Scrollable Panel
- Activity Cards

---

# Future Enhancements

- Activity Search
- Filter by Module
- Filter by Date
- Export Activity Log (Excel/PDF)
- Activity Statistics
- Real-time Activity (WebSocket)
- Timeline Grouped by Date
- Activity Notifications
- IP Address Display
- Browser & Device Information
- Geo Location Tracking
- Admin Activity Monitoring

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
