#!/bin/sh
set -e

# Wait for database to be ready
# (Optional: Add a small check if the database is up)

# Generate application key if APP_KEY is empty
if [ -z "$APP_KEY" ]; then
    echo "Generating Application Key..."
    php artisan key:generate --no-interaction
fi

# Create storage link if not exists
echo "Creating Storage Link..."
php artisan storage:link --no-interaction

# Run migrations
echo "Running Migrations..."
php artisan migrate --force --no-interaction

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
