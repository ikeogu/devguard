<?php 

return [
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
    'dashboard' => [
        'path' => env('DMG_DASHBOARD_PATH', 'dev-monitor'),
        'middleware' => ['web', 'auth'], // Customize as needed
    ]
];