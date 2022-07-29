<?php

use Leeto\MoonShine\Models\MoonshineUser;

return [
    'title' => env('MOONSHINE_TITLE', 'MoonShine'),
	'logo' => env('MOONSHINE_LOGO', ''),

    'route' => [
        'prefix' => env('MOONSHINE_ROUTE_PREFIX', 'moonshine'),
        'middleware' => ['web', 'moonshine'],
    ],

    'auth' => [
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
    'extensions' => [
        //
    ],
];
