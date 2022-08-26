<?php

return [
    'title' => env('MOONSHINE_TITLE', 'MoonShine'),
    'logo' => env('MOONSHINE_LOGO', ''),
    'prefix' => env('MOONSHINE_ROUTE_PREFIX', 'moonshine'),
    'frontend_url' => env('MOONSHINE_FRONTEND_URL', 'localhost'),
    'stateful' => explode(
        ',',
        env(
            'SANCTUM_STATEFUL_DOMAINS',
            sprintf(
                '%s%s%s',
                'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
                env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : '',
                env('MOONSHINE_FRONTEND_URL') ? ','.parse_url(env('MOONSHINE_FRONTEND_URL'), PHP_URL_HOST) : ''
            )
        )
    ),
];
