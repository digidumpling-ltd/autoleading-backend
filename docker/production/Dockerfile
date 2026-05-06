FROM composer:2.7 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
COPY packages ./packages

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts

FROM node:20-alpine AS assets

WORKDIR /app

COPY packages ./packages
COPY public ./public
COPY resources ./resources
COPY vite.config.js ./vite.config.js

RUN npm --prefix packages/Webkul/Admin install
RUN npm --prefix packages/Webkul/Admin run build
RUN npm --prefix packages/Webkul/Shop install
RUN npm --prefix packages/Webkul/Shop run build
RUN npm --prefix packages/Webkul/Installer install
RUN npm --prefix packages/Webkul/Installer run build

FROM php:8.3-fpm

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y \
        curl \
        git \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libzip-dev \
        unzip \
        zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=assets /app/public /var/www/html/public

COPY . /var/www/html

RUN cp -a /var/www/html/public /opt/app-public \
    && chown -R www-data:www-data storage bootstrap/cache /opt/app-public

COPY docker/production/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["php-fpm", "-F"]
