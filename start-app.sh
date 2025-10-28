#!/bin/bash

# 🚀 Laravel Application Startup Script
# Run this after fixing the environment setup

echo "🔧 Starting Laravel Water Management SaaS Application..."

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "❌ PHP not found. Please install PHP first:"
    echo "   sudo apt install -y php8.4-cli php8.4-mbstring php8.4-xml php8.4-curl"
    exit 1
fi

# Check if Composer is available
if ! command -v composer &> /dev/null; then
    echo "❌ Composer not found. Please install Composer first:"
    echo "   curl -sS https://getcomposer.org/installer | php"
    echo "   sudo mv composer.phar /usr/local/bin/composer"
    exit 1
fi

echo "✅ PHP and Composer found"

# Install dependencies if vendor directory doesn't exist
if [ ! -d "vendor" ]; then
    echo "📦 Installing Laravel dependencies..."
    composer install --no-dev --optimize-autoloader
fi

# Clear caches
echo "🧹 Clearing Laravel caches..."
php artisan view:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan config:clear 2>/dev/null || true

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true

# Generate application key if not set
if [ -z "$(grep APP_KEY= .env 2>/dev/null)" ] || [ "$(grep APP_KEY= .env 2>/dev/null)" = "APP_KEY=" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate 2>/dev/null || true
fi

# Start the server
echo "🚀 Starting Laravel server at http://127.0.0.1:8000"
echo "   Press Ctrl+C to stop the server"
echo ""

php artisan serve --host=0.0.0.0 --port=8000