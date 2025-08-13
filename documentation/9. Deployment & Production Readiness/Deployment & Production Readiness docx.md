# 🚀 Deployment & Production Readiness (LaraBaseX)

This section documents deployment and production practices in LaraBaseX, strictly based on the codebase and config files.

#

## 1. Environment Configuration

- All environment variables are managed via `.env` and `.env.example`.
- Key settings: `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`, `DB_*`, `SESSION_*`, `QUEUE_*`, `FILESYSTEM_DISK`, `MAIL_*`.
- Sensitive values (keys, passwords) must be set for production.

**Where:**
- `.env`, `.env.example`, `config/app.php`, `config/database.php`, etc.

**How to test:**
- Set up `.env` for production and run `php artisan config:cache`.

#

## 2. Caching & Session

- Default cache driver is `file`, but Redis/Memcached can be used for production (`config/cache.php`).
- Session driver is `database` for persistence and scalability (`config/session.php`).
- Session lifetime and encryption can be configured.

**Where:**
- `config/cache.php`, `config/session.php`, `.env`

**How to test:**
- Set `CACHE_DRIVER` and `SESSION_DRIVER` in `.env`, run `php artisan cache:clear` and test session persistence.

#

## 3. Queue & Jobs

- Queue driver is set to `database` by default (`config/queue.php`).
- Supports other drivers (Redis, SQS, etc.) for production scaling.
- Jobs table migration included for persistent job storage.

**Where:**
- `config/queue.php`, `.env`, `database/migrations/0001_01_01_000002_create_jobs_table.php`

**How to test:**
- Set up queue worker (`php artisan queue:work`) and dispatch jobs.

#

## 4. File Storage

- Default disk is `local`, but S3 or other cloud disks can be configured for production (`config/filesystems.php`).
- Private and public storage paths are set for security.

**Where:**
- `config/filesystems.php`, `.env`

**How to test:**
- Set `FILESYSTEM_DISK` to `s3` and test file upload/download.

#

## 5. Mail Configuration

- Default mailer is `log` for local, but SMTP, SES, Mailgun, etc. can be set for production (`config/mail.php`).
- All mail settings are environment-driven.

**Where:**
- `config/mail.php`, `.env`

**How to test:**
- Set up mailer in `.env` and send a test email.

#

## 6. Database & Unicode

- MySQL is the default database, with `utf8mb4` charset and strict mode enabled for safety (`config/database.php`).
- SSL and dump options are set for secure backups.

**Where:**
- `config/database.php`, `.env`

**How to test:**
- Run migrations and verify schema; test backup/dump.

#

## 7. Frontend Build & Vite

- Vite is used for frontend build and asset management (`vite.config.ts`).
- Production build: `npm run build` outputs assets to `public/build`.
- TailwindCSS and React plugins are included.

**Where:**
- `vite.config.ts`, `package.json`, `resources/js/`, `public/build/`

**How to test:**
- Run `npm run build` and verify assets in `public/build`.

#

## 8. Security & Debug

- `APP_DEBUG` must be set to `false` in production.
- `APP_KEY` must be set and kept secret.
- HTTPS should be enforced (`APP_FORCE_HTTPS=true`).
- Sensitive config values should never be committed.

**Where:**
- `.env`, `config/app.php`

**How to test:**
- Set `APP_DEBUG=false`, `APP_FORCE_HTTPS=true` and verify error pages and HTTPS redirects.

#

## 9. Production Checklist (AWS/Azure/Other)

**After deploying LaraBaseX to a live server, you MUST:**

1. Set up a secure `.env` file with all production values (keys, DB, mail, cache, etc.).
2. Run `php artisan key:generate` to set `APP_KEY`.
3. Set `APP_DEBUG=false` and `APP_ENV=production`.
4. Set `APP_FORCE_HTTPS=true` and configure web server for HTTPS.
5. Run `php artisan migrate --force` to apply all migrations.
6. Run `php artisan config:cache`, `route:cache`, `view:cache` for performance.
7. Set up queue workers (`php artisan queue:work --daemon`) for jobs.
8. Set up cron for scheduled tasks (`php artisan schedule:run`).
9. Configure file storage (S3, etc.) and test uploads/downloads.
10. Set up mailer and send a test email.
11. Set up cache driver (Redis/Memcached) for scalability.
12. Monitor logs and errors (`storage/logs/laravel.log`).
13. Restrict public access to sensitive folders (e.g., `.env`, `storage/`, `vendor/`).
14. Regularly back up database and storage.
15. Test all critical flows (auth, file upload, mail, jobs) in production.

**Where to find these settings:**
- `.env`, `config/*`, `database/migrations/*`, `vite.config.ts`, `public/build/`

#

> All practices and checklist items above are strictly based on the LaraBaseX codebase. For more details, see the referenced files and follow the checklist for a smooth production deployment.
