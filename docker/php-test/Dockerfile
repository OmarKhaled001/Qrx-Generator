# Test image: PHP + imagick (PNG/EPS QR output) + gd + pdo_mysql/sqlite.
# docker build --build-arg PHP_VERSION=8.5 -t qrx-php85 docker/php-test
ARG PHP_VERSION=8.5
FROM php:${PHP_VERSION}-cli
RUN apt-get update && apt-get install -y --no-install-recommends libmagickwand-dev libsqlite3-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev unzip git \
 && pecl install imagick && docker-php-ext-enable imagick \
 && docker-php-ext-configure gd --with-jpeg --with-freetype && docker-php-ext-install gd pdo_mysql pdo_sqlite zip bcmath exif \
 && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
