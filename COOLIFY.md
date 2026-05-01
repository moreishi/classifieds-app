# Coolify Deployment Guide

## Quick Deploy (Nixpacks — Zero Config)

### Step 1: Create Resource
- **Resources → New → Application**
- Select your repo, branch `master`
- **Build pack**: **Nixpacks** (auto-detected, no config needed)

### Step 2: Set Environment Variables
Under **Environment** tab:

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
```

### Step 5: Queue Worker
Nixpacks runs a single container (nginx + php-fpm only). Queue worker needs a separate service:
- Create a new Coolify **Service** (Docker Image)
- Command: `php /app/artisan queue:work --sleep=3 --tries=3 --max-time=3600`
- No ports needed

### Step 6: Deploy
That's it. Nixpacks detects PHP from `composer.json`, installs extensions from `ext-*` requirements, runs `composer install` + `npm run build`, starts nginx with Laravel routing.

## Alternative: Dockerfile (All-in-One)

For single-container with built-in queue worker, use **Dockerfile** build pack with target `production`.

## Troubleshooting

**composer install fails?** Add missing `ext-*` to `composer.json` require section.

**500?** `APP_KEY` missing or invalid. Regenerate.

**Storage link?** `php artisan storage:link --force` in post-deployment commands.

**Queue?** Needs separate worker service with Nixpacks.
