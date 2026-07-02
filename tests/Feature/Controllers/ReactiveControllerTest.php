<?php

declare(strict_types=1);

use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Text;

uses()->group('reactive-controller');

it('get response', function () {
    asAdmin()->post($this->moonshineCore->getRouter()->to('reactive', [
        '_component_name' => 'test-item-resource',
        'resourceUri' => 'test-item-resource',
        'pageUri' => 'form-page',
        'values' => [
            'name' => 'new name',
        ],
    ]))
        ->assertJson(['values' => [
            'name' => 'new name',
        ]])
        ->assertOk()
    ;
});

it('forbids response when update policy denies it', function () {
    $item = createItem();

    $resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([
            Text::make('Name')->reactive(),
        ])
        ->setTestPolicy(true);

    MoonshineUser::query()->whereKey(1)->update([
        'name' => 'Policies test',
    ]);

    asAdmin()->post($this->moonshineCore->getRouter()->to('reactive', [
        '_component_name' => $resource->getUriKey(),
        'resourceUri' => $resource->getUriKey(),
        'pageUri' => 'form-page',
        'resourceItem' => $item->id,
        'values' => [
            'name' => 'new name',
        ],
    ]))
        ->assertForbidden();
});
