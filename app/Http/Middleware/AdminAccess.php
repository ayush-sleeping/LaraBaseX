<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Permission;

/**
 * AdminAccess Middleware
 * Provides comprehensive permission-based access control for backend administration.
 *
 * Key Features :
 *  User authentication validation
 *  User status checking (ACTIVE/INACTIVE)
 *  Dynamic permission checking based on controller@method
 *  Route action parsing and controller matching
 *  Method-level permission validation
 *
 *  Type hints and modern PHP syntax
 *  Comprehensive logging and audit trail
 *  Better error handling and responses
 *  Support for API and web responses
 *  Emergency access for SuperAdmin/RootUser
 *  Detailed documentation and comments
 *  Security improvements and rate limiting consideration
 */
class AdminAccess
{
    /**
     * System roles that bypass permission checks (emergency access)
     * These roles have full access to all admin functions
     */
    /**
     * @var array<int, string>
     */
    protected array $superAdminRoles = ['SuperAdmin', 'RootUser'];

    /**
     * Handle an incoming request.
     *
     * This is the main method that processes all the checks from your old middleware:
     * 1. Authentication check (if user exists)
     * 2. User status validation (must be ACTIVE)
     * 3. Permission-based access control (controller@method matching)
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard Optional guard name
     * @return Response
     */
    public function handle(Request $request, Closure $next, ?string $guard = null): Response
    {
        // STEP 1: Authentication Check (same as old middleware)
        if (!Auth::guard($guard)->check()) {
            Log::warning('AdminAccess: Unauthenticated access attempt', [
                'ip' => $request->ip(),
                'route' => Route::currentRouteName(),
                'user_agent' => $request->userAgent(),
            ]);

            Auth::logout();
            return $this->redirectToLogin($request, 'Authentication required');
        }

        $user = Auth::guard($guard)->user();

        // STEP 2: User Status Check (same logic as old middleware)
        if ($user->status !== 'ACTIVE') {
            Log::warning('AdminAccess: Inactive user access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'status' => $user->status,
                'ip' => $request->ip(),
                'route' => Route::currentRouteName(),
            ]);

            Auth::logout();
            return $this->redirectToLogin($request, 'You are not allowed to view this page. Please contact admin.');
        }

        // STEP 3: Super Admin Emergency Access
        // Give full access to SuperAdmin/RootUser roles (enhancement)
        if ($this->isSuperAdmin($user)) {
            $this->logAccess($request, $user, 'super_admin_access');
            return $next($request);
        }

        // STEP 4: Permission-Based Access Control (enhanced version of old logic)
        $routeInfo = $this->extractRouteInformation($request);

        if (!$routeInfo) {
            Log::error('AdminAccess: Could not extract route information', [
                'user_id' => $user->id,
                'route' => Route::currentRouteName(),
                'action' => Route::currentRouteAction(),
            ]);
            return $this->accessDenied($request, 'Route information unavailable');
        }

        // STEP 5: Check Permission Access (same logic as old middleware but enhanced)
        if ($this->hasPermissionAccess($user, $routeInfo)) {
            $this->logAccess($request, $user, 'permission_granted', $routeInfo);
            return $next($request);
        }

