<?php

declare(strict_types=1);

use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

uses()->group('fields');
uses()->group('json-field');

beforeEach(function (): void {
    $this->item = createItem(countComments: 0);

    expect($this->item->data)
        ->toBeEmpty();
});

function updateJsonValue(TestResource $resource, Item $item, array|string $data): void
{
    asAdmin()->put(
        $resource->getRoute('crud.update', $item->getKey()),
        ['data' => $data],
    )->assertRedirect();

    $item->refresh();
}

it('applies json rows from array input', function (): void {
    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Text::make('Title'),
            Text::make('Value'),
        ]),
    );

    updateJsonValue($resource, $this->item, [
        ['title' => 'Title 1', 'value' => 'Value 1'],
        ['title' => 'Title 2', 'value' => 'Value 2'],
    ]);

    expect($this->item->data->toArray())
        ->toBe([
            ['title' => 'Title 1', 'value' => 'Value 1'],
            ['title' => 'Title 2', 'value' => 'Value 2'],
        ]);
});

it('applies json rows from encoded payload', function (): void {
    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Select::make('Key')
                ->options(['vk' => 'VK', 'email' => 'E-mail']),
            Text::make('Value'),
        ]),
    );

    updateJsonValue($resource, $this->item, json_encode([
        ['key' => 'vk', 'value' => '111'],
        ['key' => 'email', 'value' => '222'],
    ], JSON_THROW_ON_ERROR));

    expect($this->item->data->toArray())
        ->toBe([
            ['key' => 'vk', 'value' => '111'],
            ['key' => 'email', 'value' => '222'],
        ]);
});

it('uses default json rows when request value is missing', function (): void {
    $default = [
        ['title' => 'Title 1', 'value' => 'Value 1'],
    ];

    $resource = addFieldsToTestResource(
        Json::make('Data')
            ->fields([
                Text::make('Title'),
                Text::make('Value'),
            ])
            ->default($default),
    );

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->data->toArray())
        ->toBe($default);
});
