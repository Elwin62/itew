#!/bin/sh

# Make sure line endings are correct (no \r issues on linux)
set -e

# Run migrations
php artisan migrate --force

# Run database seeder (The DatabaseSeeder has logic to only run if Users table is empty)
php artisan db:seed --force

# Cache routes and config for performance
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Force correct MPM at runtime to avoid AH00534
a2dismod mpm_event mpm_worker || true
a2enmod mpm_prefork || true

# Dynamically configure Apache to listen on Railway's assigned port
PORT=${PORT:-80}
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Start Apache in the foreground
exec apache2-foreground
