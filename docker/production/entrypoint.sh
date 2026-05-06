#!/usr/bin/env sh
set -e

if [ -d /var/www/html/public ] && [ -z "$(ls -A /var/www/html/public 2>/dev/null)" ]; then
    cp -a /opt/app-public/. /var/www/html/public/
fi

chown -R www-data:www-data storage bootstrap/cache public
chmod -R 775 storage bootstrap/cache public

if [ ! -e /var/www/html/public/storage ]; then
    php artisan storage:link
fi

php artisan optimize

exec "$@"
