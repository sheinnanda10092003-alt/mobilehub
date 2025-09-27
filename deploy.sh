#!/bin/bash

echo "🚀 Starting MobileHub Deployment..."

# Install/Update Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

# Install/Update NPM dependencies
echo "📦 Installing NPM dependencies..."
npm install

# Build assets
echo "🔨 Building production assets..."
npm run build

# Clear and optimize caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Set proper permissions
echo "🔒 Setting file permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs
chmod -R 777 storage/framework/sessions
chmod -R 777 storage/framework/cache
chmod -R 777 storage/framework/views

echo "✅ Deployment completed successfully!"
echo "🌐 Your MobileHub application is ready to serve users!"