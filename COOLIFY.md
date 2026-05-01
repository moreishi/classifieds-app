# Coolify Deployment Guide

## Prerequisites

- Coolify instance running (self-hosted or cloud)
- GitHub repo connected: `moreishi/classifieds-app`

## Option 1: Docker Compose (Recommended)

### Step 1: Create Resource
- In Coolify, go to **Resources → New → Docker Compose**
- Select your GitHub repo and branch (`master`)
- Set **Compose file** to `docker-compose.production.yml`

### Step 2: Set Environment Variables
Add these in Coolify's environment tab:

| Variable | Value | Notes |
|---|---|---|
| `APP_KEY` | `base64:...` | Generate: `php -r "echo 'base64:'.base64_encode(random_bytes(32));"` |
| `APP_URL` | `https://your-domain.com` | Your Coolify domain |
| `DB_HOST` | (Coolify internal) | Same private network via `coolify` internal DNS |
| `DB_PORT` | `3306` | |
| `DB_DATABASE` | `iskina` | Whatever you name your DB |
| `DB_USERNAME` | Coolify DB user | |
| `DB_PASSWORD` | Coolify DB password | |
| `APP_PORT` | `80` | Coolify handles port mapping |

### Step 3: Attach MySQL Database
- In Coolify, create a **MySQL** database resource
- Add it as a dependency to this app resource
- Copy the credentials into the app's env vars above

### Step 4: Deploy
Click **Deploy**. The entrypoint will:
1. Create storage symlink
2. Run `php artisan migrate --force --seed` (safe — only runs pending)
3. Start nginx + php-fpm + queue worker via supervisord

## Option 2: Single Dockerfile (Simpler)

### Step 1: Create Resource
- **Resources → New → Dockerfile** (single container)
- Point to GitHub repo, branch `master`
- Set **Build pack** to `Dockerfile`
- Set **Target** to `production`

### Step 2: Add MySQL Database
- Create a MySQL database in Coolify
- Note the internal hostname (usually `mysql` + container hash)

### Step 3: Set Environment Variables
Same as Option 1. Coolify will inject the database credentials.

### Step 4: Port
- Set port mapping to `80`

## Post-Deploy

```bash
# Verify health
curl https://your-domain.com/up
# → {"status":"ok"}

# Login
# → Email: admin@iskina.ph / Password: password
```

## Important Notes

- **No separate Vite container** in production — assets are prebuilt via `npm run build` in the Dockerfile
- **Queue worker** runs inside the same container via supervisord (`autostart=false` by default — set to `true` in production supervisord.conf)
- **Storage** is persistent via Docker volume `iskina_storage`
- **First deploy** runs `migrate --seed` which creates the admin user + sample data
- To run without sample data: set `APP_ENV=production` and the seeder will skip it

## Troubleshooting

### 500 Error on first load
The `.env` isn't used in Docker — all config comes from environment variables. Make sure `APP_KEY` is set.

### Storage link issues
The entrypoint script runs `storage:link --force` on every start. Check `ls -la /app/public/storage`.

### Queue not processing
```bash
docker exec iskina-app supervisorctl status
# Should show: queue:work RUNNING
```
