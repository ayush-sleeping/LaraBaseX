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
    <details>
    <summary>
    <strong>🔐 Implementation Details</strong> (Click to expand)</summary>

    **📁 Files Modified:**
    - `/app/Providers/AppServiceProvider.php` - Force HTTPS URL generation
    - `/config/app.php` - Added `force_https` configuration
    - `/app/Http/Middleware/ForceHttps.php` - HTTP to HTTPS redirects
    - `/app/Http/Kernel.php` - Middleware registration
    - `/.env.example` - Added `APP_FORCE_HTTPS` variable

    **🔧 How It Works:**
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

    **⚙️ Configuration:**
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

    **✅ Features:**
    - Automatic HTTPS enforcement in production
    - Environment-based configuration
    - 301 redirects for SEO
    - Development-friendly (skips local)
    - Dual-layer protection (AppServiceProvider + Middleware)

    </details>
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
- ❌ API Documentation via Swagger or Postman
- ❌ Postman Collection for APIs preloaded
- ❌ PHPStan or Larastan for static analysis
- ❌ Predefined Error messages in lang/en/messages.php
- ❌ Custom Artisan commands (php artisan make:command)


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
- ❌ Enable caching (config, route, view, queries)
- ❌ DB backups automated
- ❌ Health check route (/health)
- ❌ Use Laravel Forge or Ploi or GitHub Actions for CI/CD


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Authentication Flow Documentation

<details>
<summary><strong>🔐 Complete Login Process Flow</strong> (Click to expand)</summary>

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

</details>


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>



<br>

<br>

#

## Authorization Flow Documentation

<details>
<summary><strong>🛡️ Complete Role-Based Access Control (RBAC) Flow</strong> (Click to expand)</summary>

### Complete Role-Based Access Control (RBAC) Flow
Understanding how user authorization works from registration to protected resource access.

#### 🎯 Step-by-Step Authorization Flow

**1. User Registration with Role Assignment**

```php
POST /register → RegisteredUserController@store() → User created with 'User' role and 'INACTIVE' status
```

**2. Admin Approval Process**

```php
Admin changes user status: 'INACTIVE' → 'ACTIVE' in admin panel
```

**3. User Login Attempt**

```php
POST /login → AuthenticatedSessionController@store() → Authentication succeeds
```

**4. Protected Route Access**

```php
GET /admin/dashboard → Middleware stack: ['auth', 'verified', 'admin', 'preventBackHistory']
```

**5. AdminAccess Middleware Validation**

```php
AdminAccess middleware validates:
1. Authentication status
2. User status (ACTIVE/INACTIVE)
3. User roles (RootUser/Admin/User)
4. Specific permissions for the route
```

**6. Permission-Based Access Control**

```php
Route: admin/dashboard → Permission: dashboard-view → Access Granted/Denied
```

**7. Final Access Decision**

```php
if (hasPermission) → Proceed to controller
else → Access Denied (403)
```

#### 📋 Files Involved in Authorization

| Step | File | Purpose |
|------|------|---------|
| 1 | `RegisteredUserController.php` | Assigns default 'User' role to new users |
| 2 | `AdminAccess.php` | Main authorization middleware |
| 3 | `User.php` model | Role/permission relationships |
| 4 | `routes/backend.php` | Protected route definitions |
| 5 | `DashboardController.php` | Resource access control |
| 6 | Database tables | `roles`, `permissions`, `role_has_permissions` |

#### 🛡️ Authorization Security Levels

Authorization happens in **4 security levels**:

| Level | Check | Middleware/Location | Action if Fails |
|-------|-------|-------------------|-----------------|
| **Level 1** | Authentication | `auth` middleware | Redirect to login |
| **Level 2** | User Status | `AdminAccess` middleware | Access denied |
| **Level 3** | Role Validation | `AdminAccess` middleware | Access denied |
| **Level 4** | Permission Check | `AdminAccess` middleware | Access denied |

