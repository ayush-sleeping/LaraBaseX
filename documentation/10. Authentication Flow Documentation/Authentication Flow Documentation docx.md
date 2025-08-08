
### Complete Login Process Flow
Understanding how user authentication works from frontend to backend and database.

#### 🎯 Step-by-Step Login Flow

**1. User visits login page**

```php
GET /login → AuthenticatedSessionController@create() → returns Inertia::render('auth/login')
```

**2. Login form displayed**

```typescript
resources/js/pages/auth/login.tsx renders with email/password fields
```

**3. User submits form**

```php
POST /login → AuthenticatedSessionController@store(LoginRequest $request)
```

**4. Login validation & authentication**

```php
LoginRequest validates email/password → Auth::attempt() tries to log user in
```

**5. Successful login redirect**

```php
return redirect()->intended(RouteServiceProvider::HOME);
// RouteServiceProvider::HOME = '/dashboard'
```

**6. Dashboard redirect route**

```php
GET /dashboard → redirects to /admin/dashboard (from backend.php)
```

**7. Admin dashboard loads**

```php
GET /admin/dashboard → DashboardController@index() → Inertia::render('dashboard')
```

**8. Final page rendered**

```typescript
resources/js/pages/dashboard.tsx displays the admin dashboard
```

#### 📋 Files Involved in Authentication

| Step | File | Purpose |
|------|------|---------|
| 1 | `routes/auth.php` | Defines login routes |
| 2 | `AuthenticatedSessionController.php` | Handles login logic |
| 3 | `auth/login.tsx` | Login form UI |
| 4 | `LoginRequest.php` | Validates credentials |
| 5 | `RouteServiceProvider.php` | Defines redirect destination |
| 6 | `routes/backend.php` | Dashboard redirect route |
| 7 | `DashboardController.php` | Dashboard data & logic |
| 8 | `dashboard.tsx` | Dashboard UI |

#### 🛡️ Security Requirements

For login to succeed, **ALL** of these must be true:

| Requirement | Check | Location |
|-------------|-------|----------|
| **Email exists** | `users.email` must match | MySQL Database |
| **Password correct** | Hashed password must verify | MySQL Database |
| **User active** | `users.status = 'ACTIVE'` | User Model |
| **Account not locked** | Rate limiting checks | LoginRequest |
| **Proper permissions** | Role/permission validation | Custom Middleware |

#### 🔐 What Happens During Authentication

```php
// When user submits login form, Laravel does:
1. SELECT * FROM users WHERE email = 'user@example.com'
2. password_verify('user-entered-password', $user->password)
3. Check if user status is ACTIVE
4. Validate user roles/permissions
5. If ALL checks pass → Login SUCCESS
6. If ANY check fails → Login FAILS
```

#### ❌ Common Login Failures

```php
// Wrong email
Auth::attempt(['email' => 'wrong@email.com', 'password' => 'anything'])
// Result: FALSE - No user found with that email

// Correct email, wrong password
Auth::attempt(['email' => 'user@example.com', 'password' => 'wrongpassword'])
// Result: FALSE - Password doesn't match hash in database

// User exists but INACTIVE status
// Custom middleware blocks access even if login succeeds
```

#### 🔑 Key Security Points

- **Database Validation**: Without correct credentials in MySQL `users` table, login is impossible
- **Password Hashing**: Passwords are hashed using Laravel's secure bcrypt algorithm
- **Session Management**: Successful login creates secure session tokens
- **Middleware Protection**: Custom middleware validates user status and permissions
- **Rate Limiting**: Failed login attempts are throttled to prevent brute force attacks

#### 📝 Authentication Middleware Stack

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
