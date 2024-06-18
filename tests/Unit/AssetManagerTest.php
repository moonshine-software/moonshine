<?php

declare(strict_types=1);

use MoonShine\AssetManager\AssetElements;
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

uses()->group('asset-manager');

it('empty asset elements', function (): void {
    expect(moonshineAssets()->getAssets())
        ->toBeInstanceOf(AssetElements::class)
        ->toBeEmpty();
});

it('add asset', function (): void {
    moonshineAssets()->add(
        Css::make('app.css')
    );

    expect(moonshineAssets()->getAssets())
        ->toHaveCount(1)
        ->each->toBeInstanceOf(Css::class);
});

it('add unique asset', function (): void {
    moonshineAssets()->add(
        Css::make('app.css')
    );

    moonshineAssets()->add([
        Css::make('app.css'),
        Css::make('app.css'),
        Css::make('app.css'),
    ]);

    expect(moonshineAssets()->getAssets())
        ->toHaveCount(1)
        ->each->toBeInstanceOf(Css::class);
});

it('asset link', function (): void {
    $asset = Js::make('app.js');

    expect($asset->getLink())
        ->toBe(moonshineAssets()->getAsset('app.js'))
    ;
});

it('add asset with attributes', function (): void {
    $asset = Css::make('app.css')
        ->defer()
        ->customAttributes([
            'data-var' => 'foo',
        ]);

    expect($asset->getAttribute('defer'))
        ->toBe('')
        ->and($asset->getAttribute('__defer'))
        ->toBeNull()
        ->and($asset->getAttribute('data-var'))
        ->toBe('foo')
    ;
});
