# IskinaPH — Single container for Coolify + Cloudflare
# Nginx + PHP-FPM + Supervisor (queue + scheduler)

ARG PHP_VERSION=8.4

# ---- Build stage ----
FROM php:${PHP_VERSION}-fpm-alpine AS build

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apk add --no-cache \
        libzip-dev \
        libpng-dev \
        oniguruma-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libjpeg-turbo \
        icu-dev \
        linux-headers \
        git \
        unzip \
        curl \
        npm \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        ctype \
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
    && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader \
    && npm ci --production \
    && npm run build

COPY . .
RUN php artisan storage:link \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ---- Production stage ----
FROM php:${PHP_VERSION}-fpm-alpine AS production

WORKDIR /app

# Install nginx, supervisor, and runtime deps
RUN apk add --no-cache \
        nginx \
        supervisor \
        libpng \
        libjpeg-turbo \
        libjpeg-turbo-dev \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
        freetype-dev \
        icu-dev \
        linux-headers \
        curl \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        ctype \
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
    && rm -rf /var/cache/apk/*

COPY --from=build --chown=www-data:www-data /app /app

# Config files
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh \
    && mkdir -p /run/nginx

HEALTHCHECK --interval=30s --timeout=3s --start-period=15s \
    CMD curl -f http://localhost:8080/up || exit 1

USER www-data

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
