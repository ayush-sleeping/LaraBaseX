<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

/**
 * CODE STRUCTURE SUMMARY:
 * BroadcastServiceProvider
 * - Registers broadcasting services
 * - Bootstraps broadcasting services
 */
class BroadcastServiceProvider extends ServiceProvider
{
    /*
     * Register any broadcasting services.
     * @return void
     */
    public function register(): void
    {
        //
    }

    /*
     * Bootstrap any broadcasting services.
     * @return void
     */
    public function boot(): void
    {
        Broadcast::routes();
        require base_path('routes/channels.php');
    }
}
