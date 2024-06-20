<?php

declare(strict_types=1);

use MoonShine\MenuManager\MenuElements;
use MoonShine\MenuManager\MenuItem;
use Pest\Expectation;

uses()->group('menu-manager');

it('empty menu elements', function (): void {
    expect(moonshineMenu()->all())
        ->toBeInstanceOf(MenuElements::class)
        ->toBeEmpty();
});

it('add menu elements', function (): void {
    moonshineMenu()->add(MenuItem::make('Item', '/'));

    expect(moonshineMenu()->all())
        ->toHaveCount(1)
        ->each(
            static fn (Expectation $item) => $item
            ->getUrl()
            ->toBe('/')
            ->getLabel()
            ->toBe('Item')
        );
});

it('add before menu elements', function (): void {
    moonshineMenu()->add([
        MenuItem::make('Item 1', '/item1'),
        MenuItem::make('Item 2', '/item2'),
        MenuItem::make('Item 3', '/item3'),
    ]);

    $item = MenuItem::make('Item 2_0', '/item2_0');

    moonshineMenu()->addBefore(static fn (MenuItem $el) => $el->getUrl() === '/item2', $item);

    $items = moonshineMenu()->all();

    expect($items[1])
        ->toBe($item);
});

it('add after menu elements', function (): void {
    moonshineMenu()->add([
        MenuItem::make('Item 1', '/item1'),
        MenuItem::make('Item 2', '/item2'),
        MenuItem::make('Item 3', '/item3'),
    ]);

    $item = MenuItem::make('Item 2_0', '/item2_0');

    moonshineMenu()->addAfter(static fn (MenuItem $el) => $el->getUrl() === '/item2', [$item]);

    $items = moonshineMenu()->all();

    expect($items[2])
        ->toBe($item);
});

it('remove menu elements', function (): void {
    moonshineMenu()->add([
        MenuItem::make('Item 1', '/item1'),
        MenuItem::make('Item 2', '/item2'),
        MenuItem::make('Item 3', '/item3'),
    ]);

    moonshineMenu()->remove(static fn (MenuItem $el) => $el->getUrl() === '/item2');

    $items = moonshineMenu()->all();

    expect($items)
        ->toHaveCount(2);
});


it('replace items', function (): void {
    moonshineMenu()->add([
        MenuItem::make('Item 1', '/item1'),
        MenuItem::make('Item 2', '/item2'),
        MenuItem::make('Item 3', '/item3'),
    ]);

    expect(moonshineMenu()->all())
        ->toHaveCount(3)
        ->and(moonshineMenu()->all([MenuItem::make('Item 1', '/item1')]))
        ->toHaveCount(1);
});

it('only visible items', function (): void {
    moonshineMenu()->add([
        MenuItem::make('Item 1', '/item1'),
        MenuItem::make('Item 2', '/item2')->canSee(static fn () => false),
        MenuItem::make('Item 3', '/item3'),
    ]);

    expect(moonshineMenu()->all())
        ->toHaveCount(2);
});


it('check active element', function (): void {
    fakeRequest('/item2');

    $item1 = MenuItem::make('Item 1', '/item1');
    $item2 = MenuItem::make('Item 2', '/item2');


    expect($item1->isActive())
        ->toBeFalse()
        ->and($item2->isActive())
        ->toBeTrue();

    $item1 = MenuItem::make('Item 1', 'http://localhost/item1');
    $item2 = MenuItem::make('Item 2', 'http://localhost/item2');


    expect($item1->isActive())
        ->toBeFalse()
        ->and($item2->isActive())
        ->toBeTrue();


    $item1 = MenuItem::make('Item 1', 'http://localhost/item1')->whenActive(static fn () => true);
    $item2 = MenuItem::make('Item 2', 'http://localhost/item2');


    expect($item1->isActive())
        ->toBeTrue()
        ->and($item2->isActive())
        ->toBeTrue();
});
