# Production Deployment Checklist

## 1. Prerequisites

- [ ] Domain name pointed to server (A/AAAA records)
- [ ] Server with PHP 8.4+, Node.js 22+, Nginx/Caddy
- [ ] MySQL 8.0+ or MariaDB 10.6+ database
- [ ] S3-compatible bucket for listing photos (or use local disk)
- [ ] SMTP mail provider (Mailtrap, SendGrid, Postmark, etc.)
- [ ] PayMongo account with Statement of Acceptance signed

## 2. Environment Setup

```bash
# Clone and install
git clone <repo> /var/www/iskina
cd /var/www/iskina

# Copy & configure environment
cp .env.example .env
php artisan key:generate

# Edit .env with production values:
#   APP_ENV=production
#   APP_DEBUG=false
#   APP_URL=https://iskina.ph
#   DB_* — your MySQL credentials
#   MAIL_* — SMTP details
#   PAYMONGO_* — live keys
#   AWS_* — S3 credentials
#   FILESYSTEM_DISK=s3 (or local)
```

## 3. Application Setup

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --production && npm run build

# Run migrations
php artisan migrate --force

# Seed roles/permissions
php artisan db:seed --class=RoleSeeder --force

# Create admin user
php artisan make:filament-user
# → Use admin@iskina.ph / strong password

# Optimize
php artisan optimize
php artisan view:cache
php artisan event:cache

# Storage link
php artisan storage:link
```

## 4. Queue & Scheduling

```bash
# Supervisor config for queue worker
cat > /etc/supervisor/conf.d/iskina-queue.conf << 'SUPERVISOR'
[program:iskina-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/iskina/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/iskina/storage/logs/queue-worker.log
stopwaitsecs=3600
SUPERVISOR

# Cron entry (every minute)
* * * * * cd /var/www/iskina && php artisan schedule:run >> /dev/null 2>&1
```

## 5. PayMongo Webhook

- **URL:** `https://iskina.ph/webhooks/paymongo`
- **Events:** `payment.paid`
- **Production keys:** Set `PAYMONGO_PUBLIC_KEY` and `PAYMONGO_SECRET_KEY` in `.env`
- **Wallet ID:** Set `PAYMONGO_WALLET_ID` for GCash verification charges

## 6. Post-Deployment Checks

- [ ] Visit `https://iskina.ph/up` → `{"status":"ok"}`
- [ ] Register a test user
- [ ] Verify email flow (check mailtrap/logs)
- [ ] Create a listing (check photo uploads)
- [ ] Test GCash verification flow
- [ ] Test Buy Credits flow
- [ ] Login to `/admin` as admin user
- [ ] Run `php artisan listings:expire` to verify scheduler
- [ ] Check `storage/logs/laravel.log` for errors
- [ ] Configure CDN / Cloudflare if applicable
- [ ] Set up uptime monitoring (betteruptime, pingdom, etc.)

## 7. Monitoring & Maintenance

```bash
# Quick health check
curl -s https://iskina.ph/up

# Check queue worker is running
sudo supervisorctl status iskina-queue:*

# View recent logs
tail -f /var/www/iskina/storage/logs/laravel.log

# Check scheduled task ran
grep "schedule" /var/log/syslog | tail -5

# Clear cache (after config changes)
php artisan optimize:clear && php artisan optimize
```

## 8. Systemd Service Reference (if no Supervisor)

If using systemd instead of Supervisor, the user services from local dev are not for production.
Use system-level services:

```bash
# /etc/systemd/system/iskina-queue.service
[Unit]
Description=Iskina Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/iskina
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
```

## 9. Security

- [ ] Ensure `/admin` route is not publicly indexed (no-robots meta or HTTP auth)
- [ ] Rate limiting: `/webhooks/paymongo` should be limited
- [ ] CORS configured if using separate API subdomain
- [ ] SQLite → MySQL migration tested
- [ ] Password hashing using bcrypt (default)
- [ ] CSRF protection active on all POST routes
- [ ] Set `SESSION_DRIVER=database` (already configured)
- [ ] Disable registration if invite-only at launch
