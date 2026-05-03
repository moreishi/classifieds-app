# IskinaPH — Single container for Coolify + Cloudflare
# Nginx + PHP-FPM + Supervisor (queue + scheduler)

ARG PHP_VERSION=8.4

# ---- Build stage ----
FROM php:${PHP_VERSION}-fpm-alpine AS build

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apk add --no-cache --virtual .build-deps \
        libzip-dev \
        libpng-dev \
        oniguruma-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        icu-dev \
        libxml2-dev \
        linux-headers \
    && apk add --no-cache \
        libjpeg-turbo \
        git \
        unzip \
        curl \
        npm \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        ctype \
        exif \
        fileinfo \
        gd \
        intl \
        mbstring \
        pdo \
        pdo_mysql \
        sockets \
        xml \
        zip \
        opcache \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy everything
COPY . .

# Debug: show PHP and Composer info (remove later if you want)
RUN php -v && echo "---" && php -m && echo "---" && composer --version

# Install dependencies with platform reqs ignored (safety fallback)
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --ignore-platform-req=ext-sockets \
    2>&1 || \
    composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --ignore-platform-reqs \
    2>&1

# Install and build frontend
RUN npm ci --production 2>&1 || npm install --production 2>&1
RUN npm run build 2>&1 || echo "Frontend build skipped - continuing anyway"

# Laravel setup  
RUN php artisan storage:link || true \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ---- Production stage ----
FROM php:${PHP_VERSION}-fpm-alpine AS production

WORKDIR /app

RUN apk add --no-cache --virtual .build-deps \
        libjpeg-turbo-dev \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
        freetype-dev \
        icu-dev \
        libxml2-dev \
        linux-headers \
    && apk add --no-cache \
        nginx \
        supervisor \
        libpng \
        libjpeg-turbo \
        curl \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        ctype \
        exif \
        fileinfo \
        gd \
        intl \
        mbstring \
        pdo \
        pdo_mysql \
        sockets \
        xml \
        zip \
        opcache \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/*

# Copy built application
COPY --from=build --chown=www-data:www-data /app /app

# Configuration files
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

# Setup and permissions
RUN chmod +x /entrypoint.sh \
    && mkdir -p /run/nginx \
    && mkdir -p /var/log/supervisor \
    && chown -R www-data:www-data /var/log/supervisor

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=15s \
    CMD curl -f http://localhost:8080/up || exit 1

USER www-data

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
