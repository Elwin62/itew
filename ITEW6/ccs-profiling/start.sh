#!/bin/sh

# Run migrations
php artisan migrate --force

# Run database seeder
# The DatabaseSeeder has been updated to only seed if the users table is empty.
php artisan db:seed --force

# Start the application using Laravel's built-in server
# Render provides the PORT environment variable. If not set, default to 8000.
PORT=${PORT:-8000}
php artisan serve --host=0.0.0.0 --port=$PORT
