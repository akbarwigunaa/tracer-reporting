# Web Tracer Reporting System

Laravel web application for processing Tracer Study Excel data into BI dashboards and automated Word reports.

## Stack
- Laravel 13, PHP 8.5, MySQL 8+
- Bootstrap 5 + AdminLTE 4 (via npm/Vite)
- Chart.js for browser-side charts
- PhpSpreadsheet for Excel reading
- PHPWord for Word report generation

## Architecture
- Clean Architecture: Presentation → Application → Domain → Infrastructure
- Repository Pattern with Interface + Eloquent implementation
- Service Layer for business logic (NOT in Controllers)
- Config-driven parameter processing via config/tracer.php

## Conventions
- PSR-12 coding standard
- Conventional Commits (feat:, fix:, refactor:, docs:, style:, test:)
- No authentication — single personal user
- Export only to Word (.docx) — no PDF
- No data cleaning/validation — reads Excel as-is

## Development
```sh
php artisan serve        # Start dev server
npx vite                 # Start Vite dev server
php artisan migrate      # Run migrations
```
