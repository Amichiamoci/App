# This is a multi stage build

# Base image (contains server and http)
FROM php:8.3-apache AS base
ENV BASE_PATH=/
ENV APP_PATH=/
# Install php extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions zip pgsql mysqli pdo pdo_mysql pdo_pgsql pdo_odbc sqlite3 ldap zip curl ffi fileinfo ftp gettext imap mbstring intl sockets


WORKDIR /var/www/html/
COPY . .
COPY ./docker_config_files/php.ini /usr/local/etc/php/

# Use composer to build the dependencies
FROM composer:latest AS composer
WORKDIR /var/www/html/
COPY --from=base /var/www/html/composer.json .
RUN composer update --no-interaction --no-progress

# Move the downloaded dependencies to the actual place they need to be
FROM base AS final
WORKDIR /var/www/html/
COPY --from=composer /var/www/html/vendor/ ./vendor/
COPY --from=composer /var/www/html/var/ ./var/

COPY ./docker_config_files/template.htaccess ./.htaccess

# Enable mod_rewrite.c
RUN a2enmod rewrite

RUN chmod +x bin/console
RUN chmod -R 777 ./var/cache/

# Download packages
RUN php bin/console importmap:install
RUN php bin/console asset-map:compile

# SSH setup for container
COPY ./docker_config_files/startup.sh ./startup.sh

RUN apt-get update \
    && apt-get install -y --no-install-recommends dialog \
    && apt-get install -y --no-install-recommends openssh-server \
    && echo "root:Docker!" | chpasswd \
    && chmod u+x ./startup.sh
COPY ./docker_config_files/sshd_config /etc/ssh/

# Start the server
EXPOSE 80 2222
RUN ./startup.sh