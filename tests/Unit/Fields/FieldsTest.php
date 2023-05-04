<?php

use MoonShine\Decorations\Block;
use MoonShine\Exceptions\FieldsException;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

// Uses

uses()->group('fields');

// Performs

function exampleFields(): Fields
{
    return Fields::make([
        Text::make('Field 1'),
        Text::make('Field 2'),
    ]);
}

// Expectations

it('transformed to columns', function () {
    expect(TestResourceBuilder::buildWithFields()->getFields()->onlyColumns())
        ->toBeIterable()
        ->toContain('id', 'name')
        ->toBeInstanceOf(Fields::class)
        ->each->toBeScalar();
});

it('wrapped into decoration', function () {
    expect(exampleFields()->wrapIntoDecoration(Block::class, 'Label'))
        ->first()
        ->toBeInstanceOf(Block::class);
});

it('can`t be wrapped in a not decoration class', function () {
    exampleFields()->wrapIntoDecoration(Fields::class, 'Label');
})->throws(FieldsException::class, FieldsException::wrapError()->getMessage());
