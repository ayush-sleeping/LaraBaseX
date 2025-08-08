# 🚀 LaraBaseX

> A Laravel 12 + ReactJS Full Stack Starter Boilerplate.
This is a secure, modular, production-ready base project using Laravel 12 with ReactJS frontend, ideal for building scalable web applications without Blade.

<div id="top"></div>

##

### Table of content:

| No. | Topics                                                                                  |
| --- | --------------------------------------------------------------------------------------- |
| 0.  | [Tech Stack](#tech-stack)                                                               |
| 1   | [Authentication Flow Documentation](#authentication-flow-documentation)                 |
| 2   | [Authorization Flow Documentation](#authorization-flow-documentation)                   |
| 3   | [Setting Profile Information Update](#setting-profile-information-update)               |
| 4   | [Setting Password Update](#setting-password-update)                                     |
| 5   | [Permission Based UI Implementation](#permission-based-ui-implementation)               |

<br>

<br>

#



## Tech Stack

- **Backend**: Laravel 12 (REST API)
- **Frontend**: ReactJS (Vite + Axios)
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Deployment Ready**: Docker / Shared Hosting / VPS



### 🔒 1. Security Essentials
These features protect your app, data, and server from attacks:

- ✅ HTTPS enforced (Force HTTPS in AppServiceProvider)
- ✅ CORS configured properly (config/cors.php)
- ✅ CSRF protection (even for APIs, use Sanctum or tokens)
- ✅ Rate Limiting for APIs (ThrottleRequests middleware)
- ✅ Validation layer using FormRequest (php artisan make:request)
- ✅ Use policies/gates for authorization (php artisan make:policy)
- ✅ Avoid mass assignment bugs ($fillable vs $guarded)
- ✅ Escape output or sanitize input if user-generated data is stored
- ✅ Sanitize uploaded files & validate MIME types
- ✅ Use environment variables for all secrets (never hardcode keys)
- ✅ Disable debug mode on production (APP_DEBUG=false)
- ✅ Log all authentication attempts and system errors
- ✅ Do not expose Laravel version in headers




### 🧱 2. Architecture & Structure Essentials
- ✅ Keep controllers thin, use Services for logic
- ✅ Helpers.php for reusable functions (as you're doing)
- ✅ Job Queues setup (Redis + Supervisor in production)
- ✅ Use resource() routes & API standards (api.php)
- ❌ Use Service classes for business logic (e.g. App\Services\UserService)
- ❌ Use Repository pattern (clean separation from Eloquent queries)
- ❌ Use enums for static statuses or types (php artisan make:enum)
- ❌ Event-Listener system for side-effects (e.g. sending email after registration)
- ❌ Transform API response data using Laravel Resource classes


### 📦 3. Packages to Include
- ✅ Spatie Laravel Permission – roles/permissions
- ✅ Laravel Sanctum or Passport – token-based auth
- ✅ Laravel Telescope (local/dev) – debugging, request log
- ✅ Laravel Debugbar (local/dev) – performance analysis
- ✅ Spatie Backup – scheduled database/file backups
- ✅ Spatie Activity Log – audit logs for user actions


### 🧠 4. Developer Experience (DX)
- ✅ Global Exception Handler for API errors
- ✅ Standard API Response format using success(), error() helpers
- ✅ Seeder & Factory files for test data
- ✅ Well-structured .env.example file
- ✅ API Documentation via Swagger or Postman
- ✅ Postman Collection for APIs preloaded
- ✅ PHPStan or Larastan for static analysis
- ✅ Predefined Error messages in lang/en/messages.php


### 🧰 5. Frontend Integration (ReactJS)
Since Laravel 12 uses Vite + React:

- ✅ Serve React app via Vite from Laravel backend
- ✅ Set up proxy in vite.config.js to API routes
- ✅ React routing via React Router DOM
- ✅ Token-based authentication (e.g. Sanctum)
- ✅ Store tokens securely (httpOnly if possible)
- ✅ Axios with global error interceptor
- ✅ Dotenv file in React for API URLs


### 🔐 6. User Management Essentials
- ✅ Register/Login/Logout APIs
- ✅ Change Password / Forgot Password / Email Verify
- ✅ User roles and permissions (admin, user, manager)
- ✅ Login attempt throttling
- ✅ User profile with avatar upload
- ❌ Two-Factor Authentication (optional)


### 🛠️ 7. Helper Functions You Should Add
You already have many! Add:

- ✅ api_success() / api_error() – standardized response
- ✅ get_random_code() – for OTP, referral codes
- ✅ generate_slug() – auto slug from title
- ✅ upload_file() – universal file uploader
- ✅ remove_file() – delete uploaded file
- ✅ get_file_url() – retrieve full file URL from path
- ✅ human_readable_time() – time ago format
- ✅ log_activity() – wrapper to log user actions


### 💾 8. MySQL Best Practices
- ✅ Use InnoDB, not MyISAM
- ✅ Use foreign keys with onDelete('cascade')
- ✅ Add indexes to frequently searched fields
- ✅ Store timestamps in UTC, convert in app
- ✅ Avoid text or json unless needed
- ✅ Use migrations and version your DB


### 🔄 9. Deployment & Production Readiness
- ✅ .env file set up with production keys
- ✅ Use queues and Supervisor (for jobs)
- ✅ Enable Redis or Memcached
- ✅ Enable caching (config, route, view, queries)
- ✅ Health check route (/health)
- ✅ Use Laravel Forge or Ploi or GitHub Actions for CI/CD
