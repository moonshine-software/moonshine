<?php

declare(strict_types=1);

use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

uses()->group('crud-controller');

it('get component', function () {
    $item = createItem(3, 2);

    asAdmin()->get($this->moonshineCore->getRouter()->to('component', [
        '_component_name' => 'index-table-test-item-resource',
        'resourceUri' => 'test-item-resource',
        'pageUri' => 'index-page',
    ]))
        ->assertSee('Name title')
        ->assertSee('Category title')
        ->assertDontSee('Content title')
        ->assertDontSee('Public at title')
        ->assertSee('Comments title')
        ->assertSee('Images title')
        ->assertSee($item->id)
        ->assertSee($item->name)
        ->assertOk()
    ;
});

it('forbids index component when view any policy denies it', function () {
    $resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([
            ID::make(),
            Text::make('Name'),
        ])
        ->setTestPolicy(true);

    MoonshineUser::query()->whereKey(1)->update([
        'name' => 'Policies test',
    ]);

    asAdmin()->get($this->moonshineCore->getRouter()->to('component', [
        '_component_name' => "index-table-{$resource->getUriKey()}",
        'resourceUri' => $resource->getUriKey(),
        'pageUri' => 'index-page',
    ]))
        ->assertForbidden();
});

it('forbids detail component when view policy denies it', function () {
    $item = createItem();

    $resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([
            ID::make(),
            Text::make('Name'),
        ])
        ->setTestPolicy(true);

    asAdmin()->get($this->moonshineCore->getRouter()->to('component', [
        '_component_name' => 'crud-detail',
        'resourceUri' => $resource->getUriKey(),
        'pageUri' => 'detail-page',
        'resourceItem' => $item->id,
    ]))
        ->assertForbidden();
});
