FROM php:7.1.19-fpm

RUN apt-get update && apt-get install -y libmcrypt-dev mysql-client wget git \
    && docker-php-ext-install mcrypt pdo_mysql

RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/1b137f8bf6db3e79a38a5bc45324414a6b1f9df2/web/installer -O - -q | php -- --quiet
RUN mv composer.phar /usr/local/bin/composer

RUN composer global require "laravel/installer=~1.1"

WORKDIR /var/www

COPY . .

RUN chown -R www-data:www-data /var/www
