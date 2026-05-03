# Coolify Deployment Guide (Dockerfile)

> **Single container** with Nginx + PHP-FPM + Supervisor (queue + scheduler)  
> **Port:** 8080  
> **Health check:** `/up`

## Quick Deploy

### Step 1: Create Resource
- **Resources → New → Application**
- Select your repo, branch `master`
- **Build pack**: **Dockerfile**
- **Port:** `8080`
- **Health check path:** `/up`

### Step 2: Set Environment Variables

Under **Environment** tab, add all of these:

```env
# Laravel
APP_KEY=base64:...
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=<coolify-mysql-host>
DB_PORT=3306
DB_DATABASE=iskina
DB_USERNAME=iskina
DB_PASSWORD=...

# Session
SESSION_DRIVER=database
SESSION_DOMAIN=.your-domain.com

# Queue
QUEUE_CONNECTION=database

# Media
FILESYSTEM_DISK=public
QUEUE_CONVERSIONS_BY_DEFAULT=false

# PayMongo
PAYMONGO_PUBLIC_KEY=pk_live_xxx
PAYMONGO_SECRET_KEY=sk_live_xxx
PAYMONGO_WALLET_ID=wal_xxx

# Mail
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=noreply@iskina.ph
MAIL_FROM_NAME="Iskina.ph"
```

Generate APP_KEY:
```bash
php -r "echo 'base64:'.base64_encode(random_bytes(32));"
```

### Step 3: Attach MySQL Database
- Create a MySQL/MariaDB database in Coolify
- Add as dependency
- Copy host/credentials into the env vars above

### Step 4: Storage Persistence

Add a persistent volume for uploaded files:
- **Mount path:** `/app/storage`
- This keeps user uploads across deployments

If using S3 instead, skip this and set `FILESYSTEM_DISK=s3` with AWS credentials.

### Step 5: Deploy

That's it. The entrypoint handles:
1. Wait for MySQL
2. Generate `APP_KEY` if missing
3. Run migrations
4. Seed admin user (idempotent)
5. Start nginx + php-fpm + queue worker + scheduler via Supervisor

### Accounts
- **Admin:** `admin@iskina.ph` / `password` (change immediately)
- Always email-verified (set by `RoleSeeder`)

## PayMongo Webhook

After deployment, register this webhook with PayMongo:
- **URL:** `https://your-domain.com/webhooks/paymongo`
- **Event:** `payment.paid`
- The endpoint has no CSRF and no auth by design — rate limit recommended

## Troubleshooting

**Build fails?** Check Coolify has enough memory. Docker multi-stage builds need ~2GB.

**Health check fails?** Check DB credentials in env vars. The nginx health endpoint is `/up`.

**500 errors?** `APP_KEY` missing or invalid — regenerate.

**Storage not persisting?** Add a persistent volume mount for `/app/storage`.

**Queue not processing?** Supervisor starts the queue worker automatically. Check Coolify logs.

**Scheduler not running?** Supervisor starts `schedule:work` automatically — no cron needed.

## Updating

On redeploy, the entrypoint runs:
- `php artisan migrate --force` (safe — idempotent)
- `php artisan db:seed --class=RoleSeeder --force` (safe — uses `firstOrCreate`)
- `php artisan optimize` (caches routes, config, events)
