<?php

use MoonShine\Menu\MenuElement;
use MoonShine\MoonShine;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('core');

beforeEach(function (): void {
    $this->resource = TestResourceBuilder::new();
});

it('find resource by uri key', function (): void {
    MoonShine::resources([
        $this->resource,
    ]);

    expect(MoonShine::getResourceFromUriKey($this->resource->uriKey()))
        ->toBe($this->resource);
});

it('menu', function (): void {
    MoonShine::menu([
        $this->resource,
    ]);

    expect(MoonShine::getMenu())
        ->toBeCollection()
        ->toHaveCount(1)
        ->first()
        ->toBeInstanceOf(MenuElement::class);
});
