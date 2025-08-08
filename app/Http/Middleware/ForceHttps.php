<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ForceHttps Middleware
 *
 * Redirects all HTTP requests to HTTPS in production environment.
 * This provides an additional layer of security beyond URL::forceScheme().
 *
 * Features:
 * ✅ Environment-based HTTPS enforcement
 * ✅ Configurable via APP_FORCE_HTTPS environment variable
 * ✅ 301 permanent redirect for SEO benefits
 * ✅ Skip for local development
 */
class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only enforce HTTPS in production or when explicitly enabled
        if ($this->shouldForceHttps($request)) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }

    /**
     * Determine if HTTPS should be enforced
     */
    private function shouldForceHttps(Request $request): bool
    {
        // Skip if already HTTPS
        if ($request->isSecure()) {
            return false;
        }

        // Skip for local development
        if (app()->environment('local')) {
            return false;
        }

        // Force HTTPS in production
        if (app()->environment('production')) {
            return true;
        }

        // Or force HTTPS when explicitly configured
        return config('app.force_https', false);
    }
}
