#!/bin/sh

set -e

UPLOAD_DIRECTORY=/app/var/uploads
LOGS_DIRECTORY=/app/var/log
CACHE_DIRECTORY=/app/var/cache

mkdir -p $UPLOAD_DIRECTORY $LOGS_DIRECTORY $CACHE_DIRECTORY
echo 'deny from all' > $UPLOAD_DIRECTORY/.htaccess
chown -R www-data:www-data $UPLOAD_DIRECTORY $LOGS_DIRECTORY $CACHE_DIRECTORY

apache2ctl -D FOREGROUND