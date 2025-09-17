<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/**
 * CODE STRUCTURE SUMMARY:
 * RouteServiceProvider
 * - Registers route services
 * - Bootstraps route services
 * Define your route model bindings, pattern filters, etc.
 * Configure the rate limiters for the application.
 */
class RouteServiceProvider extends ServiceProvider
{
    /*
     * The path to the dashboard route for your application.
     * This is used by Laravel authentication to redirect users after login.
     */
    public const HOME = '/dashboard';

    /* Define your route model bindings, pattern filters, etc. */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }

    /* Configure the rate limiters for the application. */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
