# IskinaPH — Docker

## Production (Coolify + Cloudflare)

The `Dockerfile` builds a **single container** with:
- Nginx (port 8080)
- PHP-FPM 8.4
- Supervisor (manages nginx + fpm + queue worker + scheduler)
- Cloudflare `CF-Connecting-IP` support built in

### Deploy on Coolify

1. **Create Resource → Application**
2. **Build pack:** Dockerfile
3. **Port:** `8080`
4. **Health check:** `/up`
5. **Environment variables:** Add all `.env` vars (DB, PayMongo, Mail, etc.)
6. **Attach MySQL** from Coolify marketplace
7. **Done** — entrypoint auto-runs migrations + seeds on first boot

### Required Environment Variables

```env
APP_KEY=base64:...
APP_ENV=production
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=<coolify-mysql-host>
DB_PORT=3306
DB_DATABASE=iskina
DB_USERNAME=iskina
DB_PASSWORD=...

PAYMONGO_PUBLIC_KEY=pk_live_xxx
PAYMONGO_SECRET_KEY=sk_live_xxx
PAYMONGO_WALLET_ID=wal_xxx

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=noreply@iskina.ph
```

### Cloudflare Notes

- Nginx reads `CF-Connecting-IP` header for real visitor IPs
- `real_ip_header` is set — no additional config needed
- SSL terminates at Cloudflare (container listens on HTTP)
- Add Cloudflare's origin pull cert if doing end-to-end TLS

---

## Local Development (docker-compose)

```bash
# Build and start
docker compose up -d

# Seed admin
docker compose exec app php artisan db:seed --class=RoleSeeder --force

# Visit
open http://localhost:8200
```

Separate services for dev (with live code mount):
- `app` → `php artisan serve` on port 8200
- `queue` → queue worker
- `scheduler` → schedule worker
- `mysql` → MariaDB 11 on port 3307

### Local Dev Commands

```bash
docker compose up -d                  # Start
docker compose logs -f app            # Follow app logs
docker compose exec app bash          # Shell into app
docker compose exec app php artisan tinker
docker compose down                   # Stop
docker compose down -v                # Stop + wipe volumes
```
