#!/bin/bash
# Docker end-to-end test script
# Run this from the project root on a machine with Docker installed

set -e

echo "=== 1. Fresh clone ==="
cd /tmp
rm -rf classifieds-test
git clone git@github.com:moreishi/classifieds-app.git classifieds-test
cd classifieds-test

echo "=== 2. Docker compose build ==="
docker compose build --no-cache 2>&1 | tail -5

echo "=== 3. Docker compose up ==="
docker compose up -d 2>&1

echo "=== 4. Wait for services ==="
sleep 15

echo "=== 5. Check containers ==="
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

echo "=== 6. Check app health ==="
curl -s -o /dev/null -w "%{http_code}" http://localhost:8200
echo ""

echo "=== 7. Check queue worker ==="
docker logs classifieds-queue --tail 5 2>&1

echo "=== 8. Check Vite ==="
curl -s -o /dev/null -w "%{http_code}" http://localhost:5173 2>/dev/null || echo "5173 unreachable (expected if Vite hasn't started yet)"

echo "=== 9. Login test ==="
COOKIE="/tmp/docker-cookie.txt"
CSRF=$(curl -s -c "$COOKIE" "http://localhost:8200/login" | sed -n 's/.*_token" value="\([^"]*\)".*/\1/p')
curl -s -L -b "$COOKIE" -c "$COOKIE" -d "_token=$CSRF&email=admin@iskina.ph&password=password&remember=" "http://localhost:8200/login" -o /dev/null -w "Login: %{http_code}\n"

echo "=== 10. Core routes ==="
for path in "/" "/search?q=phone" "/dashboard" "/listings/create" "/offers" "/notifications"; do
  CODE=$(curl -s -o /dev/null -w "%{http_code}" -b "$COOKIE" "http://localhost:8200$path")
  echo "  $path → $CODE"
done

echo "=== 11. Admin panel ==="
curl -s -o /dev/null -w "admin: %{http_code}\n" -b "$COOKIE" "http://localhost:8200/admin"

echo "=== 12. Cleanup ==="
docker compose down -v
rm -f "$COOKIE"

echo ""
echo "=== RESULTS ==="
echo "Pass if: Login=200, all routes=200, admin=200, containers all 'Up'"
