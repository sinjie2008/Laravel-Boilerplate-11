<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>



## About Laravel Boilderplate 
Boiler Plate Default Function

Admin Protal
- Theme AdminLTE
-  Pagination
-  Search
-  Query Builder
-  Backup
-  AI Chat to db
-  API Passport

Modules Convert
- Todo List (Done)
-  AI SQL Generator (Done)
-  Role Premission  (Done)
-  Activity Log  (Remove Bug) <-
- Moduler Installer  (Done)
- Document Upload  (Done)
- Backup Manager, (still having bug) <-

Sample Config
- .env


## Installation Guide

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
php artisan tinker
chmod -R 777 storage bootstrap/cache


php artisan serve
