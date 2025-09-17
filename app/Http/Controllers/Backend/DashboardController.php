<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Enquiry;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * CODE STRUCTURE SUMMARY:
 * DashboardController ( Handles the main dashboard/control panel for the backend administration, Provides overview statistics and main navigation for admins )
 * Display the main dashboard with system statistics
 * Get dashboard statistics for AJAX requests
 * Get system information for admin panel
 */
class DashboardController extends Controller
{
    /* Display the main dashboard with system statistics. */
    public function index(Request $request): Response
    {
        // Get system roles (excluding RootUser for regular counts)
        $systemRoles = get_system_roles();
        // Count users with system roles (excluding RootUser)
        $usersCount = User::whereHas('roles', function ($query) use ($systemRoles) {
            $query->whereIn('name', $systemRoles)
                ->where('name', '!=', 'RootUser');
        })->count();

        // Count employees, Count enquiries, Count total roles, Count total permissions ::
        $employeesCount = Employee::count();
        $enquiriesCount = Enquiry::count();
        $rolesCount = Role::count();
        $permissionsCount = Permission::count();

        // Recent activities (last 7 days)
        $recentUsers = User::where('created_at', '>=', now()->subDays(7))->count();
        $recentEnquiries = Enquiry::where('created_at', '>=', now()->subDays(7))->count();
        $recentEmployees = Employee::where('created_at', '>=', now()->subDays(7))->count();

        // Latest enquiries for quick overview
        $latestEnquiries = Enquiry::with(['createdBy:id,first_name,last_name'])
            ->latest()
            ->take(5)
            ->get()
            /** @phpstan-ignore-next-line */
            ->map(function (Enquiry $enquiry): array {
                    return [
                        'id' => $enquiry->hashid,
                        'name' => $enquiry->first_name.' '.$enquiry->last_name,
                        'email' => $enquiry->email,
                        'mobile' => $enquiry->mobile,
                        'message' => Str::limit($enquiry->message, 100),
                        'created_at' => $enquiry->created_at->format('M d, Y'),
                        'created_by' => $enquiry->createdBy ? $enquiry->createdBy->first_name.' '.$enquiry->createdBy->last_name : null,
                    ];
                }
            );

        // Latest employees
        /**
         * @var \Illuminate\Support\Collection<int, array<string, mixed>> $latestEmployees
         */
        $latestEmployees = Employee::with(['user:id,first_name,last_name,email', 'createdBy:id,first_name,last_name'])
            ->latest()
            ->take(5)
            ->get()
            ->map(
                /**
                 * @param  Employee  $employee
                 * @return array<string, mixed>
                 */
                function ($employee) {
                    return [
                        'id' => $employee->hashid,
                        'emp_id' => $employee->emp_id,
                        'name' => $employee->user ? $employee->user->first_name.' '.$employee->user->last_name : 'N/A',
                        'email' => $employee->user ? $employee->user->email : $employee->personal_email,
                        'designation' => $employee->designation,
                        'created_at' => $employee->created_at->format('M d, Y'),
                        'created_by' => $employee->createdBy ? $employee->createdBy->first_name.' '.$employee->createdBy->last_name : null,
                    ];
                }
            );

        // Statistics for charts/widgets
        $stats = [
            'users' => [
                'total' => $usersCount,
                'recent' => $recentUsers,
                'label' => 'System Users',
                'icon' => 'users',
                'color' => 'blue',
            ],
            'employees' => [
                'total' => $employeesCount,
                'recent' => $recentEmployees,
                'label' => 'Employees',
                'icon' => 'briefcase',
                'color' => 'green',
            ],
            'enquiries' => [
                'total' => $enquiriesCount,
                'recent' => $recentEnquiries,
                'label' => 'Enquiries',
                'icon' => 'mail',
                'color' => 'yellow',
            ],
            'roles' => [
                'total' => $rolesCount,
                'recent' => 0, // Roles don't change frequently
                'label' => 'Roles',
                'icon' => 'shield',
                'color' => 'purple',
            ],
            'permissions' => [
                'total' => $permissionsCount,
                'recent' => 0, // Permissions don't change frequently
                'label' => 'Permissions',
                'icon' => 'key',
                'color' => 'red',
            ],
        ];

        return Inertia::render('backend/dashboard/index', [
            'stats' => $stats,
            'latestEnquiries' => $latestEnquiries,
            'latestEmployees' => $latestEmployees,
            'systemInfo' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_time' => now()->format('M d, Y H:i:s'),
                'timezone' => config('app.timezone'),
            ],
        ]);
    }

    /* Get dashboard statistics for AJAX requests. */
    public function getStats(Request $request): JsonResponse
    {
        $systemRoles = get_system_roles();

        return response()->json([
            'users_count' => User::whereHas('roles', function ($query) use ($systemRoles) {
                $query->whereIn('name', $systemRoles)->where('name', '!=', 'RootUser');
            })->count(),
            'employees_count' => Employee::count(),
            'enquiries_count' => Enquiry::count(),
            'roles_count' => Role::count(),
            'permissions_count' => Permission::count(),
            'recent_enquiries' => Enquiry::where('created_at', '>=', now()->subDays(7))->count(),
            'updated_at' => now()->toISOString(),
        ]);
    }

    /* Get system information for admin panel. */
    public function getSystemInfo(Request $request): JsonResponse
    {
        return response()->json([
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_type' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'mail_driver' => config('mail.default'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'server_time' => now()->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone'),
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2).' MB',
            'memory_peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2).' MB',
        ]);
    }
}
