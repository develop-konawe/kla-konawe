# ==============================================================================
# Stage 1: Build Frontend Assets (Vite + React + Tailwind)
# ==============================================================================
FROM node:20-alpine AS node_builder

WORKDIR /app

COPY package*.json ./
RUN npm install --legacy-peer-deps

COPY vite.config.ts tsconfig.json ./
COPY resources ./resources
COPY public ./public

RUN npm run build

# ==============================================================================
# Stage 2: Install Composer Dependencies
# ==============================================================================
FROM composer:2 AS composer_builder

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs

# ==============================================================================
# Stage 3: Production Runtime (PHP 8.3-FPM + Nginx + Supervisor on Alpine)
# ==============================================================================
FROM php:8.3-fpm-alpine AS runtime

LABEL maintainer="Diskominfo Konawe"
LABEL description="Portal KLA (Kabupaten Layak Anak) Konawe"

# 1. Install System Dependencies & Tools
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    curl \
    mariadb-client \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    shadow \
    && rm -rf /var/cache/apk/*

# 2. Configure & Install PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        intl \
        zip \
        opcache \
        gd

# 3. Setup Configurations
COPY docker/php/php.ini $PHP_INI_DIR/conf.d/99-custom.ini
COPY docker/php/opcache.ini $PHP_INI_DIR/conf.d/opcache.ini
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

# 4. Setup Working Directory
WORKDIR /var/www/html

# 5. Copy Application Source Code
COPY . .

# 6. Copy Built Assets & Dependencies from Previous Stages
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=node_builder /app/public/build ./public/build

# 7. Create Storage Directory Structure & Set Permissions
RUN mkdir -p storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/app/public \
             storage/logs \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 8. Container Metadata & Healthcheck
EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

