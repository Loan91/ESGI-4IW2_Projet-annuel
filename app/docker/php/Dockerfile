FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libpq-dev libicu-dev && apt-get clean
RUN pecl install apcu xdebug
RUN docker-php-ext-enable apcu xdebug
RUN docker-php-ext-install intl opcache pdo pdo_pgsql

WORKDIR /usr/share/nginx/html/
