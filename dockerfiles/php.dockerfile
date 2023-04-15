FROM php:7.4-fpm-alpine3.14

WORKDIR /var/www/html

RUN apk update && apk add --no-cache \
    libxml2-dev \
    icu-dev \
    libpng-dev \
    freetype-dev \
    && docker-php-ext-install pdo pdo_mysql intl soap gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && if [ ! -f "/usr/local/bin/composer" ]; then echo "Composer installation failed"; exit 1; fi \
    && chmod +x /usr/local/bin/composer \
    && if [ ! -x "/usr/local/bin/composer" ]; then echo "Composer permission change failed"; exit 1; fi
