#!/bin/sh

set -e

APP_PATH=/app/var

LOGS_DIRECTORY=$APP_PATH/log
CACHE_DIRECTORY=$APP_PATH/cache

APP_DATA=$APP_PATH/data
UPLOAD_DIRECTORY=$APP_DATA/uploads

mkdir -p $UPLOAD_DIRECTORY $LOGS_DIRECTORY/nginx
chown -R www-data:www-data $APP_DATA $LOGS_DIRECTORY

php-fpm -D
nginx -g "daemon off;"