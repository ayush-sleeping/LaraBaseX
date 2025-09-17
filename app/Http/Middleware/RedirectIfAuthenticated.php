<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * CODE STRUCTURE SUMMARY:
 * RedirectIfAuthenticated Middleware
 * Redirect authenticated users away from guest-only routes
 */
class RedirectIfAuthenticated
{
    // Redirect authenticated users away from guest-only routes
    public function handle(Request $request, Closure $next, string ...$guards): mixed
    {
        /** @var array<string|null> $guards */
        $guards = empty($guards) ? [null] : $guards;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}

// This middleware redirects authenticated users away from guest-only routes (like login/register).
// If a user is already authenticated, they are redirected to the application's home route.
// You can customize the redirect path by changing RouteServiceProvider::HOME.