#### 🔐 Role & Permission Structure

```php
// Role Hierarchy (from highest to lowest access)
RootUser → Admin → User

// Permission Mapping Example:
Route: admin/users → Permission: user-view
Route: admin/users (POST) → Permission: user-store
Route: admin/users/{user} (PUT) → Permission: user-update
```

#### 📊 Current Role Permissions

| Role | Status | Permissions | Access Level |
|------|--------|-------------|--------------|
| **RootUser** | ACTIVE | All 14 permissions | Full system access |
| **Admin** | ACTIVE | Configurable subset | Partial admin access |
| **User** | INACTIVE → ACTIVE | `dashboard-view` only | Basic dashboard access |

**RootUser Permissions:**
```
- dashboard-view, role-view, role-store, role-update, role-permission
- user-view, user-store, user-update
- employee-view, employee-store, employee-update
- enquiry-view, enquiry-store, enquiry-update
```

**User Permissions:**
```
- dashboard-view (basic dashboard access only)
```

#### 🚦 Authorization Decision Process

```php
// AdminAccess Middleware Logic Flow:

1. Authentication Check
   if (!Auth::check()) → return redirect('/login')

2. User Status Check
   if ($user->status !== 'ACTIVE') → return access_denied()

3. Super Admin Bypass
   if ($user->hasRole(['RootUser', 'SuperAdmin'])) → return next($request)

4. Permission Extraction
   $route = 'admin/dashboard' → $permission = 'dashboard-view'

5. Permission Check
   if ($user->can($permission)) → return next($request)
   else → return access_denied()
```

#### 🔄 User Lifecycle & Authorization States

```mermaid
Registration → INACTIVE + User Role → Admin Approval → ACTIVE Status → Login → Permission Check → Access Granted/Denied
```

**State Transitions:**
```php
// New User Registration
User::create([
    'status' => 'INACTIVE',  // Cannot login
    'role' => 'User'         // Basic permissions when activated
]);

// Admin Activation
$user->update(['status' => 'ACTIVE']); // Can now login

// Role Upgrade (if needed)
$user->assignRole('Admin'); // Gets additional permissions
```

#### ❌ Common Authorization Failures

```php
// Scenario 1: Inactive User
$user->status = 'INACTIVE' → Access denied (even with correct role)

// Scenario 2: Insufficient Role
$user->hasRole('User') but route needs 'Admin' → Access denied

// Scenario 3: Missing Permission
$user->hasRole('Admin') but lacks 'user-delete' permission → Access denied

// Scenario 4: Route Permission Not Found
Route has no mapped permission → Access denied (fail-safe)
```

#### 🛠️ Permission Mapping System

The system uses **dynamic permission mapping** based on route patterns:

```php
// Route Pattern → Permission Pattern
admin/{resource} → {resource}-view
admin/{resource} (POST) → {resource}-store
admin/{resource}/{id} (PUT) → {resource}-update
admin/{resource}/{id} (DELETE) → {resource}-destroy

// Examples:
admin/users → user-view
admin/users (POST) → user-store
admin/users/123 (PUT) → user-update
admin/roles/456/permission → role-permission
```

#### 🔑 Key Authorization Features

- **Role-Based Access Control**: Users assigned roles with specific permissions
- **Dynamic Permission Checking**: Permissions mapped from route patterns
- **Multi-Level Security**: 4-layer validation (auth → status → role → permission)
- **Emergency Access**: RootUser bypasses all permission checks
- **Fail-Safe Design**: Unknown routes/permissions default to access denied
- **Comprehensive Logging**: All access attempts logged for security audit

#### 📝 Authorization Middleware Configuration

```php
// Protected Routes Configuration
Route::middleware(['auth', 'verified', 'admin', 'preventBackHistory'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        // Each route automatically mapped to required permission
    });
});
```

**Middleware Responsibilities:**
- **auth**: Basic authentication validation
- **verified**: Email verification (if enabled)
- **admin**: Role-based authorization (our custom middleware)
- **preventBackHistory**: Security measure for sensitive pages

