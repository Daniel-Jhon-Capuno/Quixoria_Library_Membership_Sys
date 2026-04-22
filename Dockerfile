# 1. Use PHP 8.4 (The latest to match your composer requirements)
FROM php:8.4-apache

# 2. Install system tools, Node.js, and ZIP libraries
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip git curl

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

# 3. Install PHP extensions (Added zip here)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip
RUN a2enmod rewrite

# 4. Set up the working directory
COPY . /var/www/html
WORKDIR /var/www/html

# 5. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Build the application
# We add --ignore-platform-reqs just in case there's a small mismatch, 
# but PHP 8.4 should cover it.
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# 7. Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Point Apache to Laravel's public directory
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

CMD ["apache2-foreground"]