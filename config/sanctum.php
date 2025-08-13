<?php

use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\VerifyCsrfToken;
use Laravel\Sanctum\Sanctum;

// --------------------------------------------------------------------------
// Sanctum Configuration - Best Practices
// --------------------------------------------------------------------------
// - Add all SPA and API domains to SANCTUM_STATEFUL_DOMAINS in .env
// - Use 'expiration' for token expiry if needed (null = never expires)
// - Adjust guards if you use custom auth guards
// - Customize middleware if you override CSRF or cookie encryption
// - For more: https://laravel.com/docs/sanctum

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        Sanctum::currentApplicationUrlWithPort()
    ))),

    'guard' => explode(',', env('SANCTUM_GUARDS', 'web')),
    'expiration' => env('SANCTUM_EXPIRATION', null),
    'middleware' => [
        'verify_csrf_token' => env('SANCTUM_VERIFY_CSRF_MIDDLEWARE', VerifyCsrfToken::class),
        'encrypt_cookies' => env('SANCTUM_ENCRYPT_COOKIES_MIDDLEWARE', EncryptCookies::class),
    ],
];