#### 🎯 Authorization Security Benefits

- **✅ Granular Access Control**: Each route protected by specific permissions
- **✅ Role Hierarchy**: Clear privilege levels from User to RootUser
- **✅ Admin Approval Workflow**: New users require explicit activation
- **✅ Dynamic Permission System**: Easy to add new protected routes
- **✅ Emergency Access**: RootUser always has system access
- **✅ Comprehensive Audit**: All authorization decisions logged
- **✅ Fail-Safe Default**: Deny access when in doubt


</details>

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>



<br>

<br>

#

## Setting Profile Information Update

<details>
<summary><strong>👤 Complete Profile Management Flow</strong> (Click to expand)</summary>

### Complete Profile Management Flow
Understanding how authenticated users can update their personal information (name and email).

#### 🎯 Step-by-Step Profile Update Flow

**1. User accesses profile settings**

```php
GET /settings/profile → ProfileController@edit() → returns Inertia::render('settings/profile')
```

**2. Profile form displayed**

```typescript
resources/js/pages/settings/profile.tsx renders with first_name, last_name, and email fields
```

**3. User modifies information**

```typescript
Form fields auto-populate with current user data from auth.user object
```

**4. User submits form**

```php
PATCH /settings/profile → ProfileController@update(ProfileUpdateRequest $request)
```

**5. Backend validation**

```php
ProfileUpdateRequest validates:
- first_name: required, string, max:255
- last_name: nullable, string, max:255
- email: required, unique (except current user), valid email format
```

**6. Email verification check**

```php
if (email changed) → set email_verified_at = null → triggers verification process
```

**7. Profile updated & saved**

```php
$user->fill($validated_data) → $user->save() → redirect back to profile page
```

**8. Success feedback displayed**

```typescript
"Saved" message shows briefly using Transition component
```

#### 📋 Files Involved in Profile Update

| Step | File | Purpose |
|------|------|---------|
| 1 | `routes/settings.php` | Defines profile routes |
| 2 | `ProfileController.php` | Handles profile logic |
| 3 | `settings/profile.tsx` | Profile form UI |
| 4 | `ProfileUpdateRequest.php` | Validates profile data |
| 5 | `User.php` model | User data storage |
| 6 | `SettingsLayout.tsx` | Settings page wrapper |

#### 🛡️ Profile Update Security Features

Profile updates include these security measures:

| Security Layer | Check | Purpose |
|----------------|-------|---------|
| **Authentication** | Must be logged in | Only auth users can update profile |
| **Ownership** | Only update own profile | Users can't modify other profiles |
| **Email Uniqueness** | Email must be unique in system | Prevents duplicate accounts |
| **Data Validation** | Required fields & format validation | Ensures data integrity |
| **Email Verification** | Reset verification on email change | Confirms new email ownership |

#### 🔐 Profile Form Fields & Validation

```typescript
// Profile Form Structure
type ProfileForm = {
    first_name: string;  // Required, max 255 chars
    last_name: string;   // Optional, max 255 chars
    email: string;       // Required, valid email, unique
};

// Form Initialization
const { data, setData, patch } = useForm<ProfileForm>({
    first_name: auth.user.first_name || '',
    last_name: auth.user.last_name || '',
    email: auth.user.email,
});
```

#### 📊 Profile Update Process Details

**Frontend Form Handling:**
```typescript
// Real-time data binding
onChange={(e) => setData('first_name', e.target.value)}

// Form submission
patch(route('profile.update'), { preserveScroll: true });

// Success feedback
{recentlySuccessful && <p>Saved</p>}
```

**Backend Processing:**
```php
// Validation through ProfileUpdateRequest
$validated = $request->validated();

// Fill user model with new data
$request->user()->fill($validated);

// Handle email changes
if ($request->user()->isDirty('email')) {
    $request->user()->email_verified_at = null;
}

// Save changes
$request->user()->save();
```

