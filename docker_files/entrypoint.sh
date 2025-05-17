#!/bin/sh

set -e

UPLOAD_DIRECTORY=/app/var/uploads
mkdir -p $UPLOAD_DIRECTORY
echo 'deny from all' > $UPLOAD_DIRECTORY.htaccess
chown -R www-data:www-data $UPLOAD_DIRECTORY

apache2ctl -D FOREGROUND