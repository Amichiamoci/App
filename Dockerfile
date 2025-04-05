FROM php:8.4-apache AS base
RUN apt update && \
    apt install -y --no-install-recommends --upgrade \
        libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
        libzip-dev zip unzip \
        curl libcurl4-openssl-dev wget \
        apache2-utils \
        libicu-dev libonig-dev \
        sqlite3 libpq-dev && \
    apt clean && \
    rm -rf /var/lib/apt/lists/*

# Install php extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-configure intl && \
    docker-php-ext-install -j$(nproc)\
        zip \
        fileinfo \
        ftp \
        gettext \
        intl \
        mbstring \
        sockets \
        mysqli pdo_mysql \
        pgsql pdo_pgsql \
        pdo

# Use composer to build the dependencies
FROM composer:latest AS builder
WORKDIR /app
COPY composer.json .
RUN composer update --no-interaction --no-progress

# Build the final image
FROM base AS final
ENV BASE_PATH=/
ENV APP_PATH=/
RUN mkdir -p /app
RUN chown -R www-data /app 
WORKDIR /app

# Enable the site in apache
COPY ./docker_files/apache.conf /etc/apache2/sites-enabled/app.conf
COPY ./docker_files/php.ini /usr/local/etc/php/

# Move the downloaded dependencies to the actual place they need to be
COPY --from=builder --chown=www-data /app/vendor ./vendor
COPY --from=builder --chown=www-data /app/var ./var

# Actually copy the code
COPY . .

# Enable cache handling
RUN chmod +x bin/console
RUN chmod -R 777 ./var/cache/

# Download packages
RUN php bin/console importmap:install
RUN php bin/console asset-map:compile

# Start the server
EXPOSE 80
ENTRYPOINT [ "apache2ctl", "-D", "FOREGROUND" ]