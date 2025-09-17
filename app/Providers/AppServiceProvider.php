<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

/**
 * CODE STRUCTURE SUMMARY:
 * AppServiceProvider
 * - Registers application services
 * - Bootstraps application services
 */
class AppServiceProvider extends ServiceProvider
{
    /* Register any application services. */
    public function register(): void
    {
        // Optionally define DS for cross-platform directory separator usage
        if (! defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
    }

    /* Bootstrap any application services. */
    public function boot(): void
    {
        // Set default string length for older MySQL versions and utf8mb4 compatibility
        Schema::defaultStringLength(191);

        // Force HTTPS in production environment
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Alternative: Force HTTPS based on APP_FORCE_HTTPS environment variable
        // This gives you more control via .env file
        if (config('app.force_https', false)) {
            URL::forceScheme('https');
        }
    }
}
