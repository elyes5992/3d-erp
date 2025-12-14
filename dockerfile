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

# Create the .env file (just so Laravel doesn't crash on boot)
RUN cp .env.example .env

# Generate Key
RUN php artisan key:generate --force

# ----------------------------------------------------
# DELETED: config:cache, route:cache, view:cache
# We must NOT run these here. We need Laravel to read 
# the real variables when the server starts.
# ----------------------------------------------------

# Expose port 10000
EXPOSE 10000

# Start the server
# We run 'config:clear' first to make sure no bad config is stuck
CMD php artisan config:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000