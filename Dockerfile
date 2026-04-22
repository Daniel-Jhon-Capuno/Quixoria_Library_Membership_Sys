# 1. Use PHP 8.4 (Required for your Laravel/Symfony version)
FROM php:8.4-apache

# 2. Install all necessary system libraries
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql gd zip

# 3. Enable Apache mod_rewrite
RUN a2enmod rewrite

# 4. Set the working directory
WORKDIR /var/www/html

# 5. Copy your project files
COPY . .

# 6. Install Composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 7. CRITICAL: Set the permissions for Render's user
# We give full ownership to www-data and ensure storage is writable
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Point Apache to the Laravel public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 9. THE STARTUP COMMAND
# Migrates database, clears old cache, and starts the server
CMD php artisan migrate --force && \
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    apache2-foreground