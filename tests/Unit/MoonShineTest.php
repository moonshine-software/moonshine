<?php

use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Menu\MenuSection;
use MoonShine\MoonShine;
use MoonShine\Resources\MoonShineUserRoleResource;
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
        ->toBeInstanceOf(MenuSection::class);
});

it('registers custom single menu item', function (): void {
    MoonShine::customItems([
        new MoonShineUserRoleResource(),
    ]);

    MoonShine::menu([
        $this->resource,
    ]);

    $menu = MoonShine::getMenu();
    expect($menu)
        ->toBeCollection()
        ->toHaveCount(2)
        ->and($menu->last()->resource())
        ->toBeInstanceOf(MoonShineUserRoleResource::class);
});

it('registers custom group menu item', function (): void {
    MoonShine::customItems([
        MenuGroup::make('Permission', [
            MenuItem::make('Roles', new MoonShineUserRoleResource()),
        ]),
    ]);

    MoonShine::menu([
        $this->resource,
    ]);

    $menuLast = MoonShine::getMenu()->last();
    expect($menuLast)
        ->toBeInstanceOf(MenuGroup::class)
        ->and($menuLast->items())
        ->toHaveCount(1)
        ->and($menuLast->items()->first()->resource())
        ->toBeInstanceOf(MoonShineUserRoleResource::class);
});
