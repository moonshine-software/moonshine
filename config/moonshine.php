<?php

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use MoonShine\Exceptions\MoonShineNotFoundException;
use MoonShine\Forms\FiltersForm;
use MoonShine\Forms\LoginForm;
use MoonShine\Http\Middleware\Authenticate;
use MoonShine\Http\Middleware\ChangeLocale;
use MoonShine\Layouts\AppLayout;
use MoonShine\Models\MoonshineUser;
use MoonShine\Pages\Dashboard;
use MoonShine\Pages\ErrorPage;
use MoonShine\Pages\LoginPage;
use MoonShine\Pages\ProfilePage;

return [
    'title' => env('MOONSHINE_TITLE', 'MoonShine'),
    'domain' => env('MOONSHINE_DOMAIN'),

    'prefix' => 'admin',
    'page_prefix' => 'page',

    'middlewares' => [
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        AuthenticateSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
        SubstituteBindings::class,
        ChangeLocale::class,
    ],

    'home_route' => 'moonshine.index',

    'not_found_exception' => MoonShineNotFoundException::class,

    'use_migrations' => true,
    'use_notifications' => true,

    'disk' => 'public',
    'disk_options' => [],

    'cache' => 'file',

    'layout' => AppLayout::class,

    'auth' => [
        'enable' => true,
        'guard' => 'moonshine',
        'model' => MoonshineUser::class,
        'middleware' => Authenticate::class,
        'pipelines' => [],
    ],

    'user_fields' => [
        'username' => 'email',
        'password' => 'password',
        'name' => 'name',
        'avatar' => 'avatar',
    ],

    'forms' => [
        'login' => LoginForm::class,
        'filters' => FiltersForm::class,
    ],

    'pages' => [
        'dashboard' => Dashboard::class,
        'profile' => ProfilePage::class,
        'login' => LoginPage::class,
        'error' => ErrorPage::class,
    ],

    'default_with_import' => true,
    'default_with_export' => true,

    'global_search' => [
        // User::class
    ],

    'locales' => [
        // en
    ],

    'socialite' => [
        // 'driver' => 'path_to_image_for_button'
    ],
];
