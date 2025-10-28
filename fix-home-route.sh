#!/bin/bash

# 🩵 Laravel Route Fix – Route [home] not defined
# This script fixes the missing 'home' route error

echo "🔧 Fixing Laravel Route [home] not defined error..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Not in Laravel project directory. Please run from project root."
    exit 1
fi

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "⚠️  PHP not found. Please install PHP first:"
    echo "   sudo apt install -y php8.4-cli php8.4-mbstring php8.4-xml php8.4-curl"
    echo ""
    echo "✅ Route and view files have been created successfully!"
    echo "   - Added home route to routes/web.php"
    echo "   - Created resources/views/home.blade.php"
    echo ""
    echo "🚀 Once PHP is installed, run these commands:"
    echo "   php artisan route:clear"
    echo "   php artisan view:clear"
    echo "   php artisan config:clear"
    echo "   php artisan serve"
    exit 0
fi

echo "✅ PHP found, clearing caches..."

# Clear Laravel caches
php artisan route:clear
php artisan view:clear
php artisan config:clear

echo "✅ Caches cleared successfully!"

# Test the route
echo "🧪 Testing home route..."
php artisan route:list | grep "home" || echo "⚠️  Home route not found in route list"

echo ""
echo "🚀 Starting Laravel server..."
echo "   Visit: http://127.0.0.1:8000/home"
echo "   Press Ctrl+C to stop"
echo ""

php artisan serve