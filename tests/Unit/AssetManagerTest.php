<?php

declare(strict_types=1);

use MoonShine\AssetManager\AssetElements;
use MoonShine\AssetManager\AssetManager;
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

uses()->group('asset-manager');

beforeEach(function () {
    $this->assetManager = $this->moonshineCore->getContainer(AssetManager::class);
});

it('empty asset elements', function (): void {
    expect($this->assetManager->getAssets())
        ->toBeInstanceOf(AssetElements::class)
        ->toBeEmpty();
});

it('add asset', function (): void {
    $this->assetManager->add(
        Css::make('app.css')
    );

    expect($this->assetManager->getAssets())
        ->toHaveCount(1)
        ->each->toBeInstanceOf(Css::class);
});

it('add unique asset', function (): void {
    $this->assetManager->add(
        Css::make('app.css')
    );

    $this->assetManager->add([
        Css::make('app.css'),
        Css::make('app.css'),
        Css::make('app.css'),
    ]);

    expect($this->assetManager->getAssets())
        ->toHaveCount(1)
        ->each->toBeInstanceOf(Css::class);
});

it('asset link', function (): void {
    $asset = Js::make('app.js');

    expect($asset->getLink())
        ->toBe('app.js')
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

it('add asset with version', function (): void {
    $asset = Css::make('app.css')->version('1.0');

    expect($asset->getLink())
        ->toContain('?v=1.0')
    ;
});
