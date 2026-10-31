<?php

declare(strict_types=1);

use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\MenuManager\MenuAutoloaderContract;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\MenuManager\MenuGroup;

uses()->group('menu-autoloader');

it('resolve', function () {
    $autoloader = app(MenuAutoloaderContract::class);
    $items = $autoloader->resolve();
    $group = $items[0];
    $filler = $group->getItems()[0]->getFiller();

    expect($items)
        ->toContainOnlyInstancesOf(MenuElementContract::class)
        ->and($group)
        ->toBeInstanceOf(MenuGroup::class)
        ->and($filler)
        ->toBeInstanceOf(MoonShineUserResource::class)
    ;
});

it('resolve cached', function () {
    $autoloader = app(MenuAutoloaderContract::class);
    $cached = $autoloader->toArray();
    $items = $autoloader->resolve($cached);
    $group = $items[0];
    $filler = $group->getItems()[0]->getFiller();

    expect($items)
        ->toContainOnlyInstancesOf(MenuElementContract::class)
        ->and($group)
        ->toBeInstanceOf(MenuGroup::class)
        ->and($filler)
        ->toBeInstanceOf(MoonShineUserResource::class)
    ;
});

it('to array', function () {
    $autoloader = app(MenuAutoloaderContract::class);
    $items = $autoloader->toArray();
    $snapshot = [
        'position' => 1,
        'group' => [
            'class' => 'MoonShine\Laravel\Resources\MoonShineUserRoleResource',
            'label' => 'moonshine::ui.resource.system',
            'icon' => 'users',
            'canSee' => 'canSee',
            'translatable' => true,
        ],
        'items' => [
            0 => [
                'filler' => 'MoonShine\Laravel\Resources\MoonShineUserResource',
                'canSee' => 'canSee',
                'position' => 0,
            ],
            1 => [
                'filler' => 'MoonShine\Laravel\Resources\MoonShineUserRoleResource',
                'canSee' => 'canSee',
                'position' => 1,
            ],
        ],
    ];

    $group = $items[0];

    expect($group)
        ->toMatchArray($snapshot)
    ;
});

it('uses registered resource instances for menu items', function () {
    $resources = $this->moonshineCore->getResources();
    $builtResources = 0;

    app()->resolving(static function (mixed $object) use (&$builtResources): void {
        if ($object instanceof ResourceContract) {
            $builtResources++;
        }
    });

    $filler = app(MenuAutoloaderContract::class)->resolve()[0]->getItems()[0]->getFiller();

    expect($builtResources)
        ->toBe(0)
        ->and($filler)
        ->toBe($resources->findByUri(app(MoonShineUserResource::class)->getUriKey()));
});
