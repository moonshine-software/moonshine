<?php

use MoonShine\Menu\MenuSection;
use MoonShine\MoonShine;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('core');

beforeEach(function () {
    $this->resource = TestResourceBuilder::new();
});

it('find resource by uri key', function () {
    MoonShine::resources([
        $this->resource,
    ]);

    expect(MoonShine::getResourceFromUriKey($this->resource->uriKey()))
        ->toBe($this->resource);
});

it('menu', function () {
    MoonShine::menu([
        $this->resource,
    ]);

    expect(MoonShine::getMenu())
        ->toBeCollection()
        ->toHaveCount(1)
        ->first()
        ->toBeInstanceOf(MenuSection::class);
});
