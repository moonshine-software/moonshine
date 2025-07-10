<?php

uses()->group('arch');

arch('moonshine')
    ->expect('MoonShine')
    ->toUseStrictTypes();

arch('globals')
    ->expect(['dd', 'dump', 'debugbar', 'trap', 'collect', 'str', 'rescue'])
    ->not->toBeUsed();

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
        'Leeto\FastAttributes',
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
