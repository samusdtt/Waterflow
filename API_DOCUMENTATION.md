# Water Management SaaS API Documentation

## Base URL
```
https://yourdomain.com/api
```

## Authentication
All API endpoints require authentication using Laravel Sanctum. Include the bearer token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## Response Format
All API responses follow this format:

### Success Response
```json
{
    "success": true,
    "data": { ... },
    "message": "Optional success message"
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

## Authentication Endpoints

### Login
**POST** `/api/login`

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "role": "client",
            "supplier_id": 1
        },
        "token": "1|abc123..."
    }
}
```

### Register
**POST** `/api/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password",
    "phone": "+1234567890",
    "address": "123 Main St",
    "city": "Mumbai",
    "state": "Maharashtra",
    "pincode": "400001",
    "role": "client"
}
```

### Logout
**POST** `/api/logout`

**Headers:**
```
Authorization: Bearer {token}
```

## Profile Endpoints

### Get Profile
**GET** `/api/profile`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "phone": "+1234567890",
        "address": "123 Main St",
        "city": "Mumbai",
        "state": "Maharashtra",
        "pincode": "400001",
        "role": "client",
        "supplier": {
            "id": 1,
            "name": "AquaFresh Water Solutions"
        }
    }
}
```

### Update Profile
**POST** `/api/profile`

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "phone": "+1234567891",
    "address": "456 New St",
    "city": "Mumbai",
    "state": "Maharashtra",
    "pincode": "400002"
}
```

## Product Endpoints

### Get Products
**GET** `/api/products`

**Response:**
```json
{
    "success": true,
    "data": {
        "jar": [
            {
                "id": 1,
                "name": "Amust Jar",
                "description": "Premium quality 20L water jar",
                "type": "jar",
                "size": "20L",
                "brand": "Amust",
                "price": 50.00,
                "formatted_price": "₹50.00",
                "stock_quantity": 100,
                "is_active": true
            }
        ],
        "box": [
            {
                "id": 3,
                "name": "Amust 200ml Box",
                "description": "Convenient 200ml water box",
                "type": "box",
                "size": "200ml",
                "brand": "Amust",
                "price": 105.00,
                "formatted_price": "₹105.00",
                "stock_quantity": 200,
                "is_active": true
            }
        ]
    }
}
```

## Order Endpoints

### Create Order
**POST** `/api/orders`

**Request Body:**
```json
{
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        },
        {
            "product_id": 3,
            "quantity": 1
        }
    ],
    "payment_method": "online",
    "delivery_address": "123 Delivery Address",
    "notes": "Please deliver in the morning"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Order created successfully",
    "data": {
        "id": 1,
        "order_number": "WM202401010001",
        "status": "pending",
        "payment_status": "pending",
        "payment_method": "online",
        "subtotal": 205.00,
        "tax_amount": 36.90,
        "total_amount": 241.90,
        "delivery_address": "123 Delivery Address",
        "notes": "Please deliver in the morning",
        "created_at": "2024-01-01T10:00:00.000000Z",
        "order_items": [
            {
                "id": 1,
                "product_id": 1,
                "quantity": 2,
                "unit_price": 50.00,
                "total_price": 100.00,
                "product": {
                    "id": 1,
                    "name": "Amust Jar",
                    "brand": "Amust",
                    "size": "20L"
                }
            }
        ]
    }
}
```

### Get Orders
**GET** `/api/orders`

**Query Parameters:**
- `status` (optional): Filter by order status (pending, confirmed, in_progress, delivered, cancelled)
- `limit` (optional): Number of orders per page (default: 20)

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "order_number": "WM202401010001",
                "status": "delivered",
                "payment_status": "paid",
                "total_amount": 241.90,
                "created_at": "2024-01-01T10:00:00.000000Z",
                "delivered_at": "2024-01-01T14:30:00.000000Z",
                "staff": {
                    "id": 2,
                    "name": "Rajesh Kumar"
                },
                "supplier": {
                    "id": 1,
                    "name": "AquaFresh Water Solutions"
                }
            }
        ],
        "first_page_url": "http://localhost/api/orders?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://localhost/api/orders?page=1",
        "links": [...],
        "next_page_url": null,
        "path": "http://localhost/api/orders",
        "per_page": 20,
        "prev_page_url": null,
        "to": 1,
        "total": 1
    }
}
```

### Get Order Details
**GET** `/api/orders/{id}`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "order_number": "WM202401010001",
        "status": "delivered",
        "payment_status": "paid",
        "payment_method": "online",
        "subtotal": 205.00,
        "tax_amount": 36.90,
        "total_amount": 241.90,
        "paid_amount": 241.90,
        "due_amount": 0.00,
        "delivery_address": "123 Delivery Address",
        "notes": "Please deliver in the morning",
        "created_at": "2024-01-01T10:00:00.000000Z",
        "delivered_at": "2024-01-01T14:30:00.000000Z",
        "order_items": [...],
        "staff": {...},
        "supplier": {...},
        "payments": [...]
    }
}
```

