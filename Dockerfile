FROM php:8.4-fpm-alpine

# Install system deps
RUN apk add --no-cache \
 bash \
 curl \
 git \
 unzip \
 libzip-dev \
 libpng-dev \
 libjpeg-turbo-dev \
 freetype-dev \
 icu-dev \
 oniguruma-dev \
 libxml2-dev \
 linux-headers \
 nodejs \
 npm \
 nginx \
 supervisor

# PHP extensions
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
 pdo \
 pdo_mysql \
 mbstring \
 exif \
 pcntl \
 bcmath \
 gd \
 intl \
 zip \
 sockets

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy files
COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install frontend deps
RUN npm install && npm run build

# Laravel optimizations
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# ---------- NGINX CONFIG ----------
COPY docker/nginx.conf /etc/nginx/nginx.conf

# ---------- SUPERVISOR ----------
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
