<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * CODE STRUCTURE SUMMARY:
 * Application HTTP kernel.
 * Register global middleware
 * Route middleware groups
 * Route middleware here.
 */
class Kernel extends HttpKernel
{
    /* Global HTTP middleware stack run during every request. */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class, // Uncomment if implemented
        \App\Http\Middleware\ForceHttps::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \App\Http\Middleware\TrimStrings::class,
    ];

    /* Route middleware groups. */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class, // Uncomment if needed
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\EncryptCookies::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Uncomment if using Sanctum
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        // Example custom group (uncomment if implemented)
        // 'admin' => [
        //     \App\Http\Middleware\Admin::class
        // ],
    ];

    /* Route middleware (assignable to groups or used individually). */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        // Custom middleware ::
        'admin' => \App\Http\Middleware\AdminAccess::class,
        'preventBackHistory' => \App\Http\Middleware\PreventBackHistory::class,
        'basic_auth' => \App\Http\Middleware\BasicAuth::class,
        'token' => \App\Http\Middleware\Token::class,
        'trustHosts' => \App\Http\Middleware\TrustHosts::class,
        'force.https' => \App\Http\Middleware\ForceHttps::class,
    ];
}
