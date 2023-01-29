#!/bin/sh

cd /var/www/html/ && php artisan optimize && php artisan cache:clear
supervisord -c /etc/supervisord.conf
php-fpm81 -D --pid /run/php-fpm.pid
exec nginx
