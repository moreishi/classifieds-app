# Coolify Deployment Guide

> **Stack:** Laravel 13.7, PHP 8.4, Livewire 4, Filament 5  
> **PHP constraint:** `^8.4` (locked in composer.json + composer.lock)  
> **Branch:** `master` (production), `develop` (staging)

## Quick Deploy (Nixpacks — Zero Config)

### Step 1: Create Resource
- **Resources → New → Application**
- Select your repo, branch `master`
- **Build pack**: **Nixpacks** (auto-detected, no config needed)

### Step 2: Set Environment Variables

| Variable | Value | Required |
|---|---|---|
| `APP_KEY` | `base64:...` | ✅ |
| `APP_URL` | `https://your-domain.com` | ✅ |
| `APP_ENV` | `production` | ✅ |
| `APP_DEBUG` | `false` | ✅ |
| `DB_CONNECTION` | `mysql` | ✅ |
| `DB_HOST` | Coolify DB hostname | ✅ |
| `DB_PORT` | `3306` | |
| `DB_DATABASE` | `iskina` | ✅ |
| `DB_USERNAME` | Coolify DB user | ✅ |
| `DB_PASSWORD` | Coolify DB password | ✅ |
| `QUEUE_CONNECTION` | `database` | ✅ |
| `SESSION_DRIVER` | `database` | ✅ |
| `QUEUE_CONVERSIONS_BY_DEFAULT` | `false` | ✅ |

Generate APP_KEY:
```bash
php -r "echo 'base64:'.base64_encode(random_bytes(32));"
```

### Step 3: Attach MySQL Database
- Create a MySQL database in Coolify
- Add as dependency → copy credentials into env vars

### Step 4: Post-Deployment Commands
In **Commands** section:
```bash
php artisan migrate --force
php artisan storage:link --force
php artisan db:seed --class=RoleSeeder --force
```

### Step 5: Queue Worker
Nixpacks runs a single container (nginx + php-fpm only). Queue worker needs a separate service:
- Create a new Coolify **Service** (Docker Image)
- Command: `php /app/artisan queue:work --sleep=3 --tries=3 --max-time=3600`
- No ports needed

### Step 6: Scheduler
Add a cron job or a second service:
- Command: `php /app/artisan schedule:work`
- No ports needed

### Step 7: Deploy
That's it. Nixpacks detects PHP, installs extensions from `composer.json`, runs `composer install` + `npm run build`, starts nginx with Laravel routing.

### Accounts Created
- **Admin:** `admin@iskina.ph` / `password` (change immediately)
  - Created by `RoleSeeder`, always email-verified
  - On re-seed: `firstOrCreate` + `email_verified_at` guard

## PayMongo Webhook

After deployment, register webhook:
- **URL:** `https://your-domain.com/webhooks/paymongo`
- **Event:** `payment.paid`
- Note: webhook endpoint has no CSRF and no auth by design

Set env vars:
```env
PAYMONGO_PUBLIC_KEY=pk_live_xxx
PAYMONGO_SECRET_KEY=sk_live_xxx
PAYMONGO_WALLET_ID=wal_xxx
```

## SMTP

Configure mail in Coolify env vars:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@iskina.ph
MAIL_FROM_NAME="Iskina.ph"
```

## Troubleshooting

**composer install fails?** Add missing `ext-*` to `composer.json` require section.

**PHP version mismatch?** Constraint is `^8.4`. Ensure Coolify image has PHP 8.4+.

**500 error?** `APP_KEY` missing or invalid. Regenerate.

**Storage link?** `php artisan storage:link --force` in post-deployment commands.

**Queue?** Needs separate worker service with Nixpacks.

**Scheduler?** Needs separate service or cron job.

**Media table error?** The `create_media_table` migration has a `Schema::hasTable()` guard — safe to run repeatedly.
