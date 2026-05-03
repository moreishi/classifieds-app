#!/bin/sh
set -e

# Wait for DB if using MySQL
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Waiting for MySQL..."
    while ! nc -z "$DB_HOST" "${DB_PORT:-3306}"; do
        sleep 1
    done
    echo "MySQL ready."
fi

# Generate APP_KEY if not set
if ! grep -q "APP_KEY=" .env || [ -z "$(grep 'APP_KEY=' .env | cut -d= -f2)" ]; then
    php artisan key:generate --force
fi

# Run migrations (idempotent)
php artisan migrate --force

# Cache for production
if [ "$APP_ENV" = "production" ]; then
    php artisan optimize
    php artisan view:cache
    php artisan event:cache
fi

# Storage link (safe to run multiple times)
php artisan storage:link --force

exec "$@"
