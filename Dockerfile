# Gunakan base image PHP dengan FPM
FROM php:8.2-fpm

# Install dependencies OS
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Set permission
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

EXPOSE 8000
