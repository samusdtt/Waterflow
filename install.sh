#!/bin/bash

# Water Management SaaS Installation Script
echo "🚀 Installing Water Management SaaS Application..."

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.1 or higher."
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
REQUIRED_VERSION="8.1.0"

if [ "$(printf '%s\n' "$REQUIRED_VERSION" "$PHP_VERSION" | sort -V | head -n1)" != "$REQUIRED_VERSION" ]; then
    echo "❌ PHP version $PHP_VERSION is not supported. Please install PHP 8.1 or higher."
    exit 1
fi

echo "✅ PHP version $PHP_VERSION is supported"

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    exit 1
fi

echo "✅ Composer is installed"

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
if command -v npm &> /dev/null; then
    echo "📦 Installing Node.js dependencies..."
    npm install
    npm run build
else
    echo "⚠️  Node.js/npm not found. Skipping frontend build."
fi

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    echo "✅ .env file created. Please update it with your database credentials."
else
    echo "✅ .env file already exists"
fi

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Check database connection
echo "🔍 Checking database connection..."
if php artisan migrate:status &> /dev/null; then
    echo "✅ Database connection successful"
else
    echo "❌ Database connection failed. Please check your .env file."
    echo "Make sure to update the following variables in your .env file:"
    echo "- DB_CONNECTION"
    echo "- DB_HOST"
    echo "- DB_PORT"
    echo "- DB_DATABASE"
    echo "- DB_USERNAME"
    echo "- DB_PASSWORD"
    exit 1
fi

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Set permissions
echo "🔐 Setting file permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Clear caches
echo "🧹 Clearing application caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "🎉 Installation completed successfully!"
echo ""
echo "Next steps:"
echo "1. Update your .env file with correct database credentials"
echo "2. Configure your web server to point to the public directory"
echo "3. Set up payment gateway credentials (Razorpay/Stripe)"
echo "4. Configure email settings for notifications"
echo "5. Create your first admin user"
echo ""
echo "To create an admin user, run:"
echo "php artisan tinker"
echo "Then execute:"
echo "User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password'), 'role' => 'super_admin']);"
echo ""
echo "Access your application at: http://your-domain.com"
echo ""
echo "📚 For more information, check the README.md file"