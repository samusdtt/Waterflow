# Water Management SaaS - Deployment Guide

## Quick Start (Recommended)

### Using Docker (Easiest)

1. **Prerequisites:**
   - Docker and Docker Compose installed
   - Git (to clone the repository)

2. **Deploy:**
   ```bash
   # Clone the repository
   git clone <repository-url>
   cd Waterflow
   
   # Run the setup script
   ./setup.sh
   ```

3. **Access:**
   - Application: http://localhost
   - Database: localhost:3306 (user: root, password: password)

### Manual Setup

1. **System Requirements:**
   - PHP 8.1 or higher
   - Composer
   - Node.js and NPM
   - MySQL/MariaDB
   - Web server (Apache/Nginx)

2. **Install Dependencies:**
   ```bash
   # Install PHP extensions
   sudo apt install php8.1-cli php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-gd php8.1-intl php8.1-bcmath
   
   # Install Composer
   curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
   
   # Install Node.js and NPM
   curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
   sudo apt-get install -y nodejs
   ```

3. **Setup Application:**
   ```bash
   # Install PHP dependencies
   composer install --no-dev --optimize-autoloader
   
   # Install Node.js dependencies
   npm install
   npm run production
   
   # Setup environment
   cp .env.example .env
   php artisan key:generate
   
   # Setup database
   mysql -u root -p -e "CREATE DATABASE water_management;"
   php artisan migrate
   
   # Create storage link
   php artisan storage:link
   
   # Set permissions
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

4. **Configure Web Server:**
   
   **Apache (.htaccess):**
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^(.*)$ public/$1 [L]
   </IfModule>
   ```
   
   **Nginx:**
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /path/to/Waterflow/public;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
   }
   ```

## Production Deployment

### Using Docker Compose (Recommended)

1. **Production Configuration:**
   ```yaml
   # docker-compose.prod.yml
   version: '3.8'
   services:
     app:
       build: .
       environment:
         - APP_ENV=production
         - APP_DEBUG=false
         - DB_CONNECTION=mysql
         - DB_HOST=db
         - DB_DATABASE=water_management
         - DB_USERNAME=root
         - DB_PASSWORD=your_secure_password
       volumes:
         - ./storage:/var/www/storage
       restart: unless-stopped
     
     db:
       image: mariadb:10.11
       environment:
         - MYSQL_ROOT_PASSWORD=your_secure_password
         - MYSQL_DATABASE=water_management
       volumes:
         - db_data:/var/lib/mysql
       restart: unless-stopped
     
     nginx:
       image: nginx:alpine
       ports:
         - "80:80"
         - "443:443"
       volumes:
         - ./nginx.conf:/etc/nginx/nginx.conf
         - ./ssl:/etc/nginx/ssl
       restart: unless-stopped
   ```

2. **Deploy:**
   ```bash
   docker-compose -f docker-compose.prod.yml up -d
   ```

### Cloud Deployment

#### DigitalOcean App Platform
1. Connect your GitHub repository
2. Set environment variables:
   - `APP_KEY`: Generate with `php artisan key:generate --show`
   - `DB_CONNECTION`: mysql
   - `DB_HOST`: Your database host
   - `DB_DATABASE`: Your database name
   - `DB_USERNAME`: Your database username
   - `DB_PASSWORD`: Your database password

#### AWS Elastic Beanstalk
1. Create a new application
2. Upload your code as a ZIP file
3. Configure environment variables
4. Set up RDS for database

#### Heroku
1. Create a new app
2. Add MySQL addon
3. Set config vars
4. Deploy with Git

## Environment Configuration

### Required Environment Variables

```env
APP_NAME="Water Management SaaS"
APP_ENV=production
APP_KEY=base64:your_generated_key
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your_database_host
DB_PORT=3306
DB_DATABASE=water_management
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Payment Gateways
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret

# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Security Considerations

1. **Change default passwords**
2. **Use HTTPS in production**
3. **Set proper file permissions**
4. **Regular security updates**
5. **Database backups**
6. **Environment variable security**

## Troubleshooting

### Common Issues

1. **Permission Denied:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

2. **Database Connection Error:**
   - Check database credentials
   - Ensure database server is running
   - Verify network connectivity

3. **Composer Issues:**
   ```bash
   composer install --no-dev --optimize-autoloader
   composer dump-autoload
   ```

4. **Node.js Build Issues:**
   ```bash
   npm install
   npm run production
   ```

## Monitoring and Maintenance

1. **Log Monitoring:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Database Backups:**
   ```bash
   mysqldump -u username -p water_management > backup.sql
   ```

3. **Application Updates:**
   ```bash
   git pull origin main
   composer install --no-dev --optimize-autoloader
   php artisan migrate
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Support

For additional support, check:
- README.md for general information
- API_DOCUMENTATION.md for API details
- GitHub Issues for bug reports