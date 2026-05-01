# Coolify Deployment Guide

## Prerequisites

- Coolify instance running (self-hosted or cloud)
- GitHub repo connected: `moreishi/classifieds-app`
- Branch: `master`

## Quick Deploy (Nixpacks)

### Step 1: Create Resource
- **Resources → New → Application**
- Select repo and branch
- **Build pack**: **Nixpacks** (auto-detected from `composer.json`)
- Leave **Build target** empty

### Step 2: Set Environment Variables
Under **Environment** tab, add:

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
- Create a **MySQL** database resource
- Add as dependency to this app
- Copy host/user/pass into the env vars above

### Step 4: Post-Deployment Commands
In Coolify's **Commands** section, set:
```
php artisan migrate --force
php artisan storage:link --force
```

### Step 5: Queue Worker
Nixpacks runs one container (nginx + php-fpm only). For the queue worker, add a **separate Coolify service**:
- Create **Resources → New → Service**
- Use same image, command: `php /app/artisan queue:work --sleep=3 --tries=3 --max-time=3600`
- No nginx, no exposed ports

### Step 6: Deploy
Click **Deploy**. Nixpacks will:
1. Detect PHP 8.4 from `composer.json`
2. Install required extensions (gd, zip, intl, bcmath, etc.) from `ext-*` platform requirements
3. Set server root to `/app/public`
4. Run `composer install`
5. Run `npm install` + `npm run build`
6. Start nginx with Laravel fallback (index.php)

Verify:
```bash
curl https://your-domain.com/up
# → {"status":"ok"}
```

## Alternative: Docker Build Pack

For single-container with built-in queue worker (supervisord):

- **Build pack**: **Dockerfile**
- **Build target**: `production`
- Same env vars as above
- Entrypoint auto-runs `migrate --seed` on first start
- Has queue worker + nginx + php-fpm in one container

## Troubleshooting

### composer install fails (exit code 1)
Nixpacks needs `ext-*` declared in `composer.json` `require` section to know which PHP packages to install. If you add new packages that need more extensions, add them there too.

### 500 Error
Missing `APP_KEY` or wrong value. Regenerate and set.

### Storage link broken
Run manually: `docker exec <container> php /app/artisan storage:link --force`

### Queue not processing
If using Dockerfile build pack, check supervisord. If using Nixpacks, you need a separate queue worker service.
