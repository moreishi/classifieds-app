FROM php:8.4-fpm-alpine AS base

RUN apk add --no-cache \
    bash \
    git \
    curl \
    mysql-client \
    nginx \
    supervisor \
    nodejs \
    npm \
    linux-headers \
    oniguruma-dev \
    libxml2-dev \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    $PHPIZE_DEPS

# PHP extensions
RUN docker-php-ext-install \
    mbstring \
    pdo \
    pdo_mysql \
    xml \
    bcmath \
    gd \
    opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]

# Nginx config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# PHP config
COPY docker/php.ini /usr/local/etc/php/conf.d/classifieds.ini
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

# ── Development stage ──────────────────────────────────
FROM base AS development

# Install dev dependencies
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN apk add --no-cache vim

# Composer install (dev deps included) on first run
COPY composer.json composer.lock ./
RUN composer install --no-interaction

COPY package.json package-lock.json ./
RUN npm ci

# Copy the rest
COPY . .

# Storage bootstrap (build-time only — runtime handled by entrypoint)
RUN mkdir -p storage/framework/{sessions,views,cache/data} storage/logs && \
    chown -R www-data:www-data storage bootstrap/cache database

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# ── Production stage ──────────────────────────────────
FROM base AS production

COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-dev

COPY package.json package-lock.json ./
RUN npm ci && npm run build

COPY . .

RUN mkdir -p storage/framework/{sessions,views,cache/data} storage/logs && \
    chown -R www-data:www-data storage bootstrap/cache database

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
