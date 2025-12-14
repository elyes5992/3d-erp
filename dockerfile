# Use PHP 8.2
FROM php:8.2-cli

# Install system dependencies and Postgres drivers
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

# ----------------------------------------------------
# THE FIX: Do not copy the example file. 
# Just create an empty file. 
# This forces Laravel to use the Render Environment Variables.
# ----------------------------------------------------
RUN touch .env

# Generate Key (This might fail if APP_KEY is not in env, 
# but we set it in Render, so it should be fine. 
# If it fails, we remove this line because APP_KEY is already in Render)
# RUN php artisan key:generate --force  <-- COMMENT THIS OUT

# Expose port 10000
EXPOSE 10000

# Start the server
CMD php artisan config:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000