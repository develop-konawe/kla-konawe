#!/bin/sh
set -e

echo "=========================================="
echo "    Portal KLA Konawe - Starting Container"
echo "=========================================="

export ENABLE_QUEUE_WORKER="${ENABLE_QUEUE_WORKER:-false}"

# 1. Wait for database connection if DB_HOST is configured and not local
if [ "$DB_CONNECTION" = "mysql" ] && [ -n "$DB_HOST" ] && [ "$DB_HOST" != "127.0.0.1" ] && [ "$DB_HOST" != "localhost" ]; then
    echo "Checking database connection to ${DB_HOST}:${DB_PORT:-3306}..."
    max_retries=30
    counter=0
    until php -r "
        \$host = getenv('DB_HOST');
        \$port = getenv('DB_PORT') ?: 3306;
        \$db   = getenv('DB_DATABASE');
        \$user = getenv('DB_USERNAME');
        \$pass = getenv('DB_PASSWORD');
        try {
            \$pdo = new PDO(\"mysql:host=\$host;port=\$port;dbname=\$db\", \$user, \$pass, [
                PDO::ATTR_TIMEOUT => 2,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            exit(0);
        } catch (Exception \$e) {
            exit(1);
        }
    " 2>/dev/null; do
        counter=$((counter + 1))
        if [ $counter -ge $max_retries ]; then
            echo "Warning: Database connection timeout after ${max_retries}s. Proceeding anyway..."
            break
        fi
        echo "Waiting for database to be ready... (${counter}/${max_retries})"
        sleep 2
    done
    if [ $counter -lt $max_retries ]; then
        echo "Database connection successful!"
    fi
fi

# 2. Ensure required storage and cache directories exist
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/app/public \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# 3. Create storage symlink & discover packages
php artisan storage:link --force 2>/dev/null || true
php artisan package:discover --ansi 2>/dev/null || true

# 4. Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 5. Run automatic migrations if requested
if [ "${AUTO_MIGRATE:-false}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration encountered an error, continuing..."
fi

# 6. Production cache optimizations
if [ "${APP_ENV:-production}" = "production" ] && [ "${APP_OPTIMIZE:-true}" = "true" ]; then
    echo "Caching configuration, routes, and views..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "All initialization steps complete. Starting processes..."
exec "$@"

