<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Models\Permissiongroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    private $permissions = [
        'Dashboard' => [
            'controller' => 'Backend\DashboardController',
            'permissions' => [
                'dashboard-view' => [
                    'index',
                ],
            ]
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
            ]
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
            ]
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
            ]
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
            ]
        ],
        //End of Permission Arr
    ];


    public $roles = [
        'RootUser' => [

            #Dashboard
            'dashboard-view',

            #Roles
            'role-view',
            'role-store',
            'role-update',
            'role-permission',

            #User
            'user-view',
            'user-store',
            'user-update',

            #Employee
            'employee-view',
            'employee-store',
            'employee-update',

            #Enquiry
            'enquiry-view',
            'enquiry-store',
            'enquiry-update',

            //End of Role Permission
        ],
        'Admin' => [

            #Dashboard
            'dashboard-view',

            #Roles
            'role-view',
            'role-store',
            'role-update',
            'role-permission',

            #User
            'user-view',
            'user-store',
            'user-update',

            #Employee
            'employee-view',
            'employee-store',
            'employee-update',

            #Enquiry
            'enquiry-view',
            'enquiry-store',
            'enquiry-update',

            //End of Role Permission

        ],
        'User' => [
            #Dashboard - basic users can view dashboard
            'dashboard-view',

            #Basic permissions for regular users
            //End of Role Permission
        ],
    ];

    private $users = [
        [
            'first_name'  => 'Root',
            'last_name'  => 'User',
            'roles'  => [
                'RootUser',
            ],
            'mobile'  => '1234567890',
            'email'  => 'rootuser@example.com',
            'password' => 'root@123@user'
        ],
    ];

    public function run(){
        #Groups & Permission
        $this->deletePermissions();
        $this->createPermissions();
        #Create Roles
        $this->createRoles();
        #Create Users
        $this->createUsers();
    }

    private function deletePermissions(){
        $permissions = Permission::all();
        foreach($permissions as $permission){
            $delete_permission = true;
            foreach($this->permissions as $group => $data){
                foreach($data['permissions'] as $p_name => $methods){
                    if($p_name == $permission->name){
                        $delete_permission = false;
                    }
                }
            }
            if($delete_permission){
                $permission->delete();
            }
        }

        $permission_groups = Permissiongroup::all();
        foreach($permission_groups as $permission_group){
            $delete_permissiongroup = true;
            foreach($this->permissions as $group => $data){
                if($group == $permission_group->name){
                    $delete_permissiongroup = false;
                }
            }
            if($delete_permissiongroup){
                $permission_group->delete();
            }
        }
    }

    private function createPermissions(){
        foreach($this->permissions as $group => $data){
            $permissiongroup = Permissiongroup::where('name',$group)->first();
            if(!$permissiongroup){
                $permissiongroup = new Permissiongroup;
                $permissiongroup->name = $group;
                $permissiongroup->controller = $data['controller'];
                $permissiongroup->save();
            }else{
                $permissiongroup->controller = $data['controller'];
                $permissiongroup->save();
            }

            foreach($data['permissions'] as $permissions_name => $methods){
                $permission = Permission::where('permissiongroup_id',$permissiongroup->id)->where('name',$permissions_name)->first();
                if(!$permission){
                    $permission = new Permission;
                    $permission->permissiongroup_id = $permissiongroup->id;
                    $permission->name = $permissions_name;
                    $permission->methods = $methods;
                    $permission->guard_name = config('auth.defaults.guard');
                    $permission->save();
                }else{
                    $permission->methods = $methods;
                    $permission->save();
                }
            }
        }
    }

    private function createRoles(){
        foreach($this->roles as $role_name => $permissions){
            $role = Role::where('name',$role_name)->first();
            if(!$role){
                $role = new Role;
                $role->name = $role_name;
                $role->guard_name = config('auth.defaults.guard');
                $role->save();
            }

            $permission_ids = Permission::whereIn('name',$permissions)->pluck('id');
            $role->syncPermissions($permission_ids);
        }
    }

    private function createUsers(){
        foreach($this->users as $data){
            $user = User::where('email',$data['email'])->first();
            if(!$user){
                $user = new User;
                $user->email = $data['email'];
                $user->mobile = $data['mobile'];
                $user->first_name = $data['first_name'];
                $user->last_name = $data['last_name'];
                $user->password = Hash::make($data['password']);
                $user->save();
            }else{
                $user->first_name = $data['first_name'];
                $user->last_name = $data['last_name'];
                $user->password = Hash::make($data['password']);
                $user->save();
            }

            #Assign Role & Sync Permission to User
            $user->assignRole($data['roles']);

            #Sync All Roles Permissions to User
            $all_permissions = collect();
            foreach($data['roles'] as $role_name){
                $role = Role::where('name',$role_name)->first();
                $permissions = $role->permissions()->get();
                foreach($permissions as $permission){
                    $all_permissions->push($permission);
                }
            }
            $user->syncPermissions($all_permissions);
        }
    }

}
