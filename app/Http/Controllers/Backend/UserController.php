<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CODE STRUCTURE SUMMARY:
 * UserController ( Handles user management for the backend administration, Provides CRUD operations, role/permission management, and data tables for users. )
 * Display a listing of users
 * Show the form for creating a new user
 * Store a newly created user
 * Display the specified user
 * Show the form for editing the specified user
 * Update the specified user
 * Remove the specified user
 * Change user status
 * Get user statistics
 * Reset user password
 * Export users to CSV
 * Validation rules for user operations
 * Custom validation messages
 */
class UserController extends Controller
{
    /* Display a listing of users :: */
    public function index(Request $request): Response
    {
        $query = User::query();
        // Apply status filter if provided
        if ($request->filled('status') && in_array($request->status, ['ACTIVE', 'INACTIVE'])) {
            $query->where('status', $request->status);
        }
        $users = $query->get();

        return Inertia::render('backend/users/index', [
            'users' => $users,
            'filters' => [
                'status' => $request->status,
            ],
        ]);
    }

    /* Show the form for creating a new user :: */
    public function create(): Response
    {
        $systemRoles = get_system_roles();
        // $roles = Role::whereNotIn('name', $systemRoles)->select('id', 'name')->orderBy('name')->get();
        $roles = Role::whereNotIn('name', ['Admin', 'RootUser'])->select('id', 'name')->orderBy('name')->get();

        return Inertia::render('backend/users/create', compact('roles'));
    }

    /* Store a newly created user in storage :: */
    public function store(Request $request): RedirectResponse
    {
        // dd($request->all());
        $validated = $request->validate($this->rules, $this->customMessages);
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }
        // Hash password, create user, assign roles and sync permissions
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        $this->assignRolesAndPermissions($user, $validated['roles']);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /* Display the specified user :: */
    public function show(User $user): Response
    {
        $user->load([
            'roles:id,name',
            'permissions:id,name',
            'creator:id,first_name,last_name',
            'updator:id,first_name,last_name',
        ]);

        return Inertia::render('backend/users/show', compact('user'));
    }

    /* Show the form for editing the specified user :: */
    public function edit(User $user): Response
    {
        $systemRoles = get_system_roles();
        $roles = Role::whereNotIn('name', $systemRoles)->select('id', 'name', 'guard_name')->orderBy('name')->get();
        $user->load(['roles:id,name']); // Load user's current roles

        return Inertia::render('backend/users/edit', compact('user', 'roles'));
    }

