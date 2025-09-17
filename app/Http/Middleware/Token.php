<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * CODE STRUCTURE SUMMARY:
 * Token Middleware ( Validates user authentication via API guard (usually token-based like Passport/Sanctum) )
 * Handle an incoming request
 * Validate token is still valid
 * Return authentication failed response
 * Log failed authentication attempt
 * Log successful API access
 * Get safe headers for logging (exclude sensitive data)
 */
class Token
{
    /**
     * Handle an incoming request.
     *
     * Enhanced version of old middleware logic:
     * Old: if (!\Auth::guard('api')->check()) { return 401; }
     * New: Comprehensive validation with user status and token checks
     */
    public function handle(Request $request, Closure $next, ?string $guard = 'api'): Response
    {
        // Step 1: Check API authentication (same as old middleware but enhanced)
        if (! Auth::guard($guard)->check()) {
            $this->logFailedAttempt($request, 'no_valid_token', $guard);

            return $this->authenticationFailed($request, 'User not authenticated');
        }
        $user = Auth::guard($guard)->user();
        // Step 2: Validate user exists
        if (! $user) {
            $this->logFailedAttempt($request, 'user_not_found', $guard);

            return $this->authenticationFailed($request, 'User not found');
        }
        // Step 3: Check user status
        if (isset($user->status) && $user->status !== 'ACTIVE') {
            $this->logFailedAttempt($request, 'user_inactive', $guard, $user->id);

            return $this->authenticationFailed($request, 'User account is inactive');
        }
        // Step 4: Validate token expiry ( for Passport/Sanctum)
        if (! $this->isTokenValid($request, $user, $guard)) {
            $this->logFailedAttempt($request, 'token_expired', $guard, $user->id);

            return $this->authenticationFailed($request, 'Token has expired');
        }
        // Step 5: Log successful access
        $this->logSuccessfulAccess($request, $user, $guard);
        // Add user to request for easy access in controllers
        $request->merge(['authenticated_user' => $user]);

        return $next($request);
    }

    /**
     * Validate token is still valid
     *
     * @param  mixed  $user
     */
    protected function isTokenValid(Request $request, $user, string $guard): bool
    {
        // For Passport tokens
        if (method_exists($user, 'token')) {
            $token = $user->token();
            if ($token && isset($token->expires_at)) {
                return $token->expires_at->isFuture();
            }
        }
        // For Sanctum tokens
        if (method_exists($user, 'currentAccessToken')) {
            $token = $user->currentAccessToken();
            if ($token && isset($token->expires_at)) {
                return $token->expires_at->isFuture();
            }
        }

        // If no expiry mechanism, consider valid
        return true;
    }

    /**
     * Return authentication failed response
     *
     * Enhanced version of old: return response()->json(['message' => 'User not logged in'], 401);
     */
    protected function authenticationFailed(Request $request, string $message): Response
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'code' => 'AUTHENTICATION_FAILED',
            'timestamp' => now()->toISOString(),
            'path' => $request->getPathInfo(),
        ], 401);
    }

    /* Log failed authentication attempt */
    protected function logFailedAttempt(Request $request, string $reason, string $guard, ?int $userId = null): void
    {
        Log::warning('Token: API authentication failed', [
            'reason' => $reason,
            'guard' => $guard,
            'user_id' => $userId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $this->getSafeHeaders($request),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log successful API access
     *
     * @param  mixed  $user
     */
    protected function logSuccessfulAccess(Request $request, $user, string $guard): void
    {
        Log::info('Token: API authentication successful', [
            'user_id' => $user->id,
            'user_email' => $user->email ?? 'N/A',
            'guard' => $guard,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /* Get safe headers for logging (exclude sensitive data) */
    /**
     * @return array<string, array<int, string>|string>
     */
    protected function getSafeHeaders(Request $request): array
    {
        $headers = $request->headers->all();
        // Remove sensitive headers
        unset($headers['authorization']);
        unset($headers['cookie']);
        unset($headers['x-api-key']);
        unset($headers['x-auth-token']);

        return $headers;
    }
}
