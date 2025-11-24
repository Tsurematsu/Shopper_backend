#!/bin/bash
set -e
cd /var/www/html
mkdir -p /var/www/html/uploads
chmod 777 /var/www/html/uploads
chown -R www-data:www-data /var/www/html/uploads
composer install --no-dev --optimize-autoloader --no-interaction
composer dump-autoload --optimize
php /var/www/html/migrate.php
exec apache2-foreground