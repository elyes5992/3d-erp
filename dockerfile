# Use PHP 8.2
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy all project files
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# ----------------------------------------------------------------
# FIX 1: Create an EMPTY .env file
# This forces Laravel to use the settings from Render Dashboard
# instead of reading 127.0.0.1 from a local file.
# ----------------------------------------------------------------
RUN touch .env

# ----------------------------------------------------------------
# FIX 2: Set Permissions
# This fixes the "500 Server Error" caused by permission denied
# on the storage folder.
# ----------------------------------------------------------------
RUN chmod -R 777 storage bootstrap/cache


# Expose port 10000
EXPOSE 10000

# Start command
CMD php artisan config:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000