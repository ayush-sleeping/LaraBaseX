# 🔐 Authentication Flow Documentation (LaraBaseX)

This guide explains the complete login/authentication process in LaraBaseX, with clear steps, code references, and security notes for developers.

#

## 1. Where is the Code?

- **Controllers:**
  - `app/Http/Controllers/Auth/AuthenticatedSessionController.php` (login logic)
  - `app/Http/Requests/LoginRequest.php` (validation)
  - `app/Providers/RouteServiceProvider.php` (redirects)
  - `app/Http/Controllers/DashboardController.php` (dashboard)
- **Routes:**
  - `routes/auth.php` (login routes)
  - `routes/backend.php` (dashboard route)
- **Frontend:**
  - `resources/js/pages/auth/login.tsx` (login form UI)
  - `resources/js/pages/dashboard.tsx` (dashboard UI)
- **Middleware:**
  - `app/Http/Middleware/admin.php`, `preventBackHistory.php`, etc.
- **Models:**
  - `app/Models/User.php` (user status, roles)

#

## 2. What Does It Do?

- Displays login form and validates credentials
- Authenticates user and creates session
- Redirects to dashboard on success
- Checks user status, roles, and permissions
- Protects routes with middleware
- Throttles failed login attempts

#

## 3. How Does It Work?

### Step-by-Step Login Flow

1. **User visits login page**
   - `GET /login` → `AuthenticatedSessionController@create()` → returns Inertia login page
2. **Login form displayed**
   - `resources/js/pages/auth/login.tsx` renders email/password fields
3. **User submits form**
   - `POST /login` → `AuthenticatedSessionController@store(LoginRequest $request)`
4. **Login validation & authentication**
   - `LoginRequest` validates credentials → `Auth::attempt()`
5. **Successful login redirect**
   - `redirect()->intended(RouteServiceProvider::HOME)` (usually `/dashboard`)
6. **Dashboard redirect route**
   - `GET /dashboard` → redirects to `/admin/dashboard` (from `backend.php`)
7. **Admin dashboard loads**
   - `DashboardController@index()` → Inertia dashboard page
8. **Final page rendered**
   - `resources/js/pages/dashboard.tsx` displays dashboard

#

## 4. Files Involved in Authentication

| Step | File | Purpose |
| --- | --- | --- |
| 1 | `routes/auth.php` | Defines login routes |
| 2 | `AuthenticatedSessionController.php` | Handles login logic |
| 3 | `auth/login.tsx` | Login form UI |
| 4 | `LoginRequest.php` | Validates credentials |
| 5 | `RouteServiceProvider.php` | Defines redirect destination |
| 6 | `routes/backend.php` | Dashboard redirect route |
| 7 | `DashboardController.php` | Dashboard data & logic |
| 8 | `dashboard.tsx` | Dashboard UI |

#

## 5. Security Requirements & Checks

For login to succeed, **ALL** of these must be true:

| Requirement | Check | Location |
| --- | --- | --- |
| **Email exists** | `users.email` must match | MySQL Database |
| **Password correct** | Hashed password must verify | MySQL Database |
| **User active** | `users.status = 'ACTIVE'` | User Model |
| **Account not locked** | Rate limiting checks | LoginRequest |
| **Proper permissions** | Role/permission validation | Custom Middleware |

#

## 6. What Happens During Authentication

```php
// When user submits login form, Laravel does:
1. SELECT * FROM users WHERE email = 'user@example.com'
2. password_verify('user-entered-password', $user->password)
3. Check if user status is ACTIVE
4. Validate user roles/permissions
5. If ALL checks pass → Login SUCCESS
6. If ANY check fails → Login FAILS
```

#

## 7. Common Login Failures

- **Wrong email:** No user found
- **Wrong password:** Password doesn't match
- **Inactive user:** Custom middleware blocks access

```php
Auth::attempt(['email' => 'wrong@email.com', 'password' => 'anything']) // FALSE
Auth::attempt(['email' => 'user@example.com', 'password' => 'wrongpassword']) // FALSE
// User exists but INACTIVE status: blocked by middleware
```

#

## 8. Key Security Points

- Database validation: credentials checked in MySQL `users` table
- Password hashing: bcrypt algorithm
- Session management: secure session tokens
- Middleware protection: validates status and permissions
- Rate limiting: throttles failed login attempts

#

## 9. Authentication Middleware Stack

After successful login, protected routes use this middleware stack:

```php
Route::middleware(['auth', 'verified', 'admin', 'preventBackHistory'])->group(function () {
    // Protected admin routes
});
```
- **auth**: Ensures user is authenticated
- **verified**: Ensures email is verified (if required)
- **admin**: Custom middleware checking admin permissions
- **preventBackHistory**: Prevents browser back button after logout

#

> All steps, files, and security checks above are strictly based on the LaraBaseX codebase. Use this guide to understand, audit, and extend authentication in your project.