#### 🔄 Email Verification Workflow

When user changes email address:

```php
1. New email is saved to database
2. email_verified_at is set to NULL
3. User must verify new email address
4. Verification link sent to new email
5. Until verified, some features may be restricted
```

**Email Verification UI:**
```typescript
{mustVerifyEmail && auth.user.email_verified_at === null && (
    <div>
        <p>Your email address is unverified.</p>
        <Link href={route('verification.send')}>
            Click here to resend verification email
        </Link>
    </div>
)}
```

#### ❌ Common Profile Update Failures

```php
// Validation Failures:

// Empty first name
'first_name' => '' → Error: "The first name field is required"

// Duplicate email
'email' => 'existing@email.com' → Error: "The email has already been taken"

// Invalid email format
'email' => 'not-an-email' → Error: "The email must be a valid email address"

// First name too long
'first_name' => str_repeat('A', 256) → Error: "First name may not be greater than 255 characters"
```

#### 🎨 UI/UX Features

**Responsive Design:**
```typescript
// Mobile: Stacked layout
<div className="grid grid-cols-1 gap-4 md:grid-cols-2">

// Desktop: Side-by-side first_name and last_name
```

**Form Accessibility:**
```typescript
// Proper labels and autocomplete
<Label htmlFor="first_name">First Name</Label>
<Input autoComplete="given-name" />

// Error message display
<InputError message={errors.first_name} />
```

**Visual Feedback:**
```typescript
// Loading state
<Button disabled={processing}>Save</Button>

// Success animation
<Transition show={recentlySuccessful}>
    <p>Saved</p>
</Transition>
```

#### 🔑 Key Profile Management Features

- **✅ Real-time Validation**: Immediate feedback on form errors
- **✅ Responsive Design**: Works on mobile and desktop devices
- **✅ Auto-population**: Form loads with current user data
- **✅ Email Verification**: Automatically triggers when email changes
- **✅ Security First**: Only authenticated users can access
- **✅ Data Integrity**: Comprehensive validation rules
- **✅ User Experience**: Smooth transitions and clear feedback

#### 📝 Profile Settings Integration

Profile updates integrate with the broader settings system:

```typescript
// Settings Layout Navigation
<SettingsLayout>
    {/* Profile form content */}
    <DeleteUser /> {/* Account deletion component */}
</SettingsLayout>
```

**Settings Navigation:**
- Profile Information (current page)
- Password Update
- Account Deletion
- Other settings sections

#### 🛠️ Technical Implementation Notes

**Inertia.js Integration:**
```php
// Controller returns Inertia response
return Inertia::render('settings/profile', [
    'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
    'status' => session('status'),
]);
```

**Form Request Validation:**
```php
// Custom validation rules in ProfileUpdateRequest
Rule::unique(User::class)->ignore($this->user()->id)
// Allows user to keep their current email while preventing duplicates
```

**State Management:**
```typescript
// Inertia useForm hook manages form state
const { data, setData, patch, errors, processing, recentlySuccessful } = useForm();
```

This profile management system provides a secure, user-friendly way for authenticated users to maintain their personal information while ensuring data integrity and security throughout the process.

</details>

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>



<br>

<br>

#

## Setting Password Update

<details>
<summary><strong>🔐 Complete Password Management Flow</strong> (Click to expand)</summary>

### Complete Password Management Flow
Understanding how authenticated users can securely update their account passwords.

#### 🎯 Step-by-Step Password Update Flow

**1. User accesses password settings**

```php
GET /settings/password → PasswordController@edit() → returns Inertia::render('settings/password')
```

**2. Password form displayed**

```typescript
resources/js/pages/settings/password.tsx renders with current_password, password, and password_confirmation fields
```

**3. User enters password information**

```typescript
Form requires: current password, new password, and password confirmation
```

**4. User submits form**

```php
PUT /settings/password → PasswordController@update(Request $request)
```

