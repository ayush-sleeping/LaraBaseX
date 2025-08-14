Run PHPStan for static analysis:
```cmd
vendor/bin/phpstan analyse --memory-limit=2G
```


Run Pest tests (unit/feature):
```cmd
vendor/bin/pest
```

Run PHPUnit tests:
```cmd
vendor/bin/phpunit
```

Run Pint for code style (PHP):
```cmd
vendor/bin/pint
```

Run ESLint for frontend code:
```cmd
npx eslint resources/js
```

Clear and cache Laravel config/routes/views:
```cmd
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache
php artisan cache:clear
```

---

API Testing (Postman/Newman):
```cmd
bash postman/test.sh
# Or manually:
newman run postman/LaraBaseX-API-Collection.json -e postman/LaraBaseX-Local-Environment.json --reporters cli,html,json
```

Health Endpoint Testing:
```cmd
php test_health.php
# Or via CI:
php artisan serve --port=8000 &
curl -f http://localhost:8000/api/health
curl -f http://localhost:8000/api/health/detailed
```

Database Migrations (for test setup):
```cmd
php artisan migrate --force
```

Node/Frontend Build (for integration tests):
```cmd
npm ci
npm run build
```

Environment Setup (for CI):
```cmd
cp .env.example .env
php artisan key:generate
```

Cache Warmup (custom, if implemented):
```cmd
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear
```

Other Useful QA/Testing Commands:
- Run all Laravel scheduled tasks (if you want to test scheduled jobs):
```cmd
php artisan schedule:run
```
- Run custom health checks (if you have custom endpoints):
```cmd
curl -f http://localhost:8000/api/health
curl -f http://localhost:8000/api/health/detailed
```

---

**Summary Table:**

| Purpose                | Command/Script                                      |
|------------------------|-----------------------------------------------------|
| PHPStan static analysis| `vendor/bin/phpstan analyse --memory-limit=2G`      |
| Pest tests             | `vendor/bin/pest`                                   |
| PHPUnit tests          | `vendor/bin/phpunit`                                |
| Pint code style        | `vendor/bin/pint`                                   |
| ESLint (frontend)      | `npx eslint resources/js`                           |
| API tests (Postman)    | `bash postman/test.sh` / `newman run ...`           |
| Health endpoint test   | `php test_health.php` / `curl ...`                  |
| Migrations             | `php artisan migrate --force`                       |
| Node build             | `npm ci` / `npm run build`                          |
| Env setup              | `cp .env.example .env` / `php artisan key:generate` |
| Cache warmup/clear     | `php artisan config:cache` etc.                     |
| Schedule tasks         | `php artisan schedule:run`                          |
