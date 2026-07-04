# Date & Time Helper

## Overview

The application stores all date and time values in **UTC** within the database and converts them to the user's timezone only while displaying.

This approach follows enterprise best practices and avoids timezone-related issues.

---

# Why Store UTC?

Using UTC provides the following benefits:

- Consistent timestamps across all servers
- Easier deployment in multiple regions
- No daylight saving time (DST) issues
- Reliable scheduling and background jobs
- Simplified database maintenance
- Recommended by Laravel and MySQL

---

# Display Timezone

Current display timezone:

```
Asia/Kolkata
```

Only the presentation layer changes the timezone.

The database always stores UTC values.

---

# Helper File

```
app/
└── Helpers/
    └── DateHelper.php
```

---

# Helper Functions

## displayDateTime()

Displays both date and time.

### Syntax

```php
displayDateTime($date);
```

### Example

```blade
{{ displayDateTime($user->last_login) }}
```

Output

```
01-07-2026 06:45 PM
```

---

## displayDate()

Displays only the date.

### Syntax

```php
displayDate($date);
```

### Example

```blade
{{ displayDate($user->create_date) }}
```

Output

```
01-07-2026
```

---

## displayTime()

Displays only the time.

### Syntax

```php
displayTime($user->last_login);
```

Output

```
06:45 PM
```

---

# Custom Formatting

Every helper accepts an optional timezone and format.

Example

```php
displayDateTime(
    $user->last_login,
    'Asia/Kolkata',
    'd M Y h:i:s A'
);
```

Output

```
01 Jul 2026 06:45:23 PM
```

---

# Default Formats

## Date Time

```
d-m-Y h:i A
```

Example

```
01-07-2026 06:45 PM
```

---

## Date

```
d-m-Y
```

Example

```
01-07-2026
```

---

## Time

```
h:i A
```

Example

```
06:45 PM
```

---

# Autoload Configuration

Helper is loaded using Composer.

composer.json

```json
"autoload": {
    "files": [
        "app/Helpers/DateHelper.php"
    ]
}
```

Run

```bash
composer dump-autoload
```

---

# Usage

## Blade

```blade
{{ displayDateTime($user->last_login) }}
```

```blade
{{ displayDate($user->password_changed_at) }}
```

```blade
{{ displayTime($user->last_login) }}
```

---

## Controller

```php
$date = displayDateTime($user->last_login);
```

---

## Service

```php
return displayDate($record->create_date);
```

---

# Timezone Flow

```
Database (UTC)

        │

        ▼

Laravel

        │

        ▼

Carbon

        │

        ▼

Asia/Kolkata

        │

        ▼

Blade View
```

---

# Supported Columns

This helper is used for displaying:

- create_date
- update_date
- last_update
- last_login
- password_changed_at
- otp_expiry
- otp_last_sent_at
- expires_at
- used_at
- sent_at
- create_date (Email Logs)
- update_date (Email Logs)

---

# Best Practices

✔ Store all timestamps in UTC.

✔ Never save local timezone values in the database.

✔ Convert timezone only while displaying.

✔ Use helper functions instead of manually formatting dates.

✔ Keep formatting consistent across the application.

✔ Avoid hardcoding Carbon formatting inside Blade files.

---

# Future Improvements

The helper can be extended to automatically detect each user's preferred timezone.

Example

```
Asia/Kolkata

America/New_York

Europe/London

Asia/Dubai
```

The helper will use the user's saved timezone while continuing to store UTC in the database.

---

# Technologies

- Laravel 12
- Carbon
- PHP 8+
- MySQL (UTC Storage)
- InnoDB
- Composer Autoload

---

# Developer Information

```text
Developer  : Snehal Vasava
Company    : Pro-TEAM Solutions Pvt. Ltd.
Framework  : Laravel 12
Version    : 1.0.0
```