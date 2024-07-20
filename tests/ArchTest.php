<?php

uses()->group('arch');

arch('moonshine')
    ->expect('MoonShine')
    ->toUseStrictTypes();

arch('globals')
    ->expect(['dd', 'dump'])
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

