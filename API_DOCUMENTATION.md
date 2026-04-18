# API Documentation

## Base URL

- **Development**: `http://localhost/api/v1`
- **Production**: `https://yourdomain.com/api/v1`

## Authentication

This API uses **Laravel Sanctum** for token-based authentication.

### Getting Started

1. Register a new account or login
2. Receive an authentication token
3. Include token in all subsequent requests

**Header Format:**

```
Authorization: Bearer {your-token}
```

---

## Authentication Endpoints

### Register

Create a new customer account.

**Endpoint:** `POST /register`  
**Auth Required:** No

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "securePassword123",
    "password_confirmation": "securePassword123",
    "phone": "+971501234567"
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "Registration successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+971501234567",
            "role": "customer",
            "notification_preferences": {
                "email": true,
                "sms": false,
                "whatsapp": false
            },
            "created_at": "2026-02-17T12:00:00.000000Z"
        },
        "token": "1|abc123xyz..."
    }
}
```

---

### Login

Authenticate and receive access token.

**Endpoint:** `POST /login`  
**Auth Required:** No

**Request Body:**

```json
{
    "email": "john@example.com",
    "password": "securePassword123"
}
```

**Success Response (200):**

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {...},
    "token": "2|xyz789abc..."
  }
}
```

---

### Get Profile

Retrieve authenticated user's profile.

**Endpoint:** `GET /me`  
**Auth Required:** Yes

**Success Response (200):**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+971501234567",
    "role": "customer",
    "notification_preferences": {...},
    "created_at": "2026-02-17T12:00:00.000000Z"
  }
}
```

---

### Update Profile

Update user profile information.

**Endpoint:** `PUT /profile`  
**Auth Required:** Yes

**Request Body:**

```json
{
    "name": "John Updated",
    "phone": "+971509876543",
    "password": "newPassword123",
    "password_confirmation": "newPassword123"
}
```

---

### Logout

Revoke current access token.

**Endpoint:** `POST /logout`  
**Auth Required:** Yes

**Success Response (200):**

```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

## Vehicle Endpoints

### List Vehicles

Get all available vehicles with optional filters.

**Endpoint:** `GET /vehicles`  
**Auth Required:** Yes

**Query Parameters:**

- `type` - Vehicle type (bus, van, coach)
- `min_capacity` - Minimum seating capacity
- `max_price` - Maximum daily rate
- `search` - Search by name
- `page` - Page number (pagination)

**Example:** `GET /vehicles?type=bus&min_capacity=30&page=1`

**Success Response (200):**

```json
{
  "success": true,
  "message": "Vehicles retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Luxury Coach 50-Seater",
      "type": "coach",
      "make": "Mercedes",
      "model": "Travego",
      "year": 2023,
      "seating_capacity": 50,
      "daily_rate": 800.00,
      "status": "active",
      "vehicle_number": "DXB-12345",
      "amenities": ["wifi", "ac", "tv"],
      "images": ["url1.jpg", "url2.jpg"],
      "description": "Premium luxury coach..."
    }
  ],
  "links": {...},
  "meta": {
    "current_page": 1,
    "total": 15
  }
}
```

---

### Get Vehicle Details

Get detailed information about a specific vehicle.

**Endpoint:** `GET /vehicles/{id}`  
**Auth Required:** Yes

