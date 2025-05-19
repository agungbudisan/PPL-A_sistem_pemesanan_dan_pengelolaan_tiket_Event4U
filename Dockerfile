# ========== Build Stage ==========
FROM php:8.2-fpm AS builder

RUN apt update && apt install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libpq-dev \
    && apt clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install

RUN php artisan storage:link || true

# ========== Runtime Stage ==========
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    git \
    curl \
    libpng \
    libxml2 \
    zlib \
    libzip \
    oniguruma \
    libpq \
    && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libpng-dev \
    libxml2-dev \
    zlib-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip \
    && apk del .build-deps

WORKDIR /var/www

COPY --from=builder /var/www /var/www
COPY --from=builder /usr/bin/composer /usr/bin/composer

COPY docker-utils/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker-utils/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker-utils/supervisord.conf /etc/supervisord.conf

RUN mkdir -p /var/www/storage/app/public \
    && mkdir -p /var/www/bootstrap/cache \
    && mkdir -p /var/www/public/storage \
    && ln -sf /var/www/storage/app/public /var/www/public/storage \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/vendor /var/www/public

RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

# Run Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
