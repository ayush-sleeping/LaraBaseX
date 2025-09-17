<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Permissiongroup;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * CODE STRUCTURE SUMMARY:
 * RoleController ( Handles role management for the backend administration, Provides CRUD operations, permission management, and data tables for roles. )
 * Display a listing of roles
 * Show the form for creating a new role
 * Store a newly created role
 * Display the specified role
 * Show the form for editing the specified role
 * Update the specified role
 * Remove the specified role
 * Show role permissions management page
 * Update role permissions
 * Get role statistics
 * Clone a role with its permissions
 * Get users assigned to a role
 * Validation rules for role operations
 * Custom validation messages
 */
class RoleController extends Controller
{
    /* Display a listing of roles. */
    public function index(): Response
    {
        $roles = Role::all();

        return Inertia::render('backend/roles/index', compact('roles'));
    }

    /*  Show the form for creating a new role. */
    public function create(): Response
    {
        $guards = config('auth.guards', []);

        return Inertia::render('backend/roles/create', [
            'guards' => array_keys($guards),
        ]);
    }

    /* Store a newly created role in storage. */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules, $this->customMessages);
        // Set default guard if not provided
        $validated['guard_name'] = $validated['guard_name'] ?? config('auth.defaults.guard');
        $role = Role::create($validated);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    /* Display the specified role. */
    public function show(Role $role): Response
    {
        $role->load([
            'createdBy:id,first_name,last_name',
            'updatedBy:id,first_name,last_name',
            'permissions:id,name,guard_name',
        ]);
        $role->loadCount(['permissions', 'users']);

        return Inertia::render('backend/roles/show', ['role' => $role]);
    }

    /* Show the form for editing the specified role. */
    public function edit(Role $role): Response
    {
        return Inertia::render('backend/roles/edit', compact('role'));
    }

    /* Update the specified role in storage. */
    public function update(Request $request, Role $role): RedirectResponse
    {
        // Modify unique rule to exclude current role
        $this->rules['name'] = 'required|string|max:125|regex:/^[\pL\s\-]+$/u|unique:roles,name,'.$role->id;
        $validated = $request->validate($this->rules, $this->customMessages);
        $role->update($validated);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    /* Remove the specified role from storage. */
    public function destroy(Role $role): RedirectResponse
    {
        // Check if role has users assigned
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete role that has users assigned to it.');
        }
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    /* Show role permissions management page. */
    public function permissionsShow(Role $role): Response
    {
        $user = auth()->user();
        // Load role with permissions
        $role->load(['permissions:id,name,guard_name,permissiongroup_id']);
        // Get permission groups based on user role
        if ($user->hasRole('RootUser')) {
            $permissionGroups = Permissiongroup::whereNotIn('name', ['PermissionGroup', 'Permission'])
                ->with(['permissions:id,name,guard_name,permissiongroup_id'])
                ->orderBy('name')
                ->get();
        } else {
            $permissionGroups = Permissiongroup::whereNotIn('name', ['PermissionGroup', 'Permission', 'Crud', 'User'])
                ->with(['permissions:id,name,guard_name,permissiongroup_id'])
                ->orderBy('name')
                ->get();
        }
        // Get all permissions
        $permissions = Permission::select('id', 'name', 'guard_name', 'permissiongroup_id')
            ->orderBy('name')
            ->get();
        // Get role permissions IDs for easy checking
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return Inertia::render('backend/roles/permissions', [
            'role' => $role,
            'permissionGroups' => $permissionGroups,
            'permissions' => $permissions,
            'rolePermissionIds' => $rolePermissionIds,
        ]);
    }

    /* Update role permissions. */
    public function permissionsUpdate(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        $permissionIds = $validated['permissions'] ?? [];
        // Sync role permissions
        $role->syncPermissions($permissionIds);
        // Update users with this role
        $users = User::role($role->name)->get();
        foreach ($users as $user) {
            $user->syncPermissions($permissionIds);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role permissions updated successfully.');
    }

    /* Get role statistics. */
    public function stats(): JsonResponse
    {
        $systemRoles = get_system_roles();
        $stats = [
            'total' => Role::whereNotIn('name', $systemRoles)->count(),
            'with_permissions' => Role::whereNotIn('name', $systemRoles)
                ->whereHas('permissions')
                ->count(),
            'without_permissions' => Role::whereNotIn('name', $systemRoles)
                ->whereDoesntHave('permissions')
                ->count(),
            'guards' => Role::whereNotIn('name', $systemRoles)
                ->select('guard_name')
                ->distinct()
                ->pluck('guard_name')
                ->toArray(),
        ];

        return response()->json([
            'status' => 'success',
            'stats' => $stats,
        ], 200);
    }

    /* Clone a role with its permissions. */
    public function clone(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:125|regex:/^[\pL\s\-]+$/u|unique:roles',
            'guard_name' => 'nullable|string|max:125',
        ], $this->customMessages);

        $validated['guard_name'] = $validated['guard_name'] ?? $role->guard_name;
        // Create new role
        $newRole = Role::create($validated);
        // Copy permissions
        $permissions = $role->permissions->pluck('id')->toArray();
        $newRole->syncPermissions($permissions);

        return response()->json([
            'status' => 'success',
            'message' => 'Role cloned successfully',
            'role' => $newRole->load(['createdBy:id,first_name,last_name', 'updatedBy:id,first_name,last_name']),
        ], 201);
    }

    /* Get users assigned to a role. */
    public function users(Role $role): JsonResponse
    {
        $users = $role->users()
            ->select('id', 'first_name', 'last_name', 'email', 'status')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'users' => $users,
        ], 200);
    }

    /**
     * Validation rules for role operations.
     *
     * @var array<string, string>
     */
    private array $rules = [
        'name' => 'required|string|max:125|regex:/^[\pL\s\-]+$/u|unique:roles',
        'guard_name' => 'nullable|string|max:125',
    ];

    /**
     * Custom validation messages.
     *
     * @var array<string, string>
     */
    private array $customMessages = [
        'name.required' => 'Role name is required',
        'name.unique' => 'The role name has already been taken',
        'name.regex' => 'The role name should contain only letters, spaces, and hyphens',
        'name.max' => 'The role name must not exceed 125 characters',
        'guard_name.max' => 'The guard name must not exceed 125 characters',
    ];
}