**5. Backend validation**

```php
Request validates:
- current_password: required, must match current user password
- password: required, must meet Password::defaults() rules, confirmed
- password_confirmation: must match password field
```

**6. Password hashing & update**

```php
Hash::make($validated['password']) → User password updated in database
```

**7. Success response**

```php
return back() → redirects to password settings page
```

**8. Success feedback displayed**

```typescript
"Saved" message shows briefly using Transition component
```

#### 📋 Files Involved in Password Update

| Step | File | Purpose |
|------|------|---------|
| 1 | `routes/settings.php` | Defines password routes |
| 2 | `Settings/PasswordController.php` | Handles password logic |
| 3 | `settings/password.tsx` | Password form UI |
| 4 | `Request` validation | Built-in Laravel validation |
| 5 | `User.php` model | Password storage |
| 6 | `SettingsLayout.tsx` | Settings page wrapper |

#### 🛡️ Password Update Security Features

Password updates include multiple security layers:

| Security Layer | Check | Purpose |
|----------------|-------|---------|
| **Authentication** | Must be logged in | Only auth users can change password |
| **Current Password** | Must provide current password | Prevents unauthorized changes |
| **Password Strength** | Must meet Password::defaults() rules | Ensures strong passwords |
| **Confirmation** | Must confirm new password | Prevents typos |
| **Secure Hashing** | bcrypt/Argon2 hashing | Passwords stored securely |

#### 🔐 Password Form Fields & Validation

```typescript
// Password Form Structure
type PasswordForm = {
    current_password: string;      // Required, must match current
    password: string;              // Required, must meet strength rules
    password_confirmation: string; // Required, must match password
};

// Form Initialization
const { data, setData, put } = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});
```

#### 📊 Password Validation Rules

**Laravel Password::defaults() includes:**
```php
- Minimum 8 characters
- At least one uppercase letter (A-Z)
- At least one lowercase letter (a-z)
- At least one number (0-9)
- At least one special character (!@#$%^&*)
- Must be confirmed (password_confirmation field)
```

**Additional Security Rules:**
```php
- current_password: Must match user's existing password
- Cannot be empty or null
- Real-time validation on frontend
- Secure hashing using Hash::make()
```

#### 🔄 Password Update Process Details

**Frontend Form Handling:**
```typescript
// Real-time data binding
onChange={(e) => setData('password', e.target.value)}

// Form submission with error handling
put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => reset(),
    onError: (errors) => {
        // Focus on error fields and reset sensitive data
        if (errors.password) {
            reset('password', 'password_confirmation');
            passwordInput.current?.focus();
        }
        if (errors.current_password) {
            reset('current_password');
            currentPasswordInput.current?.focus();
        }
    },
});
```

**Backend Processing:**
```php
// Comprehensive validation
$validated = $request->validate([
    'current_password' => ['required', 'current_password'],
    'password' => ['required', Password::defaults(), 'confirmed'],
]);

// Secure password update
$request->user()->update([
    'password' => Hash::make($validated['password']),
]);

// Return to settings page
return back();
```

#### ❌ Common Password Update Failures

```php
// Validation Failures:

// Wrong current password
'current_password' => 'wrongpassword' → Error: "The current password is incorrect"

// Weak new password
'password' => '123' → Error: "Password must be at least 8 characters"

// Password mismatch
'password' => 'NewPass123!'
'password_confirmation' => 'DifferentPass' → Error: "Password confirmation does not match"

// Missing uppercase letter
'password' => 'newpass123!' → Error: "Password must contain at least one uppercase letter"

// Missing special character
'password' => 'NewPass123' → Error: "Password must contain at least one special character"
```

#### 🎨 UI/UX Features

**Security-First Design:**
```typescript
// All password fields use type="password"
<Input type="password" autoComplete="current-password" />
<Input type="password" autoComplete="new-password" />

// Auto-focus on error fields
passwordInput.current?.focus();
currentPasswordInput.current?.focus();
```

