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

# Install Composer deps as www-data (owned correctly), with retry
if [ ! -f vendor/autoload.php ]; then
  echo "Installing Composer dependencies..."
  su-exec www-data:www-data composer clear-cache || true

  # 1st attempt (no progress, 1 parallel stream to be gentle)
  if ! COMPOSER_MAX_PARALLEL_HTTP=1 su-exec www-data:www-data composer install --prefer-dist --no-interaction --no-progress; then
    echo "Composer install failed; retrying after cache clear..."
    su-exec www-data:www-data composer clear-cache || true
    sleep 2
    # 2nd attempt (still conservative)
    COMPOSER_MAX_PARALLEL_HTTP=1 su-exec www-data:www-data composer install --prefer-dist --no-interaction --no-progress
  fi
fi

# Ensure .env exists
if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

# Generate key only if empty
if ! grep -q '^APP_KEY=.\+' .env; then
  su-exec www-data:www-data php artisan key:generate --force || true
else
  echo "APP_KEY already set; skipping key:generate"
fi

# Storage symlink
su-exec www-data:www-data php artisan storage:link || true

# Hand off to PHP-FPM (still root, but php-fpm runs workers as www-data)
exec "$@"
