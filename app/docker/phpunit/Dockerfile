FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libpq-dev libicu-dev git && apt-get clean
RUN pecl install apcu xdebug
RUN docker-php-ext-enable apcu xdebug
RUN docker-php-ext-install intl opcache pdo pdo_pgsql
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/bin/composer

WORKDIR /usr/share/nginx/html/
