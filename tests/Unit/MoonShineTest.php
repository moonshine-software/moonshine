<?php

declare(strict_types=1);

use MoonShine\Menu\MenuItem;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('core');

beforeEach(function (): void {
    $this->resource = TestResourceBuilder::new();
});

it('find resource by uri key', function (): void {
    expect(moonshine()->getResourceFromUriKey($this->resource->uriKey()))
        ->toBe($this->resource);
});

it('menu', function (): void {
    moonshine()->init([
        MenuItem::make('Resource', $this->resource),
    ]);

    expect(moonshine()->getMenu())->toBeCollection()
        ->and($last = moonshine()->getMenu()->last())
        ->toBeInstanceOf(MenuItem::class)
        ->and($last->label())
        ->toBe('Resource')
    ;
});
