<?php

declare(strict_types=1);

use MoonShine\MenuManager\MenuItem;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('core');

beforeEach(function (): void {
    $this->resource = TestResourceBuilder::new();
});

it('find resource by uri key', function (): void {
    expect(moonshine()->getResources()->findByUri($this->resource->getUriKey()))
        ->toBe($this->resource);
});

it('menu', function (): void {
    moonshineMenu()->add([
        MenuItem::make('Resource', $this->resource),
    ]);

    expect(moonshineMenu()->all())->toBeCollection()
        ->and($last = moonshineMenu()->all()->last())
        ->toBeInstanceOf(MenuItem::class)
        ->and($last->getLabel())
        ->toBe('Resource')
    ;
});
