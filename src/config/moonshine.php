<?php

return [
    'dir' => 'app/MoonShine',
    'title' => env('MOONSHINE_TITLE', 'MoonShine'),
	'logo' => env('MOONSHINE_LOGO', ''),

    'auth' => [
        'controller' => Leeto\MoonShine\Controllers\IndexController::class,
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
                'model'  => \Leeto\MoonShine\Models\MoonshineUser::class,
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
