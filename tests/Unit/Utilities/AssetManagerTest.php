<?php

use MoonShine\Fields\Code;
use MoonShine\Utilities\AssetManager;

uses()->group('assets');

beforeEach(function (): void {
    $this->manager = app(AssetManager::class);
});

it('empty', function (): void {
    expect($this->manager)
        ->css()
        ->toBeEmpty()
        ->js()
        ->toBeEmpty();
});

it('add new assets', function (): void {
    $this->manager->add([
        'script1.js',
        'script2.js',
        'style1.css',
        'style2.css',
        'trash',
    ]);

    expect($this->manager)
        ->getAssets()
        ->toHaveCount(5)
        ->js()
        ->not->toContain('trash')
        ->not->toContain(asset('style1.css'), asset('style2.css'))
        ->toContain(asset('script1.js'), asset('script2.js'))
        ->css()
        ->not->toContain('trash')
        ->not->toContain(asset('script1.js'), asset('script2.js'))
        ->toContain(asset('style1.css'), asset('style2.css'));
});

it('field assets', function (): void {
    $field = Code::make('Test');

    expect($this->manager->getAssets())
        ->toBe($field->getAssets());
});
