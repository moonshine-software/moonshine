<?php

use Leeto\MoonShine\Controllers\MoonShineDashboardController;
use Leeto\MoonShine\Models\MoonshineUser;

return [
    'dir' => 'app/MoonShine',
    'namespace' => 'App\\MoonShine',

    'title' => env('MOONSHINE_TITLE', 'MoonShine'),
	'logo' => env('MOONSHINE_LOGO', ''),

    'route' => [
        'prefix' => env('MOONSHINE_ROUTE_PREFIX', 'moonshine'),
        'middleware' => ['web', 'moonshine'],
    ],

    'auth' => [
        'controller' => MoonShineDashboardController::class,
        'guard' => 'moonshine',
        'guards' => [
            'moonshine' => [
                'driver'   => 'session',
                'provider' => 'moonshine',
            ],
        ],
        'providers' => [
            'moonshine' => [
                'driver' => 'eloquent',
                'model'  => MoonshineUser::class,
            ],
        ],
    ],
    //
    'extensions' => [
        //
    ],
];
