#!/bin/sh

set -e

php bin/console make:migration
php bin/console doctrine:migrations:migrate