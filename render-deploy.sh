#!/usr/bin/env bash

# Exit immediately if a command exits with a non-zero status
set -e

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Cache configuration, routes, and views for speed
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations automatically
php artisan migrate --force
