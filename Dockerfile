# Use a PHP + Apache image
FROM php:8.2-apache

# 1. Install system tools and Node.js
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && apt-get install -y nodejs

# 2. Install PHP extensions for MySQL and Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN a2enmod rewrite

# 3. Set up the working directory
COPY . /var/www/html
WORKDIR /var/www/html

# 4. Install Composer (PHP's package manager)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Build the application
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# 6. Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 7. Point Apache to Laravel's public directory
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

# 8. Start the server
CMD ["apache2-foreground"]