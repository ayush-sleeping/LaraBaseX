<?php

// --------------------------------------------------------------------------
// Spatie Permission Configuration - Best Practices
// --------------------------------------------------------------------------
//
// - Use env variables for model/table overrides if needed.
// - Enable teams or wildcard permissions only if your app requires them.
// - Set cache expiration and store for performance.
// - Review exception display settings for security.
// - For advanced use, see: https://spatie.be/docs/laravel-permission

return [
    'models' => [
        // Eloquent models for permissions and roles
        'permission' => env('PERMISSION_MODEL', Spatie\Permission\Models\Permission::class),
        'role' => env('ROLE_MODEL', Spatie\Permission\Models\Role::class),
    ],

    'table_names' => [
        'roles' => env('ROLES_TABLE', 'roles'),
        'permissions' => env('PERMISSIONS_TABLE', 'permissions'),
        'model_has_permissions' => env('MODEL_HAS_PERMISSIONS_TABLE', 'model_has_permissions'),
        'model_has_roles' => env('MODEL_HAS_ROLES_TABLE', 'model_has_roles'),
        'role_has_permissions' => env('ROLE_HAS_PERMISSIONS_TABLE', 'role_has_permissions'),
    ],

    'column_names' => [
        'role_pivot_key' => env('ROLE_PIVOT_KEY', null),
        'permission_pivot_key' => env('PERMISSION_PIVOT_KEY', null),
        'model_morph_key' => env('MODEL_MORPH_KEY', 'model_id'),
        'team_foreign_key' => env('TEAM_FOREIGN_KEY', 'team_id'),
    ],

    'register_permission_check_method' => env('REGISTER_PERMISSION_CHECK_METHOD', true),
    'teams' => env('PERMISSION_TEAMS', false),
    'display_permission_in_exception' => env('DISPLAY_PERMISSION_IN_EXCEPTION', false),
    'display_role_in_exception' => env('DISPLAY_ROLE_IN_EXCEPTION', false),
    'enable_wildcard_permission' => env('ENABLE_WILDCARD_PERMISSION', false),

    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString(env('PERMISSION_CACHE_EXPIRATION', '24 hours')),
        'key' => env('PERMISSION_CACHE_KEY', 'spatie.permission.cache'),
        'store' => env('PERMISSION_CACHE_STORE', 'default'),
    ],
];
