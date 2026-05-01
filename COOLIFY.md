# Coolify Deployment Guide

## Prerequisites

- Coolify instance running (self-hosted or cloud)
- GitHub repo connected: `moreishi/classifieds-app`

## Option 1: Nixpacks (Recommended)

Zero-config build. No Dockerfile needed.

### Step 1: Create Resource
- In Coolify, go to **Resources → New → Application**
- Select your GitHub repo and branch (`master`)
- Set **Build pack** to **Nixpacks** (auto-detected from `composer.json`)

### Step 2: Set Environment Variables
Add these in Coolify's environment tab:

| Variable | Value | Notes |
|---|---|---|
| `APP_KEY` | `base64:...` | Generate: `php -r "echo 'base64:'.base64_encode(random_bytes(32));"` |
| `APP_URL` | `https://your-domain.com` | Your Coolify domain |
| `APP_ENV` | `production` | |
| `APP_DEBUG` | `false` | |
| `DB_CONNECTION` | `mysql` | |
| `DB_HOST` | Coolify DB hostname | Match your MySQL service |
| `DB_PORT` | `3306` | |
| `DB_DATABASE` | `iskina` | |
| `DB_USERNAME` | Coolify DB user | |
| `DB_PASSWORD` | Coolify DB password | |
| `QUEUE_CONNECTION` | `database` | |
| `SESSION_DRIVER` | `database` | |
| `MAIL_MAILER` | `log` | Or `smtp` with credentials |
| `QUEUE_CONVERSIONS_BY_DEFAULT` | `false` | Prevents Spatie Media temp file issues |

### Step 3: Attach MySQL Database
- Create a **MySQL** database resource in Coolify
- Add it as a dependency to the app
- Copy credentials into the env vars above

### Step 4: Post-Deployment Command
In Coolify's **Commands** section, add:
```bash
php artisan migrate --force
```

### Step 5: Queue Worker (Separate Service)
Nixpacks runs a single container (nginx + php-fpm). For the queue worker, you need an additional service:

**Option A: Coolify Service (Recommended)**
- Create a new **Docker Image** resource
- Use the same image Coolify builds for your app
- Override command to: `php /app/artisan queue:work --sleep=3 --tries=3 --max-time=3600`
- No nginx needed, no exposed port

**Option B: Docker Compose (Advanced)**
- Keep the `docker-compose.production.yml` in the repo
- Set **Docker Compose** build pack

### Step 6: Deploy
Click **Deploy**. Nixpacks will:
1. Detect PHP 8.4 from `composer.json`
2. Set server root to `/app/public`
3. Run `composer install`
4. Run `npm install` + `npm run build`
5. Start nginx with Laravel fallback

Then verify:
```bash
curl https://your-domain.com/up
# → {"status":"ok"}
```

## Option 2: Docker Build Pack

For single-container deployment with built-in queue worker.

### Step 1: Create Resource
- **Resources → New → Application**
- Set **Build pack** to **Dockerfile**
- Set **Target** to `production`

### Step 2: Add MySQL Database
Same as Option 1.

### Step 3: Set Environment Variables
Same vars as Option 1. The entrypoint will run `migrate --seed` automatically.

### Step 4: Deploy
Click **Deploy**. The image will:
1. Build with multi-stage Dockerfile
2. Run `npm run build` for assets
3. Start with supervisord (nginx + php-fpm + queue worker)

## Option 3: Docker Compose (Legacy)

If you need strict multi-container separation:
- Set **Compose file** to `docker-compose.production.yml`
- Provides separate nginx, queue, and vite containers
- Full env vars needed for each service

## Important Notes

- **No `.env` file** — all config comes from Coolify environment variables
- **First deploy** needs `php artisan migrate --force` (manual post-deployment command or entrypoint handles it)
- **Admin user**: `admin@iskina.ph` / `password` — created by seeding
- **Storage** is ephemeral in Nixpacks — use S3 or external volume for uploaded files in production
- **Sample data**: to seed, set `SEED_SAMPLE_DATA=true` during first deploy only

## Troubleshooting

### 500 Error on first load
Make sure `APP_KEY` is set correctly (must be valid base64 with `base64:` prefix).

### Storage link issues
```bash
docker exec <container> php /app/artisan storage:link --force
```

### Queue not processing
Check that the queue worker service is running:
```bash
docker exec <container> php /app/artisan queue:listen --tries=3
```
