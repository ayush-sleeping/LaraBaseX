<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * CODE STRUCTURE SUMMARY:
 * AuthServiceProvider
 * - Registers authentication / authorization services
 * - Bootstraps authentication / authorization services
 */
class AuthServiceProvider extends ServiceProvider
{
    /*
     * The model to policy mappings for the application.
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\\Models\\Model' => 'App\\Policies\\ModelPolicy',
    ];

    /*
     * Register any authentication / authorization services.
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();
        // You may define additional Gates or auth logic here
    }
}
