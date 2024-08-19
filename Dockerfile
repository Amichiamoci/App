# This is a multi stage build

# Base image (contains server and http)
FROM php:8.3-apache AS base

# Install php extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions zip pgsql mysqli pdo pdo_mysql pdo_pgsql pdo_odbc sqlite3 ldap zip curl ffi fileinfo ftp gettext imap mbstring intl sockets


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

RUN chmod +x bin/console

# Download packages
RUN php bin/console importmap:install
RUN php bin/console asset-map:compile