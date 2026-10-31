<?php

declare(strict_types=1);

use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\Laravel\Resources\MoonShineUserResource;
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
        MenuItem::make($this->resource, 'Resource'),
    ]);

    expect($this->menuManager->all())->toBeCollection()
        ->and($last = $this->menuManager->all()->last())
        ->toBeInstanceOf(MenuItem::class)
        ->and($last->getLabel())
        ->toBe('Resource')
    ;
});

it('flushes resolved resources without building them again', function (): void {
    $this->moonshineCore->resources([MoonShineUserResource::class]);
    $this->moonshineCore->getResources();
    $builtResources = 0;

    app()->resolving(static function (mixed $object) use (&$builtResources): void {
        if ($object instanceof ResourceContract) {
            $builtResources++;
        }
    });

    $this->moonshineCore->flushState();

    expect($builtResources)->toBe(0);
});
