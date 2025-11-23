#!/bin/bash

set -e

# 1. Limpia Caché 
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan migrate --force

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
# 2. Comando de arranque principal
exec "$@"