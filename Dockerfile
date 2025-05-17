FROM php:8.4-apache AS base
RUN apt update && \
    apt install -y --no-install-recommends --upgrade \
        libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
        libzip-dev zip unzip \
        curl libcurl4 libcurl4-openssl-dev wget \
        apache2-utils \
        libicu-dev libonig-dev \
        # libc-client-dev libkrb5-dev \
        libsqlite3-dev sqlite3 libpq-dev && \
    apt clean && \
    rm -rf /var/lib/apt/lists/*

# Install php extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-configure intl && \
    # docker-php-ext-configure imap --with-kerberos --with-imap-ssl && \
    docker-php-ext-install -j$(nproc) \
        zip \
        fileinfo \
        ftp \
        gettext \
        intl \
        mbstring \
        sockets \
        mysqli pdo_mysql \
        pgsql pdo_pgsql \
        pdo \
        curl \
        # imap \
        pdo_sqlite

# Use composer to build the dependencies
FROM composer:latest AS builder
WORKDIR /app
COPY composer.json .
RUN composer update --no-interaction --no-progress --ignore-platform-reqs

# Build the final image
FROM base AS final
RUN mkdir -p /app
WORKDIR /app

# Enable the site in apache
COPY ./docker_files/apache.conf /etc/apache2/sites-enabled/app.conf
COPY ./docker_files/php.ini /usr/local/etc/php/
COPY --chown=www-data ./docker_files/entrypoint.sh .
RUN chmod +x ./entrypoint.sh

# Move the downloaded dependencies to the actual place they need to be
COPY --from=builder --chown=www-data /app/vendor ./vendor
COPY --from=builder --chown=www-data /app/var ./var

# Actually copy the code
COPY --chown=www-data . .

# Enable cache handling
RUN chmod +x bin/console

# Volumes setup
VOLUME [ "/app/var" ]
RUN chown -R www-data /app/var

# Download packages
RUN php bin/console importmap:install
RUN php bin/console asset-map:compile

# Start the server
EXPOSE 80
ENTRYPOINT [ "./entrypoint.sh" ]