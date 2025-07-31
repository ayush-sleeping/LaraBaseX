<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | The default hashids connection to use for encoding and decoding.
    |
    */
    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Define as many connections as you need. Use strong, unique salts!
    | You can add per-model connections here if you want custom salts.
    |
    */
    'connections' => [

        'main' => [
            'salt' => env('HASHIDS_MAIN_SALT', 'change_this_to_a_random_string'),
            'length' => 8,
            // 'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        ],

        'alternative' => [
            'salt' => env('HASHIDS_ALT_SALT', 'another_unique_salt'),
            'length' => 8,
            // 'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        ],

        // Example for a model-specific connection:
        // 'App\\Models\\User' => [
        //     'salt' => env('HASHIDS_USER_SALT', 'user_model_salt'),
        //     'length' => 10,
        // ],
    ],

];
