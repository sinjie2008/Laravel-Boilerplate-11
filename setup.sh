#!/bin/bash

echo "Running full Laravel setup..."

composer install
php artisan key:generate
php artisan migrate:fresh
php artisan module:migrate --seed

npm install
npm run build

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan storage:link

chmod -R 777 storage bootstrap/cache

# Optional: background queue worker
php artisan queue:work &

# Start server
php artisan serve