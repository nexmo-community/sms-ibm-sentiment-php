FROM php:7.3-apache

RUN apt-get update \
 && apt-get install -y vim git unzip zlib1g-dev libzip-dev \
 && docker-php-ext-install zip \
 && a2enmod rewrite \
 && sed -i 's!/var/www/html!/var/www!g' /etc/apache2/sites-available/000-default.conf \
 && echo "AllowEncodedSlashes On" >> /etc/apache2/apache2.conf

WORKDIR /var/www
