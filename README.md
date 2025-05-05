<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


## About Laravel Boilderplate 
Feat(core): Add boilerplate default functions and modules

Admin Portal:
- Theme AdminLTE
- Query Builder
- API Passport

Default Modules Convert (Done):

Common
- Todo List,
- Document Upload

User Management
- Users,
- Role,
- Permission

System Manager
- Sidebar,
- Media,
- Module,
- Backup,
- Sql Generator,
- Activity Log,
- Excel,
- LaTeX,
-  Config

Backup Manager Module (In Progress):
- DB Backup/Restore (Done)
- Files Backup (Bug), Files Restore (Not yet)
- All-in-one Backup/Restore (Not yet)

Update Patch
-  Laravel Framework 11.44.7

Auto Setup:
- setup.sh

Sample Config:
- .env

## Installation Guide

composer install
php artisan key:generate
php artisan migrate:fresh
php artisan migrate --force
php artisan module:migrate --seed --all

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


## Auto Setup
./setup.sh
