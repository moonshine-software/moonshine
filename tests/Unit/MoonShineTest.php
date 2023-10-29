<?php

declare(strict_types=1);

use MoonShine\Menu\MenuItem;
use MoonShine\MoonShine;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('core');

beforeEach(function (): void {
    $this->resource = TestResourceBuilder::new();
});

it('find resource by uri key', function (): void {
    expect(MoonShine::getResourceFromUriKey($this->resource->uriKey()))
        ->toBe($this->resource);
});

it('menu', function (): void {
    MoonShine::menu([
        MenuItem::make('Resource', $this->resource),
    ]);

    expect(MoonShine::getMenu())->toBeCollection()
        ->and($last = MoonShine::getMenu()->last())
        ->toBeInstanceOf(MenuItem::class)
        ->and($last->label())
        ->toBe('Resource')
    ;
});
