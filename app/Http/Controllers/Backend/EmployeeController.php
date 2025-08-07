<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * EmployeeController
 * Handles CRUD operations for employees with user management.
 * Integrates with roles and permissions system.
 */
class EmployeeController extends Controller
{
    /* Display a listing of employees :: */
    public function index(Request $request)
    {
        $employees = Employee::with('user:id,first_name,last_name,email,mobile,status')->get();
        return Inertia::render('backend/employees/index', compact('employees'));
    }

    /* Show the form for creating a new employee :: */
    public function create(): Response
    {
        $systemRoles = get_system_roles();
        $roles = Role::whereNotIn('name', $systemRoles)
            ->get()
            ->map(function ($role) {
                return [
                    'name' => $role->name,
                    'display_name' => $role->display_name ?? $role->name,
                ];
            });

        $empId = get_counting_number('Employee', 'EMP', 'emp_id', false);

        return Inertia::render('backend/employees/create', [
            'employee' => null,
            'roles' => $roles,
            'emp_id' => $empId,
            'mode' => 'create'
        ]);
    }

    /* Store a newly created employee :: */
    public function store(Request $request)
    {
        $request->validate($this->rules, $this->customMessages);

        $user = New User;
        $user->fill($request->all());
        $user->password = bcrypt($request->password);
        $user->save();

        $permissions = [];
        $user->assignRole($request->roles);
		foreach($request->roles as $role_name){
			$role = Role::where('name',$role_name)->first();
			array_push($permissions,$role->permissions()->get());
		}
		$user->syncPermissions($permissions);

        $employee = new Employee;
        $employee->fill($request->all());
        $employee->user_id = $user->id;
        $employee->save();

        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully.');
    }

    /* Display the specified employee :: */
    public function show(Employee $employee)
    {
        // Load the user relationship if not already loaded
        $employee->load('user.roles');
        return Inertia::render('backend/employees/show', compact('employee'));
    }

    /* Show the form for editing the specified employee :: */
    public function edit(Employee $employee): Response
    {
        // Load the user relationship if not already loaded
        $employee->load('user.roles');

        $systemRoles = get_system_roles();
        $roles = Role::whereNotIn('name', $systemRoles)
            ->get()
            ->map(function ($role) {
                return [
                    'name' => $role->name,
                    'display_name' => $role->display_name ?? $role->name,
                ];
            });

        return Inertia::render('backend/employees/edit', [
            'employee' => $employee,
            'roles' => $roles,
            'mode' => 'edit'
        ]);
    }

    /* Update the specified employee :: */
    public function update(Request $request, Employee $employee)
    {
        $this->rules['email'] = 'required|email|unique:users,email,' . $employee->user->id;
        $this->rules['personal_email'] = 'nullable|email|unique:employees,personal_email,' . $employee->id;
        $this->rules['mobile'] = 'required|digits:10|unique:users,mobile,' . $employee->user->id;
        $this->rules['password'] = 'nullable|min:6';
        $this->rules['password_confirmation'] = 'nullable|same:password';

        $request->validate($this->rules, $this->customMessages);

        $user = User::find($employee->user->id);
        $user->fill($request->all());
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        $permissions = [];
        $user->syncRoles([]);
        $user->assignRole($request->roles);
        foreach($request->roles as $role_name){
            $role = Role::where('name',$role_name)->first();
            array_push($permissions,$role->permissions()->get());
        }
        $user->syncPermissions($permissions);

        $employee->fill($request->all());
        $employee->save();

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully.');
    }

    /* Remove the specified employee :: */
    public function destroy(string $id)
    {
        $employee = Employee::with('user')->findByHashid($id);

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $userName = $employee->user ? $employee->user->first_name . ' ' . $employee->user->last_name : $employee->emp_id;

            // Delete user (this will cascade delete employee due to foreign key)
            if ($employee->user) {
                $employee->user->delete();
            }
            $employee->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Employee {$userName} deleted successfully"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete employee: ' . $e->getMessage()
            ], 500);
        }
    }

    /* Change employee status (activate/deactivate) :: */
    public function changeStatus(Request $request)
    {
        $request->validate([
            'route_key' => 'required|string',
            'status' => 'required|in:ACTIVE,INACTIVE'
        ]);

        $employee = Employee::with('user')->findByHashid($request->route_key);

        if (!$employee || !$employee->user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }

        try {
            $employee->user->status = $request->status;
            $employee->user->save();

            return response()->json([
                'status' => 'success',
                'message' => $employee->user->first_name . ' has been marked ' . strtolower($request->status) . ' successfully',
                'employee' => [
                    'id' => $employee->hashid,
                    'status' => $employee->user->status
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /* Sync user permissions based on assigned roles :: */
    private function syncUserPermissions(User $user, array $roleNames): void
    {
        $permissions = collect();

        foreach ($roleNames as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $permissions = $permissions->merge($role->permissions);
            }
        }

        $user->syncPermissions($permissions->unique('id'));
    }

    /** @var array<string, string> Validation rules */
    private array $rules = [
        'first_name' => 'required|regex:/^[\pL\s\-]+$/u|max:50',
        'last_name' => 'required|regex:/^[\pL\s\-]+$/u|max:50',
        'email' => 'required|email|unique:users,email',
        'personal_email' => 'nullable|email|unique:employees,personal_email',
        'mobile' => 'required|digits:10|unique:users,mobile',
        'password' => 'required|min:8|confirmed',
        'roles' => 'required|array|min:1',
        'roles.*' => 'exists:roles,name',
        'designation' => 'required|string|max:100',
        'status' => 'required|in:ACTIVE,INACTIVE',
    ];

    /** @var array<string, string> Custom validation messages */
    private array $customMessages = [
        'first_name.required' => 'First Name is required',
        'first_name.regex' => 'First Name should contain only alphabets',
        'last_name.required' => 'Last Name is required',
        'last_name.regex' => 'Last Name should contain only alphabets',
        'email.required' => 'Email is required',
        'email.email' => 'Email should be a valid email',
        'email.unique' => 'Email already exists',
        'personal_email.email' => 'Personal Email should be a valid email',
        'personal_email.unique' => 'Personal Email already exists',
        'mobile.required' => 'Mobile is required',
        'mobile.digits' => 'Mobile should be 10 digits',
        'mobile.unique' => 'Mobile already exists',
        'password.required' => 'Password is required',
        'password.min' => 'Password should be minimum 8 characters',
        'password.confirmed' => 'Password confirmation does not match',
        'roles.required' => 'At least one role is required',
        'designation.required' => 'Designation is required',
        'status.required' => 'Status is required',
    ];
}
