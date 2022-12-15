<?php

use Leeto\MoonShine\Models\MoonshineUser;

return [
    'dir' => 'app/MoonShine',
    'namespace' => 'App\MoonShine',

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
                'driver' => 'session',
                'provider' => 'moonshine',
            ],
        ],
        'providers' => [
            'moonshine' => [
                'driver' => 'eloquent',
                'model' => MoonshineUser::class,
            ],
        ],
    ],
    'middlewares' => [],
    'tinymce' => [
        'token' => env('MOONSHINE_TINYMCE_TOKEN', ''),
        'version' => env('MOONSHINE_TINYMCE_VERSION', '6')
    ],
    'extensions' => [
        //
    ],
];
