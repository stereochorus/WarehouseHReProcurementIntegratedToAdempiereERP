#!/bin/bash
set -e

echo "──────────────────────────────────────────"
echo "  WHR-ePIS Demo — Starting up"
echo "──────────────────────────────────────────"

# ── 1. Setup .env ─────────────────────────────
if [ ! -f .env ]; then
    echo "[1/4] .env not found — copying from .env.example"
    cp .env.example .env
else
    echo "[1/4] .env found — skipping copy"
fi

# ── 2. Generate APP_KEY if missing or empty ───
APP_KEY_VALUE=$(grep -E "^APP_KEY=" .env | cut -d= -f2-)
if [ -z "$APP_KEY_VALUE" ] || [ "$APP_KEY_VALUE" = '""' ]; then
    echo "[2/4] Generating APP_KEY..."
    php artisan key:generate --force --ansi
else
    echo "[2/4] APP_KEY already set — skipping"
fi

# ── 3. Clear runtime caches ───────────────────
echo "[3/4] Clearing caches..."
php artisan config:clear   --quiet 2>/dev/null || true
php artisan route:clear    --quiet 2>/dev/null || true
php artisan view:clear     --quiet 2>/dev/null || true

# ── 4. Optimize (production only) ─────────────
if [ "${APP_ENV}" = "production" ]; then
    echo "[4/4] APP_ENV=production — caching config, routes, views..."
    php artisan config:cache --ansi
    php artisan route:cache  --ansi
    php artisan view:cache   --ansi
else
    echo "[4/4] APP_ENV=${APP_ENV:-local} — skipping cache (dev mode)"
fi

echo "──────────────────────────────────────────"
echo "  App ready → http://localhost:8000"
echo "  Login: admin@demo.com / demo123"
echo "──────────────────────────────────────────"

exec "$@"
