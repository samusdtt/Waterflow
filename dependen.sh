#!/bin/bash
# 🚀 Water Management SaaS Manual Setup (Termux/Debian)
echo "==============================================="
echo "🚀 Setting up Water Management SaaS Application"
echo "==============================================="

# Update packages
apt update -y && apt upgrade -y

# Install core dependencies
apt install php php-mbstring php-xml php-curl php-zip php-gd php-intl php-bcmath php-mysql composer nodejs mariadb git unzip -y

# Start MariaDB service
mysql_install_db > /dev/null 2>&1
mysqld_safe --skip-grant-tables > /dev/null 2>&1 &

sleep 5

# Create database
echo "📦 Creating database..."
mysql -u root -e "CREATE DATABASE IF NOT EXISTS water_management;"

# Install PHP dependencies
echo "📥 Installing Composer packages..."
composer install --no-interaction --prefer-dist

# Install JS dependencies
echo "📥 Installing Node packages..."
npm install && npm run production

# Configure Laravel
echo "⚙️  Generating app key..."
php artisan key:generate

echo "⚙️  Running migrations..."
php artisan migrate --force

# Start Laravel server
echo "✅ Setup Complete! Starting server..."
php artisan serve --host=0.0.0.0 --port=8000
