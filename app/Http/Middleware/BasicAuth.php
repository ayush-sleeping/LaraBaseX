<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * CODE STRUCTURE SUMMARY:
 * BasicAuth Middleware
 * Handle an incoming request
 * Get expected token from environment configuration
 * Extract token from request
 * Validate the provided token
 * Return authentication failed response
 * Log failed authentication attempt
 * Log successful authentication
 */
class BasicAuth
{
    /**
     * Handle an incoming request.
     *
     * Enhanced version of old middleware logic:
     * Old: if ($request->authtoken) { if ($request->authtoken != '1234') { ... } }
     * New: Multiple token sources, environment config, better validation
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the expected auth token from environment (security )
        $expectedToken = $this->getExpectedToken();

        if (! $expectedToken) {
            Log::error('BasicAuth: No auth token configured in environment');

            return $this->authenticationFailed($request, 'Authentication not configured');
        }
        // Get token from request (multiple sources - )
        $providedToken = $this->extractTokenFromRequest($request);

        if (! $providedToken) {
            $this->logFailedAttempt($request, 'no_token_provided');

            return $this->authenticationFailed($request, 'Authentication token required');
        }

        // Validate token (same logic as old middleware but enhanced)
        if (! $this->validateToken($providedToken, $expectedToken)) {
            $this->logFailedAttempt($request, 'invalid_token');

            return $this->authenticationFailed($request, 'Invalid authentication token');
        }

        // Log successful authentication
        $this->logSuccessfulAuth($request);

        return $next($request);
    }

    /**
     * Get expected token from environment configuration
     *
     * Move from hardcoded '1234' to environment variable
     */
    protected function getExpectedToken(): ?string
    {
        return config('auth.basic_token') ?? config('app.basic_auth_token');
    }

    /**
     * Extract token from request (multiple sources)
     *
     * middleware only checked: $request->authtoken
     * Enhanced: Check multiple possible sources
     */
    protected function extractTokenFromRequest(Request $request): ?string
    {
        // Check request parameter (same as old middleware)
        if ($request->has('authtoken')) {
            return $request->authtoken;
        }

        // Check Authorization header
        if ($request->hasHeader('Authorization')) {
            $header = $request->header('Authorization');
            if (str_starts_with($header, 'Bearer ')) {
                return substr($header, 7);
            }
            if (str_starts_with($header, 'Basic ')) {
                return substr($header, 6);
            }
        }

        // Check custom header
        if ($request->hasHeader('X-Auth-Token')) {
            return $request->header('X-Auth-Token');
        }

        // Check API key header
        if ($request->hasHeader('X-API-Key')) {
            return $request->header('X-API-Key');
        }

        return null;
    }

    /**
     * Validate the provided token
     *
     * Same logic as old middleware: if ($request->authtoken != $authtoken)
     * But with additional security checks
     */
    protected function validateToken(string $providedToken, string $expectedToken): bool
    {
        // Basic validation (same as old middleware)
        if ($providedToken !== $expectedToken) {
            return false;
        }

        // Additional security: Check token length
        if (strlen($providedToken) < 8) {
            Log::warning('BasicAuth: Token too short, possible brute force attempt');

            return false;
        }

        return true;
    }

    /**
     * Return authentication failed response
     *
     * Enhanced version of old: return response()->json(['message' => 'Authentication failed'], 401);
     */
    protected function authenticationFailed(Request $request, string $message): Response
    {
        // Same JSON response format as old middleware
        return response()->json([
            'status' => 'error',
            'message' => 'Authentication failed',
            'details' => $message,
            'timestamp' => now()->toISOString(),
        ], 401);
    }

    /* Log failed authentication attempt */
    protected function logFailedAttempt(Request $request, string $reason): void
    {
        Log::warning('BasicAuth: Authentication failed', [
            'reason' => $reason,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /* Log successful authentication */
    protected function logSuccessfulAuth(Request $request): void
    {
        Log::info('BasicAuth: Authentication successful', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toISOString(),
        ]);
    }
}
