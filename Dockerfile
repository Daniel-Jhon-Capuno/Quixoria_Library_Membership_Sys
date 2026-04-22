# 1. Use the official PHP 8.4 Apache image
FROM php:8.4-apache

# 2. Install system dependencies and PHP extensions
# Added libzip-dev and the zip extension
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql gd zip

# 3. Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# 4. Set the working directory
WORKDIR /var/www/html

# 5. Copy the project files into the container
COPY . .

# 6. Install Composer (PHP package manager)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# We add --ignore-platform-req=ext-http if needed, but adding 'zip' above should fix your specific error
RUN composer install --no-dev --optimize-autoloader

# 7. Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Update Apache to point to Laravel's /public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 9. THE "MAGIC" COMMAND: Migrate, Cache, and Start
CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    apache2-foreground