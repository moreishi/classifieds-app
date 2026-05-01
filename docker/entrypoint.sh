#!/bin/sh
# classifieds-app entrypoint — runs on every container start

# Create storage structure (named volume starts empty)
mkdir -p /app/storage/framework/{sessions,views,cache/data}
mkdir -p /app/storage/logs

# Create storage symlink (safe to run every time — Laravel uses --force)
php /app/artisan storage:link --force 2>/dev/null || true

# Run migrations on startup (safe — checks for pending)
php /app/artisan migrate --force --seed

# Fix permissions for www-data
chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database

exec "$@"