**Form Accessibility:**
```typescript
// Proper labels and autocomplete
<Label htmlFor="current_password">Current password</Label>
<Input autoComplete="current-password" />

// Clear error messages
<InputError message={errors.current_password} />
```

**Visual Feedback:**
```typescript
// Loading state prevents multiple submissions
<Button disabled={processing}>Save password</Button>

// Success animation
<Transition show={recentlySuccessful}>
    <p>Saved</p>
</Transition>
```

#### 🔒 Advanced Security Features

**Error Handling & Data Protection:**
```typescript
// Automatic form reset on success
onSuccess: () => reset()

// Selective field reset on errors
onError: (errors) => {
    if (errors.password) {
        reset('password', 'password_confirmation');
    }
    if (errors.current_password) {
        reset('current_password');
    }
}
```

**Backend Security Measures:**
```php
// Current password verification
'current_password' => ['required', 'current_password']

// Strong password enforcement
'password' => ['required', Password::defaults(), 'confirmed']

// Secure hashing algorithm
Hash::make($validated['password'])
```

#### 🔑 Key Password Management Features

- **✅ Current Password Verification**: Must know current password to change
- **✅ Strong Password Enforcement**: Laravel's Password::defaults() rules
- **✅ Password Confirmation**: Prevents typos with confirmation field
- **✅ Secure Hashing**: Uses Laravel's secure Hash::make() method
- **✅ Error Handling**: Smart field focus and data reset on errors
- **✅ Form Security**: Auto-reset sensitive data after submission
- **✅ User Experience**: Clear feedback and loading states

#### 📝 Password Settings Integration

Password updates integrate with the broader settings system:

```typescript
// Settings Layout Navigation
<SettingsLayout>
    <HeadingSmall
        title="Update password"
        description="Ensure your account is using a long, random password to stay secure"
    />
    {/* Password form content */}
</SettingsLayout>
```

**Settings Navigation:**
- Profile Information
- Password Update (current page)
- Account Deletion
- Other settings sections

#### 🛠️ Technical Implementation Notes

**Inertia.js Integration:**
```php
// Simple controller response
return Inertia::render('settings/password');
// No additional data needed - form handles state internally
```

**Laravel Validation Rules:**
```php
// Built-in current password validation
'current_password' => ['required', 'current_password']

// Laravel's default password strength rules
Password::defaults() // Configurable in AppServiceProvider if needed
```

**State Management:**
```typescript
// Inertia useForm hook with refs for focus management
const { data, setData, put, reset, errors, processing, recentlySuccessful } = useForm();
const passwordInput = useRef<HTMLInputElement>(null);
const currentPasswordInput = useRef<HTMLInputElement>(null);
```

#### 🎯 Password Security Benefits

- **✅ Multi-Layer Validation**: Current password + strength rules + confirmation
- **✅ Secure Storage**: Passwords hashed with Laravel's secure algorithms
- **✅ User-Friendly Errors**: Clear validation messages and field focus
- **✅ Form Security**: Automatic sensitive data cleanup
- **✅ Authentication Required**: Only logged-in users can change passwords
- **✅ Real-Time Feedback**: Immediate validation and success confirmation
- **✅ Accessibility Compliant**: Proper labels, autocomplete, and focus management

This password management system provides enterprise-grade security while maintaining an excellent user experience, ensuring users can easily maintain strong, secure passwords for their accounts.

</details>

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>



<br>

<br>

#

## Permission Based UI Implementation

<details>
<summary><strong>Permissions management</strong> (Click to expand)</summary>

### Summary
Implemented a comprehensive permission-based UI system that will solve your 403 error issues and provide a better user experience. Here's what has been created:

### Components Created

#### 1. `PermissionDenied` Component
**Path:** `/resources/js/components/permission-denied.tsx`
- Reusable component that shows a professional "Access Denied" message
- Configurable title, message, and description
- Includes contact information for requesting permissions
- Follows your monochrome design system

