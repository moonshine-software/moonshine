<?php

declare(strict_types=1);

use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\MenuManager\MenuItem;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('core');

beforeEach(function (): void {
    $this->resource = TestResourceBuilder::new();
    $this->menuManager = $this->moonshineCore->getContainer(MenuManagerContract::class);
});

it('find resource by uri key', function (): void {
    expect($this->moonshineCore->getResources()->findByUri($this->resource->getUriKey()))
        ->toBe($this->resource);
});

it('menu', function (): void {
    $this->menuManager->add([
        MenuItem::make('Resource', $this->resource),
    ]);

    expect($this->menuManager->all())->toBeCollection()
        ->and($last = $this->menuManager->all()->last())
        ->toBeInstanceOf(MenuItem::class)
        ->and($last->getLabel())
        ->toBe('Resource')
    ;
});
