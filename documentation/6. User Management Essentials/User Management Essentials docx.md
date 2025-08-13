# 👤 User Management Essentials (LaraBaseX)

This section documents how LaraBaseX implements core user management features, strictly based on the actual codebase.

#

## 6.1 Auth APIs

LaraBaseX provides authentication APIs for login, logout, registration, and session management using Laravel Sanctum and Inertia.js.

**How it works:**
- Login, registration, and logout are handled via dedicated controllers and Inertia pages.
- Session is managed securely with HTTP-only cookies.

**Where implemented:**
- Backend: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`, `RegisteredUserController.php`, `LogoutController.php`
- Frontend: `resources/js/pages/auth/login.tsx`, `register.tsx`, `forgot-password.tsx`, `reset-password.tsx`
- Routes: `routes/auth.php`, `routes/web.php`

**How to test:**
- Use the login/register forms in the frontend and verify session/cookie creation.
- Call API endpoints (e.g., `/login`, `/register`) and check responses.

#

## 6.2 Password Management

Password management includes password reset, change, and email verification flows.

**How it works:**
- Users can request password reset links, change their password, and verify their email.
- Controllers handle validation and token management.

**Where implemented:**
- Backend: `app/Http/Controllers/Auth/PasswordController.php`, `PasswordResetLinkController.php`, `NewPasswordController.php`, `EmailVerificationNotificationController.php`
- Frontend: `resources/js/pages/auth/forgot-password.tsx`, `reset-password.tsx`, `verify-email.tsx`, `settings/password.tsx`
- Routes: `routes/auth.php`, `routes/web.php`

**How to test:**
- Use the "Forgot Password" form to request a reset link and follow the flow.
- Change password from the settings page and verify update.

#

## 6.3 Roles & Permissions

Role and permission management is powered by the Spatie Laravel Permission package.

**How it works:**
- Roles and permissions are assigned to users and checked for access control.
- Controllers and models manage role/permission assignment and checks.

**Where implemented:**
- Backend: `app/Models/Role.php`, `Permission.php`, `User.php`, `app/Http/Controllers/Backend/RoleController.php`, `PermissionController.php`
- Migrations: `database/migrations/2025_08_01_000002_create_roles_table.php`, `2025_08_01_000001_create_permissions_table.php`, etc.
- Config: `config/permission.php`
- Frontend: `resources/js/hooks/use-permissions.ts`, permission-based UI logic

**How to test:**
- Assign roles/permissions to users via backend or UI.
- Test access to protected routes/pages and verify permission enforcement.

#

## 6.4 Login Throttling

Login throttling protects against brute-force attacks by limiting repeated login attempts.

**How it works:**
- Laravel’s built-in throttle middleware is used for login routes.
- Excessive failed attempts result in temporary lockout.

**Where implemented:**
- Backend: `routes/auth.php` (middleware), `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- Config: `config/auth.php` (throttle settings)

**How to test:**
- Attempt multiple failed logins and observe lockout behavior.
- Adjust throttle settings in `config/auth.php` and retest.

#

## 6.5 User Profile

User profile management includes viewing and updating profile info, including avatar upload.

**How it works:**
- Users can view and update their profile, including uploading an avatar.
- Controllers handle profile update logic and file storage.

**Where implemented:**
- Backend: `app/Http/Controllers/Settings/ProfileController.php`, `app/Models/User.php`
- Frontend: `resources/js/pages/settings/profile.tsx`
- Migrations: `database/migrations/2025_08_04_075755_add_avatar_column_to_users_table.php`

**How to test:**
- Update profile info and avatar from the settings/profile page.
- Verify changes in the database and UI.

#

## 6.6 Two-Factor Auth [ WORKING ON IT - NOT IMPLEMENTED YET ]

Two-factor authentication (2FA) adds an extra layer of security for user accounts.

**How it works:**
- (If implemented) Users can enable 2FA, typically via TOTP apps or SMS/email codes.
- Controllers manage 2FA setup, verification, and recovery.

**Where implemented:**
- Backend: (Check for `TwoFactorController.php` or similar, or config/feature flag)
- Frontend: (Check for 2FA setup/verification pages in `resources/js/pages/settings/`)
- Config: (Check for 2FA settings in `config/auth.php` or custom config)

**How to test:**
- Enable 2FA for a user and verify login flow requires second factor.
- Test recovery and disable flows if available.

#

> All features above are strictly based on the LaraBaseX codebase. For more details, see the referenced files and try the flows in a running project.