**Success Response (200):**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Luxury Coach 50-Seater",
    ...
  }
}
```

---

### Check Availability

Check if a vehicle is available for specific dates.

**Endpoint:** `POST /vehicles/check-availability`  
**Auth Required:** Yes

**Request Body:**

```json
{
    "vehicle_id": 1,
    "start_date": "2026-03-15 10:00:00",
    "end_date": "2026-03-17 10:00:00"
}
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "available": true,
        "vehicle_id": 1,
        "start_date": "2026-03-15 10:00:00",
        "end_date": "2026-03-17 10:00:00"
    },
    "message": "Vehicle available"
}
```

---

## Booking Endpoints

### List Bookings

Get authenticated user's bookings.

**Endpoint:** `GET /bookings`  
**Auth Required:** Yes

**Success Response (200):**

```json
{
  "success": true,
  "message": "Bookings retrieved successfully",
  "data": [
    {
      "id": 123,
      "vehicle": {...},
      "start_date": "2026-03-15T10:00:00.000000Z",
      "end_date": "2026-03-17T10:00:00.000000Z",
      "pickup_location": "Dubai Marina",
      "dropoff_location": "Abu Dhabi",
      "with_driver": true,
      "status": "confirmed",
      "payment_status": "paid",
      "subtotal": 1600.00,
      "tax_amount": 80.00,
      "discount_amount": 0.00,
      "grand_total": 1680.00,
      "created_at": "2026-02-15T09:00:00.000000Z"
    }
  ]
}
```

---

### Create Booking

Create a new booking.

**Endpoint:** `POST /bookings`  
**Auth Required:** Yes

**Request Body:**

```json
{
    "vehicle_id": 1,
    "start_date": "2026-03-15 10:00:00",
    "end_date": "2026-03-17 10:00:00",
    "pickup_location": "Dubai Marina",
    "dropoff_location": "Abu Dhabi",
    "with_driver": true,
    "special_requests": "Please include GPS device"
}
```

**Success Response (201):**

```json
{
  "success": true,
  "message": "Booking created successfully",
  "data": {
    "id": 124,
    "vehicle": {...},
    "status": "pending",
    "payment_status": "pending",
    "grand_total": 1680.00,
    ...
  }
}
```

---

### Get Booking Details

Get details of a specific booking.

**Endpoint:** `GET /bookings/{id}`  
**Auth Required:** Yes

---

### Cancel Booking

Cancel a pending or confirmed booking.

**Endpoint:** `POST /bookings/{id}/cancel`  
**Auth Required:** Yes

**Success Response (200):**

```json
{
  "success": true,
  "message": "Booking cancelled successfully",
  "data": {
    "id": 124,
    "status": "cancelled",
    ...
  }
}
```

---

### Calculate Price

Get price estimate for a booking.

**Endpoint:** `POST /bookings/calculate-price`  
**Auth Required:** Yes

**Request Body:**

```json
{
    "vehicle_id": 1,
    "start_date": "2026-03-15 10:00:00",
    "end_date": "2026-03-17 10:00:00",
    "with_driver": true
}
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "subtotal": 1600.0,
        "tax": 80.0,
        "discount": 0.0,
        "total": 1680.0,
        "line_items": {
            "base_rent": 1600.0,
            "driver_fee": 200.0,
            "weekend_surge": 50.0
        }
    }
}
```

---

## Payment Endpoints

### Create Payment Intent

Create a Stripe Payment Intent for booking.

**Endpoint:** `POST /payments/intent`  
**Auth Required:** Yes

**Request Body:**

```json
{
    "booking_id": 124
}
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "client_secret": "pi_xxx_secret_yyy",
        "payment_intent_id": "pi_1ABC123",
        "booking_id": 124,
        "amount": 1680.0
    }
}
```

---

### Confirm Payment

Confirm successful payment.

**Endpoint:** `POST /payments/confirm`  
**Auth Required:** Yes

**Request Body:**

```json
{
    "payment_intent_id": "pi_1ABC123",
    "booking_id": 124
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Payment confirmed successfully",
    "data": {
        "booking_id": 124,
        "status": "confirmed",
        "payment_status": "paid"
    }
}
```

---

### Get Invoice

Get invoice data for paid booking.

**Endpoint:** `GET /invoices/{rentalId}`  
**Auth Required:** Yes

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "booking_id": 124,
        "customer_name": "John Doe",
        "vehicle": "Luxury Coach 50-Seater",
        "total": 1680.0,
        "payment_date": "2026-02-15T10:30:00.000000Z",
        "invoice_url": "https://yourdomain.com/portal/invoices/124"
    }
}
```

---

## Notification Endpoints

### List Notifications

Get all notifications for authenticated user.

**Endpoint:** `GET /notifications`  
**Auth Required:** Yes

**Success Response (200):**

```json
{
  "success": true,
  "message": "Notifications retrieved successfully",
  "data": [
    {
      "id": "abc-123",
      "type": "BookingConfirmed",
      "message": "Your booking has been confirmed",
      "data": {...},
      "read_at": null,
      "created_at": "2026-02-17T08:00:00.000000Z"
    }
  ]
}
```

---

### Get Unread Count

Get count of unread notifications.

**Endpoint:** `GET /notifications/unread`  
**Auth Required:** Yes

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "unread_count": 5
    }
}
```

---

### Mark as Read

Mark notification as read.

**Endpoint:** `POST /notifications/{id}/read`  
**Auth Required:** Yes

---

### Get Preferences

Get notification preferences.

**Endpoint:** `GET /notification-preferences`  
**Auth Required:** Yes

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "email": true,
        "sms": false,
        "whatsapp": false
    }
}
```

---

### Update Preferences

Update notification preferences.

**Endpoint:** `PUT /notification-preferences`  
**Auth Required:** Yes

**Request Body:**

```json
{
    "email": true,
    "sms": true,
    "whatsapp": false
}
```

---

## Error Responses

All error responses follow this format:

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field": ["Validation error message"]
    },
    "code": 422
}
```

### Common Error Codes

- **401**: Unauthorized - Invalid or missing token
- **422**: Validation Error - Invalid input data
- **404**: Not Found - Resource doesn't exist
- **500**: Server Error - Internal server error

---

## Rate Limiting

- **Limit**: 60 requests per minute per user
- **Headers**: Check `X-RateLimit-Remaining` header

---

## Testing with Postman/cURL

### Example cURL Request

```bash
# Login
curl -X POST http://localhost/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Get vehicles (with token)
curl -X GET http://localhost/api/v1/vehicles \
  -H "Authorization: Bearer 1|abc123xyz..." \
  -H "Accept: application/json"
```

---

## Support

For API support, contact: support@example.com
