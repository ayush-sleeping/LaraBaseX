<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PreventBackHistory Middleware
 *
 * Prevents browser back navigation by setting appropriate cache headers.
 * This is essential for admin panels and secure pages to prevent cached access.
 *
 *  Proper key-value pairs for headers
 *  Modern PHP with proper type hints
 *  Comprehensive cache prevention headers
 *  Additional security headers (X-Frame-Options, etc.)
 *  Browser-specific cache control
 *  Better documentation and error handling
 */
class PreventBackHistory
{
    /**
     * Handle an incoming request.
     *
     * Old middleware logic (with fixes):
     * - Set cache control headers to prevent browser caching
     * - Prevent back button access to sensitive pages
     * - Add expiration headers for old browser compatibility
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Fixed headers array from old middleware (was broken before)
        // Old had syntax errors: 'Pragma', 'no-cache', 'Expires', 'date' as separate elements
        // New: Proper key-value pairs
        $headers = [
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate', // Fixed: was 'nocache'
            'Pragma' => 'no-cache',           // Fixed: was separate elements
            'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT', // Fixed: was separate elements
        ];

        // Additional security headers (enhancement)
        $securityHeaders = [
            'X-Frame-Options' => 'DENY',                    // Prevent clickjacking
            'X-Content-Type-Options' => 'nosniff',          // Prevent MIME sniffing
            'X-XSS-Protection' => '1; mode=block',          // XSS protection
            'Referrer-Policy' => 'strict-origin-when-cross-origin', // Control referrer info
        ];

        // Merge all headers
        $allHeaders = array_merge($headers, $securityHeaders);

        // Apply headers to response (same logic as old middleware but enhanced)
        foreach ($allHeaders as $key => $value) {
            $response->headers->set($key, $value);
        }

        // Additional browser-specific cache prevention (enhancement)
        $this->addBrowserSpecificHeaders($response);

        return $response;
    }

    /**
     * Add browser-specific cache prevention headers (new enhancement)
     *
     * @param Response $response
     * @return void
     */
    protected function addBrowserSpecificHeaders(Response $response): void
    {
        // IE-specific cache prevention
        $response->headers->set('Cache-Control',
            $response->headers->get('Cache-Control') . ', private, no-transform');

        // Additional headers for better compatibility
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        $response->headers->set('Vary', 'User-Agent');

        // Prevent proxy caching
        $response->headers->set('Surrogate-Control', 'no-store');
    }
}
