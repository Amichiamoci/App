#!/bin/sh

set -e

php /var/www/html/bin/console make:migration
php /var/www/html/bin/console doctrine:migrations:migrate