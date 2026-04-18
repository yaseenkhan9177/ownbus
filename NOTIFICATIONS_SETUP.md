# Notifications System Setup Guide

## Overview

The notification system sends multi-channel notifications to customers for booking events including booking confirmations, payment receipts, reminders, and cancellations.

---

## Configuration Required

### 1. Environment Variables (.env)

Add the following to your `.env` file:

```env
# Email (Already configured via MAIL_* variables)

# Twilio SMS (Optional - when ready)
TWILIO_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_PHONE_NUMBER=+1234567890

# WhatsApp (Optional - when ready)
WHATSAPP_FROM_NUMBER=+1234567890
```

### 2. Database Migration

Run the migrations to create the notifications table:

```bash
php artisan migrate
```

This creates:

- `notifications` table - Stores in-app notifications
- `notification_preferences` column in `users` table - Stores customer preferences

### 3. Queue Configuration

Notifications are queued for async processing. Configure your queue driver:

```env
QUEUE_CONNECTION=database  # or redis for production
```

Run the queue worker:

```bash
php artisan queue:work
```

For production, use Supervisor to keep the queue worker running.

### 4. Task Scheduler (Cron Job)

The booking reminder command runs hourly via Laravel's scheduler. Add this cron entry:

```cron
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**What it does:**

- Runs `bookings:send-reminders` every hour
- Finds bookings starting in 24 hours (±1 hour buffer)
- Sends BookingReminder notifications via email and in-app

---

## Features Implemented

### ✅ Email Notifications (4 types)

1. **BookingConfirmed** - Sent when booking is created
2. **PaymentReceived** - Sent after successful payment
3. **BookingReminder** - Sent 24h before pickup (automated)
4. **BookingCancelled** - Sent when booking is cancelled

### ✅ In-App Notifications

- Bell icon in navbar with unread count badge
- Dropdown showing latest 5 notifications
- Full notification center at `/portal/notifications`
- Mark as read / delete functionality

### ✅ Database Notifications

- Stored in `notifications` table
- Contains message, metadata, action URLs
- Supports mark-as-read tracking

### ✅ User Preferences (Ready for future)

- `notification_preferences` JSON column in users table
- Structure: `{"email": true, "sms": false, "whatsapp": false}`
- Can be extended with preference UI

---

## Usage

### Sending Notifications in Code

```php
use App\Notifications\BookingConfirmed;

// Send to a user
$user->notify(new BookingConfirmed($rental));

// Or use the Notification facade
Notification::send($user, new BookingConfirmed($rental));
```

### Channels Available

Each notification supports multiple channels:

- **mail** - Email via Laravel Mail
- **database** - Stored in database for in-app display
- **twilio** - SMS (when Twilio configured, currently commented out)

### Testing Reminders Manually

```bash
php artisan bookings:send-reminders
```

This will find all bookings starting in ~24 hours and send reminders.

---

## SMS/WhatsApp Integration (Optional)

When you have Twilio credentials:

1. **Install Twilio SDK** (already done)
2. **Configure .env** with Twilio credentials
3. **Uncomment SMS channel** in notification classes:
    - Locate `// $channels[] = 'twilio';` in each notification
    - Uncomment to enable SMS
4. **Create TwilioChannel** class (future enhancement)

---

## Notification Preferences UI (Future)

Create a settings page for customers to toggle notification channels:

```php
// Route: /portal/settings/notifications
// View: portal/settings/notifications.blade.php
// Controller: NotificationPreferencesController
```

Allow customers to:

- Enable/disable email notifications
- Enable/disable SMS notifications
- Enable/disable WhatsApp notifications
- Choose which events trigger notifications

---

## Routes

```php
GET  /portal/notifications              - List all notifications
POST /portal/notifications/{id}/read    - Mark notification as read
POST /portal/notifications/mark-all-read - Mark all as read
DELETE /portal/notifications/{id}        - Delete notification
```

---

## Troubleshooting

### Notifications not appearing?

1. Check queue is running: `php artisan queue:work`
2. Check database: `SELECT * FROM notifications`
3. Check logs: `storage/logs/laravel.log`

### Reminders not sending?

1. Verify cron is running: `grep CRON /var/log/syslog`
2. Test manually: `php artisan bookings:send-reminders`
3. Check booking has `status='confirmed'` and `payment_status='paid'`

### Emails not sending?

1. Check `.env` MAIL\_\* configuration
2. Test email: `php artisan tinker` → `Mail::raw('test', fn($m) => $m->to('test@example.com'))`
3. For development, use Mailtrap or MailHog

---

## Next Steps

1. ✅ Configure queue worker for production
2. ✅ Set up cron job for scheduler
3. ⏳ Add Twilio credentials for SMS (optional)
4. ⏳ Create notification preferences UI
5. ⏳ Test full booking → payment → reminder flow
