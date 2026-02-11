FROM php:8.3-fpm-alpine

RUN apk add --no-cache icu-dev oniguruma-dev libzip-dev zip unzip git \
  && docker-php-ext-install intl pdo_mysql opcache zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Si Symfony est déjà en place :
RUN composer install --no-dev --optimize-autoloader --no-interaction || true

EXPOSE 9000
CMD ["php-fpm", "-F"]
