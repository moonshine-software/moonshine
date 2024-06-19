<?php

declare(strict_types=1);

use MoonShine\AssetManager\AssetElements;
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

uses()->group('asset-manager');

it('empty asset elements', static function (): void {
    expect(moonshineAssets()->getAssets())
        ->toBeInstanceOf(AssetElements::class)
        ->toBeEmpty();
});

it('add asset', static function (): void {
    moonshineAssets()->add(
        Css::make('app.css')
    );

    expect(moonshineAssets()->getAssets())
        ->toHaveCount(1)
        ->each->toBeInstanceOf(Css::class);
});

it('add unique asset', static function (): void {
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

it('asset link', static function (): void {
    $asset = Js::make('app.js');

    expect($asset->getLink())
        ->toBe(moonshineAssets()->getAsset('app.js'))
    ;
});

it('add asset with attributes', static function (): void {
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
