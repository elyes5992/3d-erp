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

# --- THE FIX IS HERE ---
# Create a .env file from the example so Laravel has something to write to
RUN cp .env.example .env
# -----------------------

# Generate the app key
RUN php artisan key:generate --force

# Optimize the application
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Expose port 10000
EXPOSE 10000

# Start the server
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000