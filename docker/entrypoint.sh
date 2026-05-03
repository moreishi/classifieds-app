#!/bin/sh
set -e

# Wait for MySQL if using it
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT:-3306}..."
    while ! nc -z "$DB_HOST" "${DB_PORT:-3306}" 2>/dev/null; do
        sleep 1
    done
    echo "MySQL ready."
fi

# Ensure APP_KEY exists
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "Generating APP_KEY..."
    cd /app && php artisan key:generate --force
fi

# Migrate and seed (idempotent)
cd /app
php artisan migrate --force
php artisan db:seed --class=RoleSeeder --force 2>/dev/null || echo "Seed already done."

# Optimize
if [ "$APP_ENV" = "production" ]; then
    php artisan optimize
    php artisan view:cache
    php artisan event:cache
fi

# Storage link (idempotent)
php artisan storage:link --force 2>/dev/null || true

exec "$@"
