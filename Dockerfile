FROM php:8.4-fpm-alpine AS base

LABEL author="Riccardo Ciucci <riccardo@ciucci.dev>" \
    description="Amichiamoci web app"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM base AS build
RUN apk add --no-cache \
    bash \
    git curl \
    autoconf g++ make libtool \
    icu-dev \
    zlib-dev libzip-dev \
    gettext-dev oniguruma-dev \
    freetype-dev jpeg-dev libpng-dev libwebp-dev libjpeg-turbo-dev \
    sqlite-dev postgresql-dev mariadb-dev

# Install php extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-configure intl && \
    docker-php-ext-install -j$(nproc) \
        gd intl opcache \
        pcntl zip \
        gettext mbstring \
        mysqli pdo_mysql \
        pgsql pdo_pgsql \
        pdo_sqlite \
        exif \
        && \
    docker-php-ext-enable opcache  

FROM base

RUN apk add --no-cache \
    nginx curl \
    icu oniguruma \
    libintl libzip \
    freetype jpeg libpng libwebp libjpeg-turbo \
    sqlite-libs postgresql-libs mariadb-connector-c


RUN mkdir -p /run/nginx /app /app/var /app/var/log /app/var/data /app/var/cache
WORKDIR /app
VOLUME [ "/app/var/log", "/app/var/data" ]

ARG APP_ENV=prod
RUN if [ "$APP_ENV" = "dev" ]; then \
      apk add --no-cache --update linux-headers autoconf g++ make; \
      pecl install xdebug && docker-php-ext-enable xdebug; \
    fi; \
    echo "APP_ENV=$APP_ENV" > .env.local

COPY ./docker_files/nginx.conf /etc/nginx/http.d/default.conf
COPY ./docker_files/php.conf /usr/local/etc/php-fpm.d/www-app.conf
COPY ./docker_files/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY --from=build /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=build /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

COPY --chown=www-data ./docker_files/entrypoint.sh .
RUN chmod +x ./entrypoint.sh

# Actually copy the code
COPY --chown=www-data . .
RUN chmod +x bin/console
RUN composer install \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    $([ "$APP_ENV" = "prod" ] && echo "--no-dev")
RUN chown -R www-data /app/var

RUN php bin/console importmap:install
RUN if [ "$APP_ENV" = "prod" ]; then \
      php bin/console asset-map:compile; \
    fi

# Start the server
EXPOSE 8080
EXPOSE 9003
CMD [ "./entrypoint.sh" ]