# This is a multi stage build

# Base image (contains server and http)
FROM php:8.3-apache AS base
WORKDIR /var/www/html/
COPY . .
COPY ./php.ini /usr/local/etc/php/

# Use composer to build the dependencies
FROM composer:latest AS composer
WORKDIR /var/www/html/
COPY --from=base /var/www/html/composer.json .
RUN composer update --no-interaction --no-progress

# Move the downloaded dependencies to the actual place they need to be
FROM base AS final
WORKDIR /var/www/html/
COPY --from=composer /var/www/html/vendor/ ./vendor/

# Enable mod_rewrite.c
RUN a2enmod rewrite

RUN mkdir -p var/cache var/log
RUN chmod +x bin/console