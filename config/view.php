
<?php

// --------------------------------------------------------------------------
// View Configuration - Best Practices for Laravel + Inertia + React + Vite
// --------------------------------------------------------------------------
// - Blade is still used for emails, error pages, and hybrid rendering.
// - You can add custom view paths for SSR or special layouts.
// - Compiled path can be set per environment via VIEW_COMPILED_PATH.
//
// Example: To add a custom SSR view path, add to the 'paths' array below.
//
//   resource_path('ssr-views'),
//
// For more: https://laravel.com/docs/views

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
        // resource_path('ssr-views'), // Uncomment if you add SSR Blade views
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
