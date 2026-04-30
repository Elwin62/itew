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

# Start Apache in the foreground
exec apache2-foreground
