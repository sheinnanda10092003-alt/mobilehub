#!/usr/bin/env bash
# Build script for Laravel on Render

set -o errexit  # Exit on error

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Installing Node dependencies..."
npm ci

echo "Building frontend assets..."
npm run build

echo "Setting permissions..."
chmod -R 755 storage bootstrap/cache

echo "Generating application key if needed..."
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

echo "Running database migrations..."
php artisan migrate --force

echo "Caching Laravel configuration..."
php artisan config:cache

echo "Caching Laravel routes..."
php artisan route:cache

echo "Caching Laravel views..."
php artisan view:cache

echo "Creating symbolic link for storage..."
php artisan storage:link

echo "Build completed successfully!"
