<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Allow all methods or override via env (comma-separated)
    'allowed_methods' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_METHODS', '*')))),

    // Allow multiple origins via env (comma-separated), fallback to *
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', '*')))),

    'allowed_origins_patterns' => [],

    // Allow all headers or override via env (comma-separated)
    'allowed_headers' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_HEADERS', '*')))),

    'exposed_headers' => [],

    'max_age' => 0,

    // Set to true if using cookies/auth with Inertia/SPA
    'supports_credentials' => env('CORS_SUPPORTS_CREDENTIALS', false),

];
