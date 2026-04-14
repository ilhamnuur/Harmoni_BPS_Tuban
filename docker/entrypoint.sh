#!/bin/sh
set -e

# Wait for database to be ready
# (Optional: Add a small check if the database is up)

# Clear bootstrap cache files that might be mounted from host
echo "Clearing Bootstrap Cache..."
rm -f bootstrap/cache/*.php

# Fix permissions for storage and cache
echo "Fixing Permissions..."
chown -R www-data:www-data storage bootstrap/cache

# Generate application key if APP_KEY is empty
if [ -z "$APP_KEY" ]; then
    echo "Generating Application Key..."
    php artisan key:generate --no-interaction
fi

# Create storage link if not exists (force recreate for Docker/Linux)
echo "Recreating Storage Link..."
rm -rf public/storage
php artisan storage:link --no-interaction

# Run migrations
echo "Running Migrations..."
if ! php artisan migrate --force --no-interaction; then
    echo "WARNING: Migrations failed. Continuing anyway..."
fi

# Optimizing Laravel
echo "Optimizing Application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# If a custom command was passed, execute it. Otherwise, start php-fpm.
if [ "$#" -gt 0 ]; then
    echo "Executing command: $@"
    exec "$@"
else
    # Start PHP-FPM
    echo "Starting PHP-FPM..."
    exec php-fpm
fi
