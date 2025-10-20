#!/usr/bin/env sh
set -e

APP_DIR=/var/www/html
export COMPOSER_ALLOW_SUPERUSER=1

cd "$APP_DIR"

# --- Prepare writable dirs ---
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache vendor

# Ensure correct ownership & perms for mounted volumes
chown -R www-data:www-data storage bootstrap/cache vendor
chmod -R 775 storage bootstrap/cache vendor || true

# --- Install Composer deps as www-data (so files are owned correctly) ---
if [ ! -f vendor/autoload.php ]; then
  su-exec www-data:www-data composer install --prefer-dist --no-interaction
fi

# App key
su-exec www-data:www-data php artisan key:generate --force || true

# Storage symlink
su-exec www-data:www-data php artisan storage:link || true

# Hand off to PHP-FPM (still root, but php-fpm runs workers as www-data)
exec "$@"
