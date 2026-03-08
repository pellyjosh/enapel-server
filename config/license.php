<?php

return [
    /*
    |--------------------------------------------------------------------------
    | License Key
    |--------------------------------------------------------------------------
    | Set this to the license key issued by enapel-cloud after purchase.
    | The key is validated remotely every time the application boots.
    */
    'key' => env('LICENSE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | enapel-cloud API URL
    |--------------------------------------------------------------------------
    | The base URL of the enapel-cloud service that issues and validates licenses.
    */
    'cloud_url' => env('ENAPEL_CLOUD_URL', 'https://cloud.enapel.com'),

    /*
    |--------------------------------------------------------------------------
    | Terminal Identifier
    |--------------------------------------------------------------------------
    | A UUID generated once on first install and stored in .env.
    | This uniquely identifies this terminal to the cloud.
    | Generate with: php artisan license:init-terminal
    */
    'terminal_id' => env('TERMINAL_IDENTIFIER'),

    /*
    |--------------------------------------------------------------------------
    | Terminal Name
    |--------------------------------------------------------------------------
    | A human-readable label for this terminal, e.g. "Main Counter", "Warehouse".
    */
    'terminal_name' => env('TERMINAL_NAME', 'Main Terminal'),

    /*
    |--------------------------------------------------------------------------
    | Grace Period (hours)
    |--------------------------------------------------------------------------
    | How long (in hours) to use a cached validation result before re-checking
    | the cloud. This allows the terminal to work offline temporarily.
    */
    'grace_hours' => env('LICENSE_GRACE_HOURS', 24),
];
