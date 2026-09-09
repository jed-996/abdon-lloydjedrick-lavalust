#!/bin/sh
set -eu
: "${APP_KEY:?Set APP_KEY in Render environment variables}"
: "${ADMIN_EMAIL:?Set ADMIN_EMAIL in Render environment variables}"
: "${ADMIN_PASSWORD_HASH:?Set ADMIN_PASSWORD_HASH in Render environment variables}"
mkdir -p runtime/session runtime/logs runtime/cache
chown -R www-data:www-data runtime
RENDER_PORT="${PORT:-10000}"
sed -ri "s/^Listen [0-9]+$/Listen ${RENDER_PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${RENDER_PORT}>/" /etc/apache2/sites-available/000-default.conf
php scripts/setup.php
exec apache2-foreground
