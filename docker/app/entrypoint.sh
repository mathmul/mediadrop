#!/usr/bin/env sh
set -e

APP_DIR=/var/www/html

# ---------- Composer env & home ----------
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_MAX_PARALLEL_HTTP=1
export COMPOSER_PROCESS_TIMEOUT=1200
export COMPOSER_HOME=/home/www-data/.composer

mkdir -p "$COMPOSER_HOME"

cd "$APP_DIR"

# ---------- Writable dirs & ownership ----------
# create once, then set proper owner/perms for mounted volumes
mkdir -p vendor storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data vendor storage bootstrap/cache "$COMPOSER_HOME"
chmod -R 775 storage bootstrap/cache || true

# ---------- Safe composer install with retry (no scripts) ----------
composer_safe_install() {
  local tries=0
  until [ $tries -ge 3 ]
  do
    if su-exec www-data:www-data composer install \
          --prefer-dist --no-interaction --no-progress --no-scripts; then
      return 0
    fi
    echo "Composer install failed; clearing cache and retrying ($((tries+1))/3)…"
    su-exec www-data:www-data composer clear-cache || true
    sleep 2
    tries=$((tries+1))
  done
  return 1
}

if [ ! -f vendor/autoload.php ]; then
  echo "Installing Composer dependencies (no scripts)…"
  composer_safe_install || { echo "Composer install failed after retries"; exit 1; }
fi

# ---------- Verify critical classes & self-heal Pest if needed ----------
php -r "require 'vendor/autoload.php';
\$need=[];
foreach ([
  'Illuminate\\Foundation\\Application',  // Laravel core
  'PHPUnit\\Framework\\TestCase',         // PHPUnit base
  'Laravel\\Sanctum\\Sanctum'             // Sanctum
] as \$cls) { if (!class_exists(\$cls)) \$need[]=\$cls; }
if (\$need) { fwrite(STDERR, 'Missing classes: '.implode(', ', \$need).\"\\n\"); exit(2); }" || {
  echo "Detected missing classes after install; retrying composer install…"
  composer_safe_install || { echo "Composer install failed after retries"; exit 1; }
}

# Pest plugin (dev) – optional self-heal for DX (won’t block runtime)
php -r "require 'vendor/autoload.php'; exit(class_exists('Pest\\\Plugin\\\Loader')?0:2);" || {
  echo "Pest plugin not found; attempting to install (dev)…"
  su-exec www-data:www-data composer require pestphp/pest-plugin:^4 --dev --no-interaction --no-scripts || true
}

# Always optimize autoload after dependency changes
su-exec www-data:www-data composer dump-autoload -o || true

# ---------- App env & key ----------
# Ensure .env exists before key:generate
if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

# Generate key only if empty / not present
if ! grep -q '^APP_KEY=.\+' .env; then
  su-exec www-data:www-data php artisan key:generate --force || true
else
  echo "APP_KEY already set; skipping key:generate"
fi

# ---------- Framework bootstrap ----------
# Link storage (idempotent)
su-exec www-data:www-data php artisan storage:link || true

# Discover packages (safe & fast)
su-exec www-data:www-data php artisan package:discover --ansi || true

# Hand off to PHP-FPM
exec "$@"