    /* Update the specified user in storage :: */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Modify unique rules to exclude current user
        $this->rules['email'] = 'required|email|max:255|unique:users,email,'.$user->id;
        $this->rules['mobile'] = 'required|string|max:15|regex:/^[0-9+\-\s]+$/|unique:users,mobile,'.$user->id;
        $this->rules['password'] = 'nullable|string|min:8|confirmed';
        $validated = $request->validate($this->rules, $this->customMessages);
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }
        // Hash password if provided
        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        // Update user, Update roles and sync permissions
        $user->update($validated);
        $this->assignRolesAndPermissions($user, $validated['roles']);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /* Private Function for : Assign roles and sync permissions for a user :: */
    /**
     * Assign roles and sync permissions for a user
     *
     * @param  array<int>  $roleIds
     */
    private function assignRolesAndPermissions(User $user, array $roleIds): void
    {
        // Clear existing roles first
        $user->syncRoles([]);

        // Get role names from IDs and assign roles
        $roles = Role::whereIn('id', $roleIds)->get();
        $roleNames = $roles->pluck('name')->toArray();
        $user->assignRole($roleNames);

        // Collect all permissions from assigned roles
        $permissions = [];
        foreach ($roles as $role) {
            $rolePermissions = $role->permissions()->pluck('id')->toArray();
            $permissions = array_merge($permissions, $rolePermissions);
        }

        // Sync permissions (remove duplicates)
        $user->syncPermissions(array_unique($permissions));
    }

    /* Remove the specified user from storage :: */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deletion of RootUser or current user
        if ($user->hasRole('RootUser') || $user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete this user');
        }
        // Delete avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /* Change user status (ACTIVE/INACTIVE) :: */
    public function changeStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);
        $user = User::findOrFail($validated['user_id']);
        // Prevent deactivating RootUser or current user
        if ($user->hasRole('RootUser') || $user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot change status of this user');
        }
        $user->status = $validated['status'];
        $user->save();

        return redirect()->route('admin.users.index')->with('success', $user->first_name.' has been marked '.strtolower($validated['status']).' successfully');
    }

    /* Get user statistics :: */
    public function stats(): JsonResponse
    {
        $systemRoles = get_system_roles();
        $stats = [
            'total' => User::whereHas('roles', function ($q) use ($systemRoles) {
                $q->whereIn('name', $systemRoles)->where('name', '!=', 'RootUser');
            })->count(),
            'active' => User::whereHas('roles', function ($q) use ($systemRoles) {
                $q->whereIn('name', $systemRoles)->where('name', '!=', 'RootUser');
            })->where('status', 'ACTIVE')->count(),
            'inactive' => User::whereHas('roles', function ($q) use ($systemRoles) {
                $q->whereIn('name', $systemRoles)->where('name', '!=', 'RootUser');
            })->where('status', 'INACTIVE')->count(),
            'today' => User::whereHas('roles', function ($q) use ($systemRoles) {
                $q->whereIn('name', $systemRoles)->where('name', '!=', 'RootUser');
            })->whereDate('created_at', today())->count(),
            'this_month' => User::whereHas('roles', function ($q) use ($systemRoles) {
                $q->whereIn('name', $systemRoles)->where('name', '!=', 'RootUser');
            })->whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json([
            'status' => 'success',
            'stats' => $stats,
        ], 200);
    }

    /* Reset user password :: */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password is required',
            'password.min' => 'Password should be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
        ]);
        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully',
        ], 200);
    }

    /* Export users to CSV :: */
    public function export(Request $request): StreamedResponse
    {
        $systemRoles = get_system_roles();
        $query = User::whereHas('roles', function ($q) use ($systemRoles) {
            $q->whereIn('name', $systemRoles)->where('name', '!=', 'RootUser');
        })->with('roles:name')->orderBy('first_name');
        // Apply filters if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $users = $query->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_'.now()->format('Y-m-d').'.csv"',
        ];

        return response()->stream(function () use ($users) {
            $handle = fopen('php://output', 'w');
            // Add CSV headers
            fputcsv($handle, [
                'ID', 'First Name', 'Last Name', 'Email', 'Mobile',
                'Status', 'Roles', 'Created At', 'Updated At',
            ]);
            // Add data rows
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $user->mobile,
                    $user->status,
                    $user->roles->pluck('name')->implode(', '),
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Validation rules for user operations
     *
     * @var array<string, mixed>
     */
    private array $rules = [
        'first_name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
        'last_name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
        'email' => 'required|email|max:255|unique:users,email',
        'mobile' => 'required|string|max:15|regex:/^[0-9+\-\s]+$/|unique:users,mobile',
        'password' => 'required|string|min:8|confirmed',
        'roles' => 'required|array|min:1',
        'roles.*' => 'exists:roles,id',
        'status' => 'required|in:ACTIVE,INACTIVE',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    /**
     * Custom validation messages
     *
     * @var array<string, string>
     */
    private array $customMessages = [
        'first_name.required' => 'First name is required',
        'first_name.regex' => 'First name should contain only letters, spaces, and hyphens',
        'last_name.required' => 'Last name is required',
        'last_name.regex' => 'Last name should contain only letters, spaces, and hyphens',
        'email.required' => 'Email is required',
        'email.email' => 'Email should be a valid email address',
        'email.unique' => 'This email is already registered',
        'mobile.required' => 'Mobile number is required',
        'mobile.regex' => 'Mobile number should contain only numbers',
        'mobile.unique' => 'This mobile number is already registered',
        'password.required' => 'Password is required',
        'password.min' => 'Password should be at least 8 characters',
        'password.confirmed' => 'Password confirmation does not match',
        'roles.required' => 'At least one role is required',
        'roles.min' => 'At least one role must be selected',
        'status.required' => 'Status is required',
        'status.in' => 'Status must be either ACTIVE or INACTIVE',
        'avatar.image' => 'Avatar must be an image file',
        'avatar.max' => 'Avatar size must not exceed 2MB',
    ];
}
