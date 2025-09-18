<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Permissiongroup;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * CODE STRUCTURE SUMMARY:
 * PermissionSeeder
 * This seeder is responsible for creating the initial permissions and roles in the database.
 * - Predefined permission groups and their permissions
 * - Predefined system roles with assigned permissions
 * - Predefined system users and their roles
 * - Main entry point for seeding permissions, roles, and users
 * - Deletes permissions and permission groups that are NOT in the predefined list
 * - Creates or updates permission groups and their permissions
 * - Creates or updates roles and assigns them their respective permissions
 * - Creates or updates predefined users and assigns them roles & permissions
 */
class PermissionSeeder extends Seeder
{
    /**
     * @var array<string, array<string, mixed>>
     */
    // Predefined permission groups and their permissions
    private array $permissions = [
        'Dashboard' => [
            'controller' => 'Backend\DashboardController',
            'permissions' => [
                'dashboard-view' => [
                    'index',
                ],
            ],
        ],
        'Role' => [
            'controller' => 'Backend\RoleController',
            'permissions' => [
                'role-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'role-store' => [
                    'create',
                    'store',
                ],
                'role-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                ],
                'role-permission' => [
                    'permissionsShow',
                    'permissionsUpdate',
                ],
            ],
        ],
        'User' => [
            'controller' => 'Backend\UserController',
            'permissions' => [
                'user-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'user-store' => [
                    'create',
                    'store',
                ],
                'user-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                ],
            ],
        ],
        'Employee' => [
            'controller' => 'Backend\EmployeeController',
            'permissions' => [
                'employee-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'employee-store' => [
                    'create',
                    'store',
                ],
                'employee-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                ],
            ],
        ],
        'Enquiry' => [
            'controller' => 'Backend\EnquiryController',
            'permissions' => [
                'enquiry-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'enquiry-store' => [

                ],
                'enquiry-update' => [

                ],
            ],
        ],
        'Analytics' => [
            'controller' => 'Backend\AnalyticsController',
            'permissions' => [
                'analytics-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'analytics-store' => [

                ],
                'analytics-update' => [

                ],
            ],
        ],
        // End of Permission Arr
    ];

    /**
     * @var array<string, array<int, string>>
     */

    // Predefined system roles with assigned permissions
    public array $roles = [
        'RootUser' => [
            // Dashboard
            'dashboard-view',

            // Roles
            'role-view',
            'role-store',
            'role-update',
            'role-permission',

            // User
            'user-view',
            'user-store',
            'user-update',

            // Employee
            'employee-view',
            'employee-store',
            'employee-update',

            // Enquiry
            'enquiry-view',
            'enquiry-store',
            'enquiry-update',

            // Analytics
            'analytics-view',
            'analytics-store',
            'analytics-update',

            // End of Role Permission
        ],
        'Admin' => [
            // Dashboard
            'dashboard-view',

            // Roles
            'role-view',
            'role-store',
            'role-update',
            'role-permission',

            // User
            'user-view',
            'user-store',
            'user-update',

            // Employee
            'employee-view',
            'employee-store',
            'employee-update',

            // Enquiry
            'enquiry-view',
            'enquiry-store',
            'enquiry-update',

            // End of Role Permission

        ],
        'User' => [
            // Dashboard - basic users can view dashboard
            'dashboard-view',

            // Basic permissions for regular users
            // End of Role Permission
        ],
    ];

    /**
     * @var array<int, array<string, mixed>>
     */

    // Predefined system users and their roles
    private array $users = [
        [
            'first_name' => 'Root',
            'last_name' => 'User',
            'roles' => [
                'RootUser',
            ],
            'mobile' => '1234567890',
            'email' => 'rootuser@example.com',
            'password' => 'root@123@user',
        ],
    ];

    // Main entry point for seeding permissions, roles, and users
    public function run(): void
    {
        // Groups & Permission
        $this->deletePermissions();
        $this->createPermissions();
        // Create Roles
        $this->createRoles();
        // Create Users
        $this->createUsers();
    }

    // Deletes permissions and permission groups that are NOT in the predefined list
    private function deletePermissions(): void
    {
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $delete_permission = true;
            foreach ($this->permissions as $group => $data) {
                foreach ($data['permissions'] as $p_name => $methods) {
                    if ($p_name == $permission->name) {
                        $delete_permission = false;
                    }
                }
            }
            if ($delete_permission) {
                $permission->delete();
            }
        }

        $permission_groups = Permissiongroup::all();
        foreach ($permission_groups as $permission_group) {
            $delete_permissiongroup = true;
            foreach ($this->permissions as $group => $data) {
                if ($group == $permission_group->name) {
                    $delete_permissiongroup = false;
                }
            }
            if ($delete_permissiongroup) {
                $permission_group->delete();
            }
        }
    }

    // Creates or updates permission groups and their permissions
    private function createPermissions(): void
    {
        foreach ($this->permissions as $group => $data) {
            $permissiongroup = Permissiongroup::where('name', $group)->first();
            if (! $permissiongroup) {
                $permissiongroup = new Permissiongroup;
                $permissiongroup->name = $group;
                $permissiongroup->controller = $data['controller'];
                $permissiongroup->save();
            } else {
                $permissiongroup->controller = $data['controller'];
                $permissiongroup->save();
            }

            foreach ($data['permissions'] as $permissions_name => $methods) {
                $permission = Permission::where('permissiongroup_id', $permissiongroup->id)->where('name', $permissions_name)->first();
                if (! $permission) {
                    $permission = new Permission;
                    $permission->permissiongroup_id = $permissiongroup->id;
                    $permission->name = $permissions_name;
                    $permission->methods = $methods;
                    $permission->guard_name = config('auth.defaults.guard');
                    $permission->save();
                } else {
                    $permission->methods = $methods;
                    $permission->save();
                }
            }
        }
    }

    // Creates or updates roles and assigns them their respective permissions
    private function createRoles(): void
    {
        foreach ($this->roles as $role_name => $permissions) {
            $role = Role::where('name', $role_name)->first();
            if (! $role) {
                $role = new Role;
                $role->name = $role_name;
                $role->guard_name = config('auth.defaults.guard');
                $role->save();
            }

            $permission_ids = Permission::whereIn('name', $permissions)->pluck('id');
            $role->syncPermissions($permission_ids);
        }
    }

    // Creates or updates predefined users and assigns them roles & permissions
    private function createUsers(): void
    {
        foreach ($this->users as $data) {
            $user = User::where('email', $data['email'])->first();
            if (! $user) {
                $user = new User;
                $user->email = $data['email'];
                $user->mobile = $data['mobile'];
                $user->first_name = $data['first_name'];
                $user->last_name = $data['last_name'];
                $user->password = Hash::make($data['password']);
                $user->save();
            } else {
                $user->first_name = $data['first_name'];
                $user->last_name = $data['last_name'];
                $user->password = Hash::make($data['password']);
                $user->save();
            }

            // Assign Role & Sync Permission to User
            $user->assignRole($data['roles']);

            // Sync All Roles Permissions to User
            $all_permissions = collect();
            foreach ($data['roles'] as $role_name) {
                $role = Role::where('name', $role_name)->first();
                $permissions = $role->permissions()->get();
                foreach ($permissions as $permission) {
                    $all_permissions->push($permission);
                }
            }
            $user->syncPermissions($all_permissions);
        }
    }
}
