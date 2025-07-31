<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /* Register any application services. */
    public function register(): void
    {
        // Optionally define DS for cross-platform directory separator usage
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
    }


    /* Bootstrap any application services. */
    public function boot(): void
    {
        // Set default string length for older MySQL versions and utf8mb4 compatibility
        Schema::defaultStringLength(191);
    }
}
