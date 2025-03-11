# This is a multi stage build

# Base image (contains server and http)
FROM php:8.4-apache AS base
ENV BASE_PATH=/
ENV APP_PATH=/
# Install php extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions zip pgsql mysqli pdo pdo_mysql pdo_pgsql pdo_odbc sqlite3 ldap zip curl ffi fileinfo ftp gettext imap mbstring intl sockets

# Enable mod_rewrite.c
RUN a2enmod rewrite

# Use composer to build the dependencies
FROM composer:latest AS builder
WORKDIR /var/www/html/
COPY composer.json .
RUN composer update --no-interaction --no-progress


# Actually copy the code
FROM base AS final
WORKDIR /var/www/html/

COPY . .
COPY ./docker_files/php.ini /usr/local/etc/php/
COPY ./docker_files/template.htaccess ./.htaccess

# Move the downloaded dependencies to the actual place they need to be
COPY --from=builder /var/www/html/vendor/ ./vendor/
COPY --from=builder /var/www/html/var/ ./var/

# Enable cache handling
RUN chmod +x bin/console
RUN chmod -R 777 ./var/cache/

# Download packages
RUN php bin/console importmap:install
RUN php bin/console asset-map:compile

# Start the server
EXPOSE 80
ENTRYPOINT [ "apache2ctl", "-D", "FOREGROUND" ]