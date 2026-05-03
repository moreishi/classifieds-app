# IskinaPH — Docker Deployment

## Quick Start

```bash
# Clone and enter
git clone <repo>
cd classifieds-app

# Copy env
cp .env.example .env

# Build and start
docker compose up -d

# Seed admin user
docker compose exec app php artisan db:seed --class=RoleSeeder --force

# Visit
open http://localhost
```

## Services

| Service | Purpose | Port |
|---------|---------|------|
| `nginx` | HTTP server | 80, 443 |
| `app` | PHP-FPM (Laravel) | 9000 |
| `queue` | Queue worker | — |
| `scheduler` | Laravel scheduler | — |
| `mysql` | MariaDB 11 | 3306 |

## Environment Variables

Set these in your `.env` file:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_KEY=base64:...              # Generate: php artisan key:generate

# These are auto-mapped by docker-compose.yml:
# DB_HOST=mysql
# DB_PORT=3306
# DB_DATABASE=iskina
# DB_USERNAME=iskina
# DB_PASSWORD=<set in .env>
# MYSQL_ROOT_PASSWORD=<set in .env>

PAYMONGO_PUBLIC_KEY=pk_live_xxx
PAYMONGO_SECRET_KEY=sk_live_xxx
PAYMONGO_WALLET_ID=wal_xxx

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@iskina.ph
```

## Production

For production, generate `APP_KEY` before starting:

```bash
docker compose run --rm app php artisan key:generate --force
docker compose up -d
```

## Commands

```bash
# Build (no cache)
docker compose build --no-cache

# Start
docker compose up -d

# Stop
docker compose down

# Logs
docker compose logs -f app
docker compose logs -f queue

# Run artisan
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --class=RoleSeeder --force
```

## Volumes

- `mysql_data` — persists database
- `storage_data` — persists uploads, logs, session files
