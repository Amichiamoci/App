#!/bin/sh
set -e
service ssh start

# echo "ServerName localhost" >> /etc/apache2/apache2.conf
apache2ctl -D FOREGROUND