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
COPY . .
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader
RUN npm ci || npm install
RUN npm run build || echo "Frontend build skipped"
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
