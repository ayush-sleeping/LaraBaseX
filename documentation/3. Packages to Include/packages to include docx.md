## Packages to Include


<div id="top"></div>

<br>

### Table of Contents
1. [Spatie Laravel Permission](#spatie-laravel-permission)
2. [Laravel Sanctum or Passport](#laravel-sanctum-or-passport)
3. [Laravel Telescope](#laravel-telescope)
4. [Laravel Debugbar](#laravel-debugbar)
5. [Spatie Backup](#spatie-backup)
6. [Spatie Activity Log](#spatie-activity-log)

<br>

<br>

#

## Spatie Laravel Permission
> Spatie Laravel Permission – roles/permissions


**What this topic is:**
A package for managing user roles and permissions in Laravel.

**Why we are using it:**
- Enables flexible role-based access control (RBAC).
- Allows assigning multiple roles and granular permissions to users.
- Simplifies permission checks in controllers, middleware, and UI.

**What it does in our project:**
- Manages roles like RootUser, Admin, User.
- Assigns permissions to roles (e.g., dashboard-view, user-update).
- Syncs permissions with backend middleware and frontend UI checks.
- Used for protecting routes, actions, and UI elements based on user roles/permissions.

**Because of this, files of code:**
- `database/seeders/PermissionSeeder.php`: Seeds roles, permissions, and assigns them to users.
- `app/Models/User.php`: Uses Spatie traits for role/permission relationships.
- `app/Http/Middleware/AdminAccess.php`: Checks permissions for route access.
- `app/Http/Controllers/Auth/RegisteredUserController.php`: Assigns default role on registration.
- `resources/js/hooks/use-permissions.ts`: Checks permissions in frontend.
- `resources/js/components/protected-section.tsx`: Protects UI sections based on permissions.

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Laravel Sanctum or Passport
> Laravel Sanctum or Passport – token-based auth
**What this topic is:**
Packages for API authentication using tokens (Sanctum for SPA/mobile, Passport for OAuth2).

**Why we are using it:**
- Provides secure token-based authentication for APIs and SPA frontend.
- Supports user login, registration, and session management.
- Enables stateless authentication for mobile or third-party clients.

**What it does in our project:**
- Handles login/logout and token issuance for users.
- Protects API routes using middleware (`auth:sanctum` or `auth:api`).
- Integrates with frontend for secure user sessions.

**Because of this, files of code:**
- `config/auth.php`: Configures guards and providers for Sanctum/Passport.
- `routes/api.php`: Defines protected API routes.
- `app/Http/Controllers/Auth/LoginController.php`: Issues tokens on login.
- `app/Http/Middleware/Authenticate.php`: Checks for valid tokens.
- `resources/js/pages/auth/login.tsx`: Frontend login flow using tokens.

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Laravel Telescope
> Laravel Telescope (local/dev) – debugging, request log
**What this topic is:**
A debugging and monitoring tool for Laravel applications (local/dev only).

**Why we are using it:**
- Provides detailed request, query, and exception logs for development.
- Helps debug issues and monitor application behavior.

**What it does in our project:**
- Logs requests, database queries, exceptions, and more during development.
- Accessible via `/telescope` route (local only).

**Because of this, files of code:**
- `config/telescope.php`: Telescope configuration.
- `app/Providers/TelescopeServiceProvider.php`: Registers Telescope in local/dev.
- `.env.example`: Enables/disables Telescope via environment variable.

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Laravel Debugbar (local/dev)
> Laravel Debugbar (local/dev) – performance analysis

**What this topic is:**
A package for profiling and debugging Laravel applications (local/dev only).

**Why we are using it:**
- Displays debug information in the browser for each request.
- Helps analyze performance, queries, and exceptions.

**What it does in our project:**
- Shows query count, execution time, and other debug info in local/dev.
- Assists in optimizing code and troubleshooting issues.

**Because of this, files of code:**
- `config/debugbar.php`: Debugbar configuration.
- `.env.example`: Enables/disables Debugbar via environment variable.

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Spatie Backup
>  Spatie Backup – scheduled database/file backups

**What this topic is:**
A package for automated backups of database and files.

**Why we are using it:**
- Ensures regular backups for disaster recovery.
- Supports scheduled and manual backup operations.

**What it does in our project:**
- Schedules backups via Laravel scheduler (`app/Console/Kernel.php`).
- Stores backup files in configured storage (local/S3).
- Notifies on backup success/failure.

**Because of this, files of code:**
- `config/backup.php`: Backup configuration.
- `app/Console/Kernel.php`: Schedules backup jobs.
- `.env.example`: Sets backup storage and notification settings.

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Spatie Activity Log
> Spatie Activity Log – audit logs for user actions

**What this topic is:**
A package for recording user actions and changes for auditing.

**Why we are using it:**
- Tracks changes and actions performed by users.
- Provides audit trails for security and compliance.

**What it does in our project:**
- Logs actions like create, update, delete on models.
- Associates logs with users and models for traceability.
- Used for reviewing user activity and debugging issues.

**Because of this, files of code:**
- `config/activitylog.php`: Activity log configuration.
- `app/Models/User.php`: Associates activity logs with users.
- `app/Models/Role.php`: Tracks role creation/updates with creator information.
- `app/Http/Controllers/*`: Logs actions in controllers.


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>