#### 2. `Error` Page Component
**Path:** `/resources/js/pages/error.tsx`
- Global error page that handles 403, 404, 500, and other HTTP errors
- Uses PermissionDenied component for 403 errors specifically
- Provides helpful navigation options (Dashboard, Back, Refresh)

#### 3. `usePermissions` Hook
**Path:** `/resources/js/hooks/use-permissions.ts`
- React hook for checking user permissions and roles
- Provides functions like `hasPermission()`, `hasRole()`, `isRootUser()`, etc.
- Automatically handles RootUser role (bypasses all permission checks)

#### 4. `ProtectedSection` Component
**Path:** `/resources/js/components/protected-section.tsx`
- Wrapper component that conditionally renders content based on permissions
- Can protect individual sections within pages
- Shows permission denied message or custom fallback
- Supports multiple permissions/roles with AND/OR logic

### Backend Integration

#### Exception Handler Updated
**Path:** `/app/Exceptions/Handler.php`
- Added proper 403 error handling for Inertia requests
- Automatically renders the error page with permission denied message
- Handles both `AccessDeniedHttpException` and `AuthorizationException`

### Usage Examples

#### 1. Protecting Navigation (Already Updated)
```tsx
// app-sidebar.tsx - Navigation items are filtered by permissions
const hasAccess = hasPermission(auth.user, 'user-view');
```

#### 2. Protecting Page Sections (Already Updated in Users Index)
```tsx
// Protect Create Button
<ProtectedSection permission="user-store" showDeniedMessage={false}>
    <Link href={route('admin.users.create')}>
        <Button>Create User</Button>
    </Link>
</ProtectedSection>

// Protect Edit/Delete in Dropdown
<ProtectedSection permission="user-update" showDeniedMessage={false}>
    <DropdownMenuItem asChild>
        <Link href={route('admin.users.edit', user.id)}>Edit User</Link>
    </DropdownMenuItem>
</ProtectedSection>

// Protect Status Toggle
<ProtectedSection permission="user-update" showDeniedMessage={false}>
    <Switch checked={isActive} onCheckedChange={() => handleStatusChange(user.id, user.status)} />
</ProtectedSection>
```

#### 3. Using the Permission Hook
```tsx
import { usePermissions } from '@/hooks/use-permissions';

function MyComponent() {
    const { hasPermission, hasRole, isRootUser } = usePermissions();

    if (!hasPermission('user-view')) {
        return <PermissionDenied />;
    }

    return (
        <div>
            {hasPermission('user-store') && <CreateButton />}
            {hasPermission('user-update') && <EditButton />}
            {(hasRole('Admin') || isRootUser()) && <AdminOnlySection />}
        </div>
    );
}
```

### Controller Protection (Recommended)

Add this to your controllers to prevent 403 errors at the source:

```php
// In UserController.php constructor
public function __construct()
{
    $this->middleware('permission:user-view')->only(['index', 'show']);
    $this->middleware('permission:user-store')->only(['create', 'store']);
    $this->middleware('permission:user-update')->only(['edit', 'update', 'changeStatus']);
}
```

### Benefits

1. **Better UX**: Users see helpful messages instead of generic 403 errors
2. **Consistent Design**: All permission denied messages follow your design system
3. **Reusable**: Components can be used across all pages and sections
4. **Flexible**: Can protect entire pages, sections, or individual buttons
5. **Type Safe**: Full TypeScript support with proper interfaces
6. **Performance**: Frontend filtering prevents unnecessary clicks and requests

### Next Steps

1. **Add middleware to controllers** to prevent backend 403 errors
2. **Apply ProtectedSection** to other pages (roles, employees, enquiries)
3. **Test with different user roles** to ensure proper permission handling
4. **Customize messages** per section if needed

This system ensures users will never see a blank 403 error page again, and will always know why they can't access something and who to contact for help!

</details>

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>



<br>

<br>

#
