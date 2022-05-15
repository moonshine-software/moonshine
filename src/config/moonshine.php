<?php

use Leeto\MoonShine\Controllers\MoonShineDashboardController;
use Leeto\MoonShine\Models\MoonshineUser;

return [
    'dir' => 'app/MoonShine',
    'title' => env('MOONSHINE_TITLE', 'MoonShine'),
	'logo' => env('MOONSHINE_LOGO', ''),

    'auth' => [
        'controller' => MoonShineDashboardController::class,
        'guard' => 'moonshine',
        'guards' => [
            'moonshine' => [
                'driver'   => 'session',
                'provider' => 'moonshine',
            ],
        ],
        'remember' => true,
        'redirect_to' => 'moonshine/login',
        'providers' => [
            'moonshine' => [
                'driver' => 'eloquent',
                'model'  => MoonshineUser::class,
            ],
        ],
    ],
    'route' => [
        'prefix' => env('MOONSHINE_ROUTE_PREFIX', 'moonshine'),
        'namespace' => 'App\\MoonShine\\Controllers',
        'middleware' => ['web', 'moonshine'],
    ],
    'extensions' => [
        //
    ],
];
