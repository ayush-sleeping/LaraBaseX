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
