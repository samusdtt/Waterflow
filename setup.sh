#!/bin/bash

# 🚀 Complete Laravel Water Management SaaS Setup Script
# This script installs all packages, sets up database, and runs all necessary commands

set -e  # Exit on any error

echo "🌊 Starting Laravel Water Management SaaS Setup..."
echo "=================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_warning "Running as root. This is recommended for package installation."
else
    print_warning "Not running as root. You may need sudo privileges for some commands."
fi

# Step 1: Update system packages
print_status "Updating system packages..."
apt update -y
apt upgrade -y

# Step 2: Install required system packages
print_status "Installing system packages..."
apt install -y \
    curl \
    wget \
    git \
    unzip \
    software-properties-common \
    apt-transport-https \
    ca-certificates \
    gnupg \
    lsb-release

# Step 3: Install PHP and extensions
print_status "Installing PHP and extensions..."
apt install -y \
    php8.4 \
    php8.4-cli \
    php8.4-fpm \
    php8.4-mysql \
    php8.4-xml \
    php8.4-curl \
    php8.4-mbstring \
    php8.4-zip \
    php8.4-gd \
    php8.4-intl \
    php8.4-bcmath \
    php8.4-json \
    php8.4-tokenizer \
    php8.4-fileinfo \
    php8.4-pdo \
    php8.4-pdo-mysql

# Step 4: Install MySQL
print_status "Installing MySQL..."
apt install -y mysql-server mysql-client

# Start and enable MySQL
systemctl start mysql
systemctl enable mysql

# Step 5: Install Node.js and npm
print_status "Installing Node.js and npm..."
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Step 6: Install Composer
print_status "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Step 7: Verify installations
print_status "Verifying installations..."
php --version
composer --version
node --version
npm --version
mysql --version

# Step 8: Navigate to project directory
print_status "Setting up project directory..."
cd /root/Waterflow

# Step 9: Install PHP dependencies
print_status "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Step 10: Install Node.js dependencies
print_status "Installing Node.js dependencies..."
npm install

# Step 11: Build frontend assets
print_status "Building frontend assets..."
npm run dev

# Step 12: Set up MySQL database
print_status "Setting up MySQL database..."

# Create database and user
mysql -e "CREATE DATABASE IF NOT EXISTS water_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS 'water_user'@'localhost' IDENTIFIED BY 'water_password';"
mysql -e "GRANT ALL PRIVILEGES ON water_management.* TO 'water_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

print_success "Database 'water_management' created successfully!"

# Step 13: Configure environment file
print_status "Configuring environment file..."

# Backup existing .env if it exists
if [ -f ".env" ]; then
    cp .env .env.backup
    print_status "Backed up existing .env file"
fi

# Create/update .env file
cat > .env << EOF
APP_NAME="Water Management SaaS"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=water_management
DB_USERNAME=water_user
DB_PASSWORD=water_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@watermanagement.com"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="\${PUSHER_HOST}"
VITE_PUSHER_PORT="\${PUSHER_PORT}"
VITE_PUSHER_SCHEME="\${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"

# Payment Gateways
STRIPE_KEY=
STRIPE_SECRET=
RAZORPAY_KEY=
RAZORPAY_SECRET=
EOF

print_success "Environment file configured!"

# Step 14: Generate application key
print_status "Generating application key..."
php artisan key:generate

# Step 15: Set proper permissions
print_status "Setting proper permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Step 16: Clear all caches
print_status "Clearing Laravel caches..."
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:clear

# Step 17: Run database migrations
print_status "Running database migrations..."
php artisan migrate --force

# Step 18: Seed the database
print_status "Seeding database with sample data..."
php artisan db:seed --force

# Step 19: Create storage link
print_status "Creating storage link..."
php artisan storage:link 2>/dev/null || true

# Step 20: Test database connection
print_status "Testing database connection..."
php artisan tinker --execute="echo 'Database connection: ' . (DB::connection()->getPdo() ? 'SUCCESS' : 'FAILED') . PHP_EOL;"

# Step 21: Display setup summary
echo ""
echo "=================================================="
print_success "🎉 Laravel Water Management SaaS Setup Complete!"
echo "=================================================="
echo ""
print_status "📋 Setup Summary:"
echo "  ✅ System packages installed"
echo "  ✅ PHP 8.4 with all required extensions"
echo "  ✅ MySQL database configured"
echo "  ✅ Node.js and npm installed"
echo "  ✅ Composer installed"
echo "  ✅ Laravel dependencies installed"
echo "  ✅ Frontend assets built"
echo "  ✅ Database created and migrated"
echo "  ✅ Sample data seeded"
echo "  ✅ Application key generated"
echo "  ✅ Permissions set"
echo "  ✅ Caches cleared"
echo ""
print_status "🔑 Default Login Credentials:"
echo "  Super Admin: admin@watermanagement.com / password"
echo "  Supplier Admin 1: admin@aquafresh.com / password"
echo "  Supplier Admin 2: admin@puredrop.com / password"
echo "  Staff 1: rajesh@aquafresh.com / password"
echo "  Staff 2: priya@puredrop.com / password"
echo "  Client 1: amit@example.com / password"
echo "  Client 2: sneha@example.com / password"
echo ""
print_status "🌐 Database Information:"
echo "  Database: water_management"
echo "  Username: water_user"
echo "  Password: water_password"
echo "  Host: localhost:3306"
echo ""
print_status "🚀 To start the application:"
echo "  php artisan serve"
echo "  Then visit: http://localhost:8000"
echo ""
print_status "📁 Project Structure:"
echo "  Frontend: CDN resources (Bootstrap, jQuery, Tailwind)"
echo "  Backend: Laravel 10 with MySQL"
echo "  Authentication: Multi-role system"
echo "  Features: Water management, orders, payments, staff tracking"
echo ""
print_success "Setup completed successfully! 🎉"