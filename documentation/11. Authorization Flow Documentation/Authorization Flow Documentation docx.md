# 🛡️ Authorization Flow Documentation (LaraBaseX)

This guide explains the complete role-based access control (RBAC) and authorization process in LaraBaseX, with clear steps, code references, and security notes for developers.

#

## 1. Where is the Code?

- **Controllers:**
  - `app/Http/Controllers/Auth/RegisteredUserController.php` (role assignment)
  - `app/Http/Controllers/DashboardController.php` (resource access)
- **Middleware:**
  - `app/Http/Middleware/AdminAccess.php` (main authorization logic)
  - `app/Http/Middleware/admin.php`, `preventBackHistory.php`, etc.
- **Models:**
  - `app/Models/User.php` (role/permission relationships)
- **Routes:**
  - `routes/backend.php` (protected route definitions)
- **Database:**
  - `roles`, `permissions`, `role_has_permissions` tables

#

## 2. What Does It Do?

- Assigns roles to users on registration
- Requires admin approval for activation
- Authenticates users and checks status
- Protects routes with multi-level middleware
- Validates roles and permissions for each route
- Maps route patterns to permissions dynamically
- Logs all access attempts for audit

#

## 3. How Does It Work?

### Step-by-Step Authorization Flow

1. **User Registration with Role Assignment**
   - `POST /register` → `RegisteredUserController@store()` → User created with 'User' role and 'INACTIVE' status
2. **Admin Approval Process**
   - Admin changes user status: 'INACTIVE' → 'ACTIVE' in admin panel
3. **User Login Attempt**
   - `POST /login` → `AuthenticatedSessionController@store()` → Authentication succeeds
4. **Protected Route Access**
   - `GET /admin/dashboard` → Middleware stack: ['auth', 'verified', 'admin', 'preventBackHistory']
5. **AdminAccess Middleware Validation**
   - Validates authentication, user status, roles, and permissions
6. **Permission-Based Access Control**
   - Route: admin/dashboard → Permission: dashboard-view → Access Granted/Denied
7. **Final Access Decision**
   - If user has permission → Proceed to controller
   - Else → Access Denied (403)

#

## 4. Files Involved in Authorization

| Step | File | Purpose |
| --- | --- | --- |
| 1 | `RegisteredUserController.php` | Assigns default 'User' role to new users |
| 2 | `AdminAccess.php` | Main authorization middleware |
| 3 | `User.php` model | Role/permission relationships |
| 4 | `routes/backend.php` | Protected route definitions |
| 5 | `DashboardController.php` | Resource access control |
| 6 | Database tables | `roles`, `permissions`, `role_has_permissions` |

#

## 5. Authorization Security Levels

Authorization happens in **4 security levels**:

| Level | Check | Middleware/Location | Action if Fails |
| --- | --- | --- | --- |
| **Level 1** | Authentication | `auth` middleware | Redirect to login |
| **Level 2** | User Status | `AdminAccess` middleware | Access denied |
| **Level 3** | Role Validation | `AdminAccess` middleware | Access denied |
| **Level 4** | Permission Check | `AdminAccess` middleware | Access denied |

#

## 6. Role & Permission Structure

- **Role Hierarchy:** RootUser → Admin → User
- **Permission Mapping Example:**
  - Route: admin/users → Permission: user-view
  - Route: admin/users (POST) → Permission: user-store
  - Route: admin/users/{user} (PUT) → Permission: user-update

#

## 7. Current Role Permissions

| Role | Status | Permissions | Access Level |
| --- | --- | --- | --- |
| **RootUser** | ACTIVE | All 14 permissions | Full system access |
| **Admin** | ACTIVE | Configurable subset | Partial admin access |
| **User** | INACTIVE → ACTIVE | `dashboard-view` only | Basic dashboard access |

**RootUser Permissions:**
- dashboard-view, role-view, role-store, role-update, role-permission
- user-view, user-store, user-update
- employee-view, employee-store, employee-update
- enquiry-view, enquiry-store, enquiry-update

**User Permissions:**
- dashboard-view (basic dashboard access only)

#

## 8. Authorization Decision Process

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

#

## 9. User Lifecycle & Authorization States

- Registration → INACTIVE + User Role → Admin Approval → ACTIVE Status → Login → Permission Check → Access Granted/Denied

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

#

## 10. Common Authorization Failures

- Inactive user: Access denied (even with correct role)
- Insufficient role: User has 'User' role but route needs 'Admin'
- Missing permission: Admin role but lacks specific permission
- Route permission not found: Fail-safe, access denied

#

## 11. Permission Mapping System

- **Dynamic permission mapping** based on route patterns:
  - admin/{resource} → {resource}-view
  - admin/{resource} (POST) → {resource}-store
  - admin/{resource}/{id} (PUT) → {resource}-update
  - admin/{resource}/{id} (DELETE) → {resource}-destroy

#

## 12. Key Authorization Features

- Role-Based Access Control: Users assigned roles with specific permissions
- Dynamic Permission Checking: Permissions mapped from route patterns
- Multi-Level Security: 4-layer validation (auth → status → role → permission)
- Emergency Access: RootUser bypasses all permission checks
- Fail-Safe Design: Unknown routes/permissions default to access denied
- Comprehensive Logging: All access attempts logged for security audit

#

## 13. Authorization Middleware Configuration

```php
// Protected Routes Configuration
Route::middleware(['auth', 'verified', 'admin', 'preventBackHistory'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        // Each route automatically mapped to required permission
    });
});
```
- **auth**: Basic authentication validation
- **verified**: Email verification (if enabled)
- **admin**: Role-based authorization (custom middleware)
- **preventBackHistory**: Security measure for sensitive pages

#

## 14. Authorization Security Benefits

- Granular Access Control: Each route protected by specific permissions
- Role Hierarchy: Clear privilege levels from User to RootUser
- Admin Approval Workflow: New users require explicit activation
- Dynamic Permission System: Easy to add new protected routes
- Emergency Access: RootUser always has system access
- Comprehensive Audit: All authorization decisions logged
- Fail-Safe Default: Deny access when in doubt

#

> All steps, files, and security checks above are strictly based on the LaraBaseX codebase. Use this guide to understand, audit, and extend authorization in your project.