## Staff Endpoints

### Get Staff Orders
**GET** `/api/staff/orders`

**Query Parameters:**
- `status` (optional): Filter by order status
- `limit` (optional): Number of orders per page

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "order_number": "WM202401010001",
                "status": "confirmed",
                "client": {
                    "id": 3,
                    "name": "Amit Patel",
                    "phone": "+1234567895"
                },
                "total_amount": 241.90,
                "delivery_address": "123 Delivery Address",
                "created_at": "2024-01-01T10:00:00.000000Z"
            }
        ]
    }
}
```

### Mark Order as Delivered
**POST** `/api/staff/orders/{id}/delivered`

**Request Body:**
```json
{
    "delivery_notes": "Delivered successfully to customer"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Order marked as delivered",
    "data": {
        "id": 1,
        "status": "delivered",
        "delivered_at": "2024-01-01T14:30:00.000000Z"
    }
}
```

### Clock In
**POST** `/api/staff/clock-in`

**Response:**
```json
{
    "success": true,
    "message": "Clocked in successfully"
}
```

### Clock Out
**POST** `/api/staff/clock-out`

**Response:**
```json
{
    "success": true,
    "message": "Clocked out successfully",
    "data": {
        "total_hours": 8
    }
}
```

## Notification Endpoints

### Get Notifications
**GET** `/api/notifications`

**Query Parameters:**
- `limit` (optional): Number of notifications per page (default: 20)

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "type": "order_status",
                "title": "Order Status Update",
                "message": "Your order #WM202401010001 has been delivered",
                "data": {
                    "order_id": 1,
                    "status": "delivered"
                },
                "is_read": false,
                "created_at": "2024-01-01T14:30:00.000000Z"
            }
        ]
    }
}
```

### Mark Notification as Read
**POST** `/api/notifications/{id}/read`

**Response:**
```json
{
    "success": true,
    "message": "Notification marked as read"
}
```

## Error Codes

| HTTP Status | Description |
|-------------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Internal Server Error |

## Rate Limiting

API requests are rate limited to 60 requests per minute per user.

## Webhooks

### Payment Webhooks

#### Razorpay Webhook
**POST** `/api/webhooks/razorpay`

#### Stripe Webhook
**POST** `/api/webhooks/stripe`

## SDK Examples

### JavaScript/Node.js
```javascript
const axios = require('axios');

const api = axios.create({
    baseURL: 'https://yourdomain.com/api',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    }
});

// Create order
const createOrder = async (orderData) => {
    try {
        const response = await api.post('/orders', orderData);
        return response.data;
    } catch (error) {
        console.error('Error creating order:', error.response.data);
        throw error;
    }
};
```

### PHP
```php
<?php

$token = 'your-api-token';
$baseUrl = 'https://yourdomain.com/api';

$headers = [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
];

// Create order
$orderData = [
    'items' => [
        ['product_id' => 1, 'quantity' => 2]
    ],
    'payment_method' => 'online',
    'delivery_address' => '123 Main St'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/orders');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
```

### Python
```python
import requests

token = 'your-api-token'
base_url = 'https://yourdomain.com/api'

headers = {
    'Authorization': f'Bearer {token}',
    'Content-Type': 'application/json'
}

# Create order
order_data = {
    'items': [
        {'product_id': 1, 'quantity': 2}
    ],
    'payment_method': 'online',
    'delivery_address': '123 Main St'
}

response = requests.post(
    f'{base_url}/orders',
    json=order_data,
    headers=headers
)

result = response.json()
```

## Support

For API support and questions:
- Email: api-support@watermanagement.com
- Documentation: https://yourdomain.com/docs
- Status Page: https://status.watermanagement.com