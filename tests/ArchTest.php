<?php

uses()->group('arch');

arch('moonshine')
    ->expect('MoonShine')
    ->toUseStrictTypes();

arch('globals')
    ->expect(['dd', 'dump', 'dumpType', 'debugbar', 'trap', 'collect', 'str', 'rescue'])
    ->not->toBeUsed();

arch('app')
    ->expect(['app'])
    ->not->toBeUsed()->ignoring(['MoonShine\Laravel', 'App']);

arch('translates')
    ->expect(['__'])
    ->not->toBeUsed()
    ->ignoring(['MoonShine\Laravel', 'App']);

arch('contracts')
    ->expect('MoonShine\Contracts')
    ->toBeInterfaces()
    ->expect('MoonShine\Contracts')
    ->toHaveSuffix('Contract');

arch('laravel')
    ->expect('MoonShine\Laravel')
    ->toOnlyBeUsedIn('MoonShine\Laravel')
    ->ignoring('MoonShine\Database')
    ->ignoring('App')
;

arch('facades')
    ->expect('Illuminate\Support\Facades')
    ->not->toBeUsed()
    ->ignoring('MoonShine\Laravel')
;

$contextDependenciesMap = [
    'MoonShine\AssetManager' => [
        'MoonShine\Support',
        'MoonShine\Contracts',
        'Illuminate\Support',
    ],
    'MoonShine\ColorManager' => [
        'MoonShine\Contracts',
        'Illuminate\Support',
    ],
    'MoonShine\Core' => [
        'MoonShine\Contracts',
        'MoonShine\Support',
        'Illuminate\Contracts',
        'Illuminate\Support',
        'Psr\Container',
        'Psr\Http\Message',
        'Psr\SimpleCache',
        'Leeto\FastAttributes',
    ],
    'MoonShine\Crud' => [
        'MoonShine\Core',
        'MoonShine\AssetManager',
        'MoonShine\Contracts',
        'MoonShine\Support',
        'MoonShine\UI',
        'Illuminate\View',
        'Illuminate\Support',
        'Illuminate\Contracts',
        'Psr\SimpleCache',
        'Leeto\FastAttributes',
        'Symfony\Component\HttpFoundation',
    ],
    'MoonShine\MenuManager' => [
        'MoonShine\Contracts',
        'MoonShine\Support',
        'MoonShine\Core',
        'MoonShine\UI',
        'Illuminate\Support',
        'Leeto\FastAttributes',
    ],
    'MoonShine\UI' => [
        'MoonShine\Core',
        'MoonShine\Contracts',
        'MoonShine\Support',
        'Illuminate\View',
        'Illuminate\Contracts',
        'Illuminate\Support',
    ],
    'MoonShine\Support' => [
        'MoonShine\Contracts',
        'Illuminate\View',
        'Illuminate\Contracts',
        'Illuminate\Support',
    ],
    'MoonShine\Contracts' => [
        'MoonShine\Support',
        'Illuminate\Contracts',
        'Illuminate\Support',
        'Illuminate\View',
        'Psr\Container',
        'Psr\Http\Message',
        'Psr\SimpleCache',
    ],
];

foreach ($contextDependenciesMap as $class => $contexts) {
    arch("Context $class only be used in " . implode(', ', [...$contexts, $class]))
        ->expect($class)
        ->toOnlyUse($contexts)
        ->ignoringGlobalFunctions()
        ->ignoring('MoonShine\Laravel')
        ->ignoring('MoonShine\Database')
        ->ignoring('App')
        ->ignoring('Composer')
    ;
}
