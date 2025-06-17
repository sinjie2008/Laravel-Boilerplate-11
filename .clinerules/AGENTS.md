# AGENTS Guide

## Repository Overview
This project is a Laravel 11 boilerplate that organizes domain logic into modules under the `Modules/` directory using the `nwidart/laravel-modules` package. Development guidelines are available in `.clinerules/laravel-11-dev-rule.md`.

### Module Structure
Each module mirrors Laravel's default layout and typically contains:

- `App/` – Controllers, Models, Requests and other classes
- `Database/` – migrations and seeders
- `routes/` – web and API route definitions
- `resources/` – Blade templates and assets
- `config/` – module specific configuration

Modules are enabled or disabled via `modules_statuses.json`.

## Environment Setup
1. Ensure PHP 8.2+, Composer, Node.js and NPM are installed. On Ubuntu based images you can install the necessary packages with:
   ```bash
   sudo apt update
   sudo apt install -y php-cli php-xml php-mbstring php-zip php-sqlite3 composer npm
   ```
2. Install PHP and Node dependencies:
   ```bash
   composer install
   npm install
   ```
3. Copy `.env.example` to `.env` and run the setup script to generate the application key, run migrations, seed modules and build assets:
   ```bash
   cp .env.example .env
   ./setup.sh
   ```
4. Verify that modules you need are enabled in `modules_statuses.json`.

## Testing & Linting
Run the automated test suite and the Pint linter before committing changes:
```bash
php artisan test
./vendor/bin/pint
```

## Development Workflow
* Follow a **module-first** approach—place new features inside an existing module or create a new module if needed. Review existing modules to stay consistent.
* Register routes inside the module's `routes` directory or `routes/web.php` when appropriate.
* After frontend changes, compile assets with:
  ```bash
  npm run build
  ```
* Launch a local server during development with:
  ```bash
  php artisan serve
  ```

Refer to `.clinerules/laravel-11-dev-rule.md` for full coding standards and architecture guidelines.
