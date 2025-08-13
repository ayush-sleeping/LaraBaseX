# 🛡️ Permission-Based UI Implementation Documentation (LaraBaseX)

This guide explains the permission-based UI system in LaraBaseX, with clear steps, code references, and integration notes for developers.

#

## 1. Where is the Code?

- **Components:**
  - `/resources/js/components/permission-denied.tsx` (PermissionDenied message)
  - `/resources/js/components/protected-section.tsx` (ProtectedSection wrapper)
- **Pages:**
  - `/resources/js/pages/error.tsx` (global error page)
- **Hooks:**
  - `/resources/js/hooks/use-permissions.ts` (permission/role checking)
- **Backend:**
  - `/app/Exceptions/Handler.php` (403 error handling)
- **Controllers:**
  - Add middleware in controller constructors for backend protection

#

## 2. What Does It Do?

- Shows professional "Access Denied" messages for 403 errors
- Protects pages, sections, and buttons based on user permissions/roles
- Provides reusable components for permission checks
- Integrates with backend for consistent error handling
- Improves UX by preventing blank error pages

#

## 3. How Does It Work?

### Step-by-Step Usage

1. **PermissionDenied Component**
   - Reusable message for denied access
   - Configurable title, message, description, and contact info
2. **Error Page Component**
   - Handles 403, 404, 500 errors globally
   - Uses PermissionDenied for 403 errors
   - Provides navigation options (Dashboard, Back, Refresh)
3. **usePermissions Hook**
   - Checks user permissions and roles
   - Functions: `hasPermission()`, `hasRole()`, `isRootUser()`
   - RootUser bypasses all permission checks
4. **ProtectedSection Component**
   - Conditionally renders content based on permissions/roles
   - Protects sections, buttons, or entire pages
   - Supports AND/OR logic and custom fallback messages
5. **Backend Integration**
   - Exception handler renders error page for 403 errors
   - Controllers use middleware for backend permission checks

#

## 4. Usage Examples

**Protecting Navigation:**
```tsx
// app-sidebar.tsx
const hasAccess = hasPermission(auth.user, 'user-view');
```

**Protecting Page Sections:**
```tsx
<ProtectedSection permission="user-store" showDeniedMessage={false}>
    <Link href={route('admin.users.create')}>
        <Button>Create User</Button>
    </Link>
</ProtectedSection>

<ProtectedSection permission="user-update" showDeniedMessage={false}>
    <DropdownMenuItem asChild>
        <Link href={route('admin.users.edit', user.id)}>Edit User</Link>
    </DropdownMenuItem>
</ProtectedSection>

<ProtectedSection permission="user-update" showDeniedMessage={false}>
    <Switch checked={isActive} onCheckedChange={() => handleStatusChange(user.id, user.status)} />
</ProtectedSection>
```

**Using the Permission Hook:**
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

**Controller Protection (Recommended):**
```php
// In UserController.php constructor
public function __construct()
{
    $this->middleware('permission:user-view')->only(['index', 'show']);
    $this->middleware('permission:user-store')->only(['create', 'store']);
    $this->middleware('permission:user-update')->only(['edit', 'update', 'changeStatus']);
}
```

#

## 5. Backend Integration

- Exception handler (`Handler.php`) renders error page for 403 errors
- Handles both `AccessDeniedHttpException` and `AuthorizationException`
- Ensures frontend and backend show consistent permission denied messages

#

## 6. Benefits

- Better UX: Users see helpful messages instead of generic 403 errors
- Consistent Design: All permission denied messages follow your design system
- Reusable: Components can be used across all pages and sections
- Flexible: Can protect entire pages, sections, or individual buttons
- Type Safe: Full TypeScript support with proper interfaces
- Performance: Frontend filtering prevents unnecessary clicks and requests

#

## 7. Permission & Role Seeding (Backend)

- **Seeder Reference:** Permissions, roles, and default users are seeded via [`database/seeders/PermissionSeeder.php`].
- **Syncing:** Permission names and mappings are defined in the seeder and should be kept in sync with controller middleware and frontend permission checks.
- **Adding New Permissions/Roles:** Always add new permissions or roles to the seeder for consistency across backend and frontend.
- **Best Practice:** Review and update the seeder whenever you change permission logic in controllers or UI to avoid mismatches.

This ensures a single source of truth for permissions and roles, making the system reliable and maintainable.

#

> All steps, files, and integration points above are strictly based on the LaraBaseX codebase. Use this guide to understand, audit, and extend permission-based UI in your project.