        // STEP 6: Access Denied (enhanced error handling)
        $this->logAccessDenied($request, $user, $routeInfo);
        return $this->accessDenied($request, 'Insufficient permissions');
    }

    /**
     * Check if user has super admin privileges
     *
     * @param mixed $user
     * @return bool
     */
    protected function isSuperAdmin($user): bool
    {
        if (!method_exists($user, 'hasAnyRole')) {
            return false;
        }

        return $user->hasAnyRole($this->superAdminRoles);
    }

    /**
     * Extract route information (enhanced version of old middleware logic)
     *
     * Old middleware logic:
     * $currentAction = \Route::currentRouteAction();
     * list($controller, $method) = explode('@', $currentAction);
     * $controller = str_replace('App\Http\Controllers\\','',$controller);
     *
     * @param Request $request
     * @return array|null
     */
    /**
     * @return array<string, string>|null
     */
    protected function extractRouteInformation(Request $request): ?array
    {
        $currentAction = Route::currentRouteAction();

        if (!$currentAction || !str_contains($currentAction, '@')) {
            return null;
        }

        // Same logic as old middleware but with better error handling
        [$controllerClass, $method] = explode('@', $currentAction);

        // Clean controller name (same as old middleware)
        $controller = str_replace('App\\Http\\Controllers\\', '', $controllerClass);

        return [
            'controller' => $controller,
            'method' => $method,
            'full_controller' => $controllerClass,
            'route_name' => Route::currentRouteName(),
        ];
    }

    /**
     * Check if user has permission access (exact same logic as old middleware)
     *
     * Old middleware logic:
     * $allow_user = false;
     * $permissions = \Auth::user()->getAllPermissions()->pluck('id');
     * $permissions = \App\Models\Permission::whereIn('id',$permissions)->get();
     * foreach($permissions as $permission){
     *     if($permission->permissiongroup->controller == $controller){
     *         foreach($permission->methods as $permission_method){
     *             if($permission_method == $method){
     *                 $allow_user = true;
     *             }
     *         }
     *     }
     * }
     *
     * @param mixed $user
     * @param array $routeInfo
     * @return bool
     */
    /**
     * @param mixed $user
     * @param array<string, string> $routeInfo
     */
    protected function hasPermissionAccess($user, array $routeInfo): bool
    {
        if (!method_exists($user, 'getAllPermissions')) {
            return false;
        }

        $allowUser = false; // Same variable name as old middleware

        // Get user permissions (same as old middleware)
        $permissionIds = $user->getAllPermissions()->pluck('id');
        $permissions = Permission::whereIn('id', $permissionIds)->get();

        // Check each permission (exact same logic as old middleware)
        foreach ($permissions as $permission) {
            if ($permission->permissiongroup->controller == $routeInfo['controller']) {
                foreach ($permission->methods as $permissionMethod) {
                    if ($permissionMethod == $routeInfo['method']) {
                        $allowUser = true;
                        break 2; // Break out of both loops
                    }
                }
            }
        }

        return $allowUser;
    }

    /**
     * Handle redirect to login (enhanced version of old middleware redirect)
     *
     * Old middleware logic:
     * return \Redirect::back()->withErrors(['message']);
     *
     * @param Request $request
     * @param string $message
     * @return Response
     */
    protected function redirectToLogin(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $message
            ], 401);
        }

        // Enhanced redirect logic with proper route checking
        $loginRoute = route('login');
        return redirect($loginRoute)->withErrors([$message]);
    }

    /**
     * Handle access denied (enhanced version of old middleware abort)
     *
     * Old middleware logic:
     * return abort(403);
     *
     * @param Request $request
     * @param string $message
     * @return Response
     */
    protected function accessDenied(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $message
            ], 403);
        }

        abort(403, $message);
    }

    /**
     * Log successful access (new enhancement)
     *
     * @param Request $request
     * @param mixed $user
     * @param string $accessType
     * @param array|null $routeInfo
     * @return void
     */
    /**
     * @param Request $request
     * @param mixed $user
     * @param string $accessType
     * @param array<string, string>|null $routeInfo
     */
    protected function logAccess(Request $request, $user, string $accessType, ?array $routeInfo = null): void
    {
        Log::info('AdminAccess: Access granted', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'access_type' => $accessType,
            'route_info' => $routeInfo,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log access denied (new enhancement)
     *
     * @param Request $request
     * @param mixed $user
     * @param array $routeInfo
     * @return void
     */
    /**
     * @param Request $request
     * @param mixed $user
     * @param array<string, string> $routeInfo
     */
    protected function logAccessDenied(Request $request, $user, array $routeInfo): void
    {
        Log::warning('AdminAccess: Access denied', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'route_info' => $routeInfo,
            'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'user_roles' => $user->getRoleNames()->toArray(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }
}
