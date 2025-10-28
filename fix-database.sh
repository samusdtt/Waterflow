#!/bin/bash

# 🔧 Quick Database Fix Script
# Use this if you just need to fix database issues

echo "🔧 Fixing Laravel Database Issues..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Not in Laravel project directory. Please run from project root."
    exit 1
fi

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "❌ PHP not found. Please install PHP first:"
    echo "   sudo apt install -y php8.4-cli php8.4-mysql php8.4-mbstring php8.4-xml php8.4-curl"
    exit 1
fi

# Check if MySQL is running
if ! systemctl is-active --quiet mysql; then
    echo "🔄 Starting MySQL service..."
    sudo systemctl start mysql
fi

echo "✅ PHP and MySQL are available"

# Install dependencies if needed
if [ ! -d "vendor" ]; then
    echo "📦 Installing Laravel dependencies..."
    composer install --no-dev --optimize-autoloader
fi

# Generate application key if not set
if [ -z "$(grep APP_KEY= .env 2>/dev/null)" ] || [ "$(grep APP_KEY= .env 2>/dev/null)" = "APP_KEY=" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate
fi

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache

# Clear caches
echo "🧹 Clearing Laravel caches..."
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:clear

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Test database connection
echo "🧪 Testing database connection..."
php artisan tinker --execute="echo 'Database connection: ' . (DB::connection()->getPdo() ? 'SUCCESS' : 'FAILED') . PHP_EOL;"

echo ""
echo "✅ Database fix completed!"
echo "🚀 You can now start the server with: php artisan serve"