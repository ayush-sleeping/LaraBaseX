## Security Essentials
These features protect your app, data, and server from attacks:

<div id="top"></div>

<br>

### Table of Contents
1. [HTTPS enforced](#https-enforced)
2. [CORS configured properly](#cors-configured-properly)
3. [CSRF protection](#csrf-protection)
4. [Rate Limiting for APIs](#rate-limiting-for-apis)
5. [Validation layer using FormRequest](#validation-layer-using-formrequest)
6. [Use policies or gates for authorization](#use-policies-or-gates-for-authorization)
7. [Avoid mass assignment bugs](#avoid-mass-assignment-bugs)
8. [Escape output or sanitize input](#escape-output-or-sanitize-input)
9. [Sanitize uploaded files and validate MIME types](#sanitize-uploaded-files-and-validate-mime-types)
10. [Use environment variables for all secrets](#use-environment-variables-for-all-secrets)
11. [Disable debug mode on production](#disable-debug-mode-on-production)
12. [Log all authentication attempts and system errors](#log-all-authentication-attempts-and-system-errors)
13. [Do not expose Laravel version in headers](#do-not-expose-laravel-version-in-headers)

<br>

<br>

#

## HTTPS enforced

**What this topic is:**
Forces all traffic to use secure HTTPS protocol.

**Why we are using it:**
- Prevents data interception and man-in-the-middle attacks.
- Ensures encrypted communication between client and server.

**What it does in our project:**
- Redirects all HTTP requests to HTTPS in production.
- Uses middleware and server config for enforcement.

**Code files:**
- `app/Http/Middleware/ForceHttps.php` (custom middleware, if present)
- `.env.example`: `APP_URL` set to `https://...`
- `public/.htaccess` or server config: Redirect rules

**How to test:**
- Visit the site using `http://` and confirm it redirects to `https://`.
- Check browser for secure lock icon.

<Details>
<Summary>For more details!</Summary>


**Files Modified:**
- `/app/Providers/AppServiceProvider.php` - Force HTTPS URL generation
- `/config/app.php` - Added `force_https` configuration
- `/app/Http/Middleware/ForceHttps.php` - HTTP to HTTPS redirects
- `/app/Http/Kernel.php` - Middleware registration
- `/.env.example` - Added `APP_FORCE_HTTPS` variable

**How It Works:**
```php
// AppServiceProvider - URL generation
if (config('app.env') === 'production') {
    URL::forceScheme('https');
}

// Middleware - Request redirects
if ($this->shouldForceHttps($request)) {
    return redirect()->secure($request->getRequestUri(), 301);
}
```

**Configuration:**
```bash
# Production
APP_ENV=production
APP_FORCE_HTTPS=true
APP_URL=https://yourdomain.com

# Development
APP_ENV=local
APP_FORCE_HTTPS=false
APP_URL=http://localhost
```

**Features:**
- Automatic HTTPS enforcement in production
- Environment-based configuration
- 301 redirects for SEO
- Development-friendly (skips local)
- Dual-layer protection (AppServiceProvider + Middleware)

</Details>

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## CORS configured properly

**What this topic is:**
Cross-Origin Resource Sharing (CORS) controls which domains can access your API.

**Why we are using it:**
- Prevents unauthorized cross-origin requests.
- Secures API endpoints from external abuse.

**What it does in our project:**
- Configures allowed origins, methods, and headers for API routes.

**Code files:**
- `app/Http/Middleware/HandleCors.php` (or use `fruitcake/laravel-cors`)
- `config/cors.php`: CORS settings
- `.env.example`: Allowed origins

**How to test:**
- Make requests from allowed and disallowed domains; check response headers.
- Use browser dev tools to inspect CORS headers.


<Details>
<Summary>For more details!</Summary>

**Files Modified:**
- `/config/cors.php` - CORS policy configuration
- `/.env.example` - Added CORS environment variables
- `/bootstrap/app.php` - HandleCors middleware auto-registered
- `/config/sanctum.php` - Sanctum CORS integration

**How It Works:**
```php
// CORS Configuration - Dynamic via environment
'allowed_origins' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', '*')))),
'allowed_methods' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_METHODS', '*')))),
'supports_credentials' => env('CORS_SUPPORTS_CREDENTIALS', false),

// Sanctum CSRF Cookie endpoint included
'paths' => ['api/*', 'sanctum/csrf-cookie'],
```

**Configuration:**
```bash
# Production - Specific domains
CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://app.yourdomain.com
CORS_ALLOWED_METHODS=GET,POST,PUT,DELETE,OPTIONS
CORS_SUPPORTS_CREDENTIALS=true

# Development - Allow all
CORS_ALLOWED_ORIGINS=*
CORS_ALLOWED_METHODS=*
CORS_SUPPORTS_CREDENTIALS=false
```

**Features:**
- Environment-based origin control
- Support for credentials (cookies/auth)
- Sanctum integration for SPA authentication
- Comma-separated multiple domains
- Automatic OPTIONS preflight handling

</Details>

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## CSRF protection

**What this topic is:**
Cross-Site Request Forgery (CSRF) protection for forms and API requests.

**Why we are using it:**
- Prevents malicious requests from other sites on behalf of users.

**What it does in our project:**
- Automatically adds CSRF tokens to forms and validates them on POST requests.

**Code files:**
- `app/Http/Middleware/VerifyCsrfToken.php`
- Blade templates: `@csrf` directive in forms

**How to test:**
- Submit a form and inspect for hidden CSRF token field.
- Try submitting a POST request without token; should be rejected.


<Details>
<Summary>For more details!</Summary>

**Files Modified:**
- `/app/Http/Middleware/VerifyCsrfToken.php` - CSRF token validation
- `/config/sanctum.php` - Sanctum CSRF middleware configuration
- `/bootstrap/app.php` - CSRF middleware registration
- `/resources/js/lib/csrf.ts` - Frontend CSRF handling

**How It Works:**
```php
// VerifyCsrfToken Middleware - Validates all state-changing requests
protected $except = [
    'api/*',  // API routes use Sanctum instead
    'webhooks/*'
];

// Sanctum CSRF for SPA authentication
'verify_csrf_token' => env('SANCTUM_VERIFY_CSRF_MIDDLEWARE', VerifyCsrfToken::class),
```

**Configuration:**
```bash
# Environment variables
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,your-domain.com
SANCTUM_VERIFY_CSRF_MIDDLEWARE=App\Http\Middleware\VerifyCsrfToken
SESSION_DRIVER=cookie
SESSION_DOMAIN=.yourdomain.com
```

**Features:**
- CSRF protection for all web forms
- Sanctum CSRF for SPA authentication
- API routes use Bearer tokens instead
- Automatic CSRF token injection in forms
- Frontend CSRF cookie management

</Details>

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Rate Limiting for APIs

**What this topic is:**
Limits the number of requests per user/IP to prevent abuse.

**Why we are using it:**
- Protects against brute-force and denial-of-service attacks.

**What it does in our project:**
- Applies rate limits to API routes using middleware.

**Code files:**
- `app/Http/Middleware/ThrottleRequests.php`
- `routes/api.php`: Middleware usage
- `config/api.php` or `config/throttle.php`

**How to test:**
- Make repeated API requests; after limit, should receive 429 Too Many Requests.


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Validation layer using FormRequest

**What this topic is:**
Centralized request validation using Laravel FormRequest classes.

**Why we are using it:**
- Ensures all incoming data is validated before processing.
- Prevents invalid or malicious input.

**What it does in our project:**
- Uses custom FormRequest classes for validation logic.
- Returns validation errors in API responses.

**Code files:**
- `app/Http/Requests/`: Custom FormRequest classes
- Controllers: Type-hint FormRequest in methods

**How to test:**
- Submit forms or API requests with invalid data; should receive validation errors.


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Use policies or gates for authorization

**What this topic is:**
Authorization logic using Laravel policies and gates.

**Why we are using it:**
- Controls access to resources and actions based on user roles/permissions.

**What it does in our project:**
- Defines policies for models and gates for custom logic.
- Used in controllers and views for access checks.

**Code files:**
- `app/Policies/`: Policy classes
- `app/Providers/AuthServiceProvider.php`: Registers policies/gates
- Controllers: `authorize()` calls

**How to test:**
- Try accessing protected resources as different users; only authorized users succeed.


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Avoid mass assignment bugs

**What this topic is:**
Prevents unintended data changes via mass assignment vulnerabilities.

**Why we are using it:**
- Protects models from unwanted attribute changes.

**What it does in our project:**
- Uses `$fillable` or `$guarded` properties in models.

**Code files:**
- `app/Models/`: Model files with `$fillable` or `$guarded`

**How to test:**
- Attempt to submit extra fields in forms/API; only allowed fields are updated.



<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Escape output or sanitize input

**What this topic is:**
Prevents XSS by escaping output and sanitizing input.

**Why we are using it:**
- Protects users from malicious scripts and data.

**What it does in our project:**
- Escapes output in Blade templates by default.
- Sanitizes input using validation and custom logic.

**Code files:**
- Blade templates: `{{ $var }}` for escaped output
- FormRequest: Custom sanitization rules

**How to test:**
- Try submitting script tags in forms; output should be escaped.


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Sanitize uploaded files and validate MIME types

**What this topic is:**
Validates and sanitizes uploaded files to prevent malicious uploads.

**Why we are using it:**
- Prevents execution of dangerous files and ensures correct file types.

**What it does in our project:**
- Validates file type, size, and content on upload.
- Stores files securely.

**Code files:**
- Controllers handling uploads
- FormRequest: File validation rules
- `config/filesystems.php`

**How to test:**
- Try uploading files with invalid types; should be rejected.


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Use environment variables for all secrets

**What this topic is:**
Stores sensitive credentials in environment variables.

**Why we are using it:**
- Prevents hardcoding secrets in codebase.
- Secures API keys, DB passwords, etc.

**What it does in our project:**
- Loads secrets from `.env` file.
- References variables in config files.

**Code files:**
- `.env.example`, `.env`: Secret keys
- `config/*.php`: Uses `env()` for secrets

**How to test:**
- Check config files for use of `env()`.
- Change secret in `.env` and confirm app uses new value.



<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Disable debug mode on production

**What this topic is:**
Ensures debug mode is off in production to prevent info leaks.

**Why we are using it:**
- Prevents exposure of sensitive error details to users.

**What it does in our project:**
- Sets `APP_DEBUG=false` in production `.env`.

**Code files:**
- `.env.example`, `.env`: `APP_DEBUG` variable
- `config/app.php`: Reads debug setting

**How to test:**
- Trigger an error in production; should show generic error page, not stack trace.


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Log all authentication attempts and system errors

**What this topic is:**
Records login attempts and system errors for auditing and monitoring.

**Why we are using it:**
- Detects suspicious activity and helps with debugging.

**What it does in our project:**
- Logs authentication events and errors to log files.

**Code files:**
- `storage/logs/laravel.log`: Log output
- `app/Exceptions/Handler.php`: Error logging
- Auth controllers: Login attempt logging

**How to test:**
- Attempt login and check logs for entry.
- Trigger errors and verify logs are written.



<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Do not expose Laravel version in headers

**What this topic is:**
Removes Laravel version from HTTP response headers.

**Why we are using it:**
- Prevents attackers from targeting known vulnerabilities.

**What it does in our project:**
- Disables `X-Powered-By` and other version headers in server config.

**Code files:**
- `public/.htaccess` or server config: Header removal

**How to test:**
- Inspect response headers in browser; Laravel version should not be present.



<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>
