# Water Management SaaS Application

A comprehensive water delivery management system with multi-supplier support, built with Laravel and modern web technologies.

## 🚀 Features

### Client Side
- **Create Orders**: Browse products, add to cart, and place orders
- **Order History**: Track past orders, view invoices, and manage dues/credits
- **Payment Methods**: Support for Cash, Online, Due payments, and Credit Points
- **Profile Management**: Update personal information and delivery addresses
- **Real-time Notifications**: Order status updates and payment reminders

### Staff Side
- **Weekly Delivery Records**: Track and manage weekly delivery schedules
- **Order Management**: Mark orders as delivered and request payment verification
- **Attendance Tracking**: Clock in/out and track working hours
- **Profile Management**: Update personal information and contact details
- **Delivery Analytics**: View delivery statistics and performance metrics

### Admin Side
- **Daily Order Database**: Comprehensive order management with location-based filtering
- **Client Management**: Client-wise data organization for easier billing
- **Staff Management**: Assign staff, track attendance, and manage roles
- **Financial Tracking**: Daily accounts, income, expenses, and profit tracking
- **Inventory Management**: Jar records, refilling tracking, and stock management
- **Multi-Supplier Support**: Manage multiple suppliers with separate data isolation

### Multi-Supplier SaaS Features
- **Supplier Registration**: B2B supplier onboarding and subscription management
- **Data Isolation**: Complete separation of supplier data and operations
- **Subscription Management**: Monthly fee tracking and subscription status monitoring
- **Super Admin Panel**: Monitor all suppliers, revenue, and system-wide analytics
- **Location-based Availability**: Customers can see supplier availability by location

## 🛠 Technology Stack

- **Backend**: Laravel 10.x with PHP 8.1+
- **Frontend**: Blade templates with Tailwind CSS
- **Database**: MySQL/PostgreSQL with multi-tenant architecture
- **Authentication**: Laravel Sanctum for API authentication
- **Payment Gateways**: Razorpay and Stripe integration
- **API**: RESTful API endpoints for mobile app support

## 📋 Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL
- Node.js and NPM (for frontend assets)
- Web server (Apache/Nginx)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd water-management-saas
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database configuration**
   - Update `.env` file with your database credentials
   - Run migrations:
     ```bash
     php artisan migrate
     ```

6. **Seed initial data**
   ```bash
   php artisan db:seed
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## 🗄 Database Schema

### Core Tables
- `suppliers` - Supplier information and subscription details
- `users` - Multi-role user management (admin, supplier, staff, client)
- `products` - Product catalog with supplier-specific pricing
- `orders` - Order management with status tracking
- `order_items` - Individual items within orders
- `payments` - Payment tracking and gateway integration
- `jar_records` - Daily jar inventory management
- `daily_accounts` - Financial tracking and reporting
- `staff_attendance` - Staff login hours and attendance
- `notifications` - System-wide notification management

### Key Relationships
- Multi-tenant architecture with supplier-based data isolation
- Role-based access control (RBAC) system
- Comprehensive order lifecycle management
- Financial tracking and reporting capabilities

## 🔐 User Roles & Permissions

### Super Admin
- System-wide access and management
- Supplier approval and subscription management
- Global analytics and reporting
- System configuration and maintenance

### Supplier Admin
- Supplier-specific data management
- Staff and client management within supplier scope
- Financial tracking and reporting
- Product catalog management

### Staff
- Order delivery management
- Attendance tracking
- Payment verification requests
- Limited profile management

### Client
- Order creation and management
- Payment processing
- Profile and address management
- Order history and tracking

## 📱 API Endpoints

### Authentication
- `POST /api/login` - User authentication
- `POST /api/register` - User registration
- `POST /api/logout` - User logout

### Client APIs
- `GET /api/products` - Get available products
- `POST /api/orders` - Create new order
- `GET /api/orders` - Get order history
- `GET /api/orders/{id}` - Get order details

### Staff APIs
- `GET /api/staff/orders` - Get assigned orders
- `POST /api/staff/orders/{id}/delivered` - Mark order as delivered
- `POST /api/staff/clock-in` - Clock in
- `POST /api/staff/clock-out` - Clock out

### General APIs
- `GET /api/profile` - Get user profile
- `POST /api/profile` - Update user profile
- `GET /api/notifications` - Get notifications
- `POST /api/notifications/{id}/read` - Mark notification as read

## 💳 Payment Integration

### Supported Payment Methods
- **Cash**: Traditional cash payments
- **Online**: Razorpay and Stripe integration
- **Due**: Credit-based payments with tracking
- **Credit Points**: Loyalty points system

### Payment Flow
1. Order creation with payment method selection
2. Payment processing through selected gateway
3. Payment verification and confirmation
4. Order status updates and notifications

## 🔔 Notification System

### Notification Types
- Subscription expiry alerts
- Low stock notifications
- Payment due reminders
- Order status updates
- Staff attendance alerts

### Delivery Channels
- In-app notifications
- Email notifications
- SMS notifications (configurable)

## 📊 Reporting & Analytics

### Admin Dashboards
- Daily order summaries
- Financial performance tracking
- Staff productivity metrics
- Client engagement analytics
- Supplier performance monitoring

### Custom Reports
- Location-based order analysis
- Payment method distribution
- Staff attendance reports
- Inventory management reports
- Revenue and profit tracking

## 🚀 Deployment

### Production Environment
1. Configure production database
2. Set up web server (Apache/Nginx)
3. Configure SSL certificates
4. Set up payment gateway credentials
5. Configure email/SMS services
6. Set up monitoring and logging

### Environment Variables
```env
APP_NAME="Water Management SaaS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=water_management_saas
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

RAZORPAY_KEY_ID=your-razorpay-key
RAZORPAY_KEY_SECRET=your-razorpay-secret
STRIPE_KEY=your-stripe-key
STRIPE_SECRET=your-stripe-secret
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation wiki

## 🔄 Version History

- **v1.0.0** - Initial release with core functionality
- **v1.1.0** - Added mobile API support
- **v1.2.0** - Enhanced reporting and analytics
- **v1.3.0** - Multi-supplier SaaS features

---

**Built with ❤️ for efficient water delivery management**