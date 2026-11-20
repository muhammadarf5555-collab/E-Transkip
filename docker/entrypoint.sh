#!/bin/bash
set -e

# Default to SQLite if no external DB configured
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    export DB_CONNECTION=sqlite
    export DB_DATABASE=/var/www/html/database/database.sqlite
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
        chown www-data:www-data /var/www/html/database/database.sqlite
        chmod 664 /var/www/html/database/database.sqlite
    fi
fi

# Ensure storage directories exist
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate APP_KEY if not supplied
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations & seeders automatically
php artisan migrate --force
php artisan db:seed --force

# Optimize configuration and routing for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP-FPM
php-fpm -D

# Start Nginx in foreground
echo "E-Transkrip is running on port 80..."
exec nginx -g "daemon off;"

