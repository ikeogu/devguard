<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Global Enable Switch
    |--------------------------------------------------------------------------
    */
    'enabled' => env('DMG_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Authentication Guard
    |--------------------------------------------------------------------------
    */
    'guard' => 'dev-user',

    'guards' => [
        'dev-user' => [
            'driver' => 'session',
            'provider' => 'dev-users',
        ],
    ],

    'providers' => [
        'dev-users' => [
            'driver' => 'eloquent',
            'model' => \Emmanuelikeogu\DevMonitoringGuard\Models\DevUser::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Third-Party Tool Toggles
    |--------------------------------------------------------------------------
    */
    'scramble' => [
        'enabled' => env('DMG_SCRAMBLE_ENABLED', true),
        'path' => env('DMG_SCRAMBLE_PATH', 'api/docs'),
    ],

    'log_viewer' => [
        'enabled' => env('DMG_LOG_VIEWER_ENABLED', true),
        'path' => env('DMG_LOG_VIEWER_PATH', 'logs'),
    ],

    'telescope' => [
        'enabled' => env('DMG_TELESCOPE_ENABLED', true),
        'path' => env('DMG_TELESCOPE_PATH', 'telescope'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    'dashboard' => [
        'path' => env('DMG_DASHBOARD_PATH', 'dev-monitor'),
        'middleware' => explode(',', env('DMG_DASHBOARD_MIDDLEWARE', 'web,auth')),
    ],
];
