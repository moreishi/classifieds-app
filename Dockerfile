# Multi-stage build for IskinaPH
# Uses PHP 8.4 FPM with Nginx

ARG PHP_VERSION=8.4

# ---- Build stage ----
FROM php:${PHP_VERSION}-fpm-alpine AS build

WORKDIR /app

# PHP extensions required by composer.json
RUN apk add --no-cache \
        libzip-dev \
        libpng-dev \
        oniguruma-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        linux-headers \
        git \
        unzip \
        curl \
        npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
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

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy only dependency files first for layer caching
COPY composer.json composer.lock package.json package-lock.json ./

# Install dependencies (production only)
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader \
    && npm ci --production \
    && npm run build

# Copy application source
COPY . .

# Generate optimized cache (requires env, done at runtime entrypoint)
RUN php artisan storage:link \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ---- Production stage ----
FROM php:${PHP_VERSION}-fpm-alpine AS production

WORKDIR /app

# Runtime extensions only
RUN apk add --no-cache libpng-dev libzip-dev oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
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

# Copy from build stage
COPY --from=build --chown=www-data:www-data /app /app

# OPcache config
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

# Health check
HEALTHCHECK --interval=60s --timeout=3s --start-period=10s \
    CMD curl -f http://localhost:9000/ping || exit 1

USER www-data

ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["php-fpm"]
