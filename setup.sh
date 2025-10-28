#!/bin/bash

echo "🚀 Setting up Water Management SaaS Application..."

# Check if Docker is available
if command -v docker &> /dev/null; then
    echo "✅ Docker found. Using Docker setup..."
    
    # Build and run with Docker Compose
    docker-compose up --build -d
    
    echo "🎉 Application is starting up!"
    echo "📱 Access your application at: http://localhost"
    echo "🗄️  Database is available at: localhost:3306"
    echo ""
    echo "To view logs: docker-compose logs -f"
    echo "To stop: docker-compose down"
    
elif command -v docker &> /dev/null; then
    echo "✅ Docker found. Using Docker setup..."
    
    # Build and run with Docker
    docker build -t water-management .
    docker run -d -p 8000:8000 --name water-management-app water-management
    
    echo "🎉 Application is starting up!"
    echo "📱 Access your application at: http://localhost:8000"
    
else
    echo "❌ Docker not found. Please install Docker to run this application."
    echo ""
    echo "For manual setup, you need:"
    echo "1. PHP 8.1+ with extensions: pdo_mysql, mbstring, xml, curl, zip, gd, intl, bcmath"
    echo "2. Composer"
    echo "3. Node.js and NPM"
    echo "4. MySQL/MariaDB"
    echo ""
    echo "Then run:"
    echo "composer install"
    echo "npm install && npm run production"
    echo "php artisan key:generate"
    echo "php artisan migrate"
    echo "php artisan serve"
fi