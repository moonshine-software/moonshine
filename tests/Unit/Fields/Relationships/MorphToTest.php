<?php

declare(strict_types=1);

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Fields\Relationships\MorphTo;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\ImageModel;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestImageResource;

uses()->group('model-relation-fields');

beforeEach(function (): void {
    $this->item = Item::factory()->createOne();
    $this->image = ImageModel::create([
        'imageable_id' => $this->item->getKey(),
        'imageable_type' => Item::class,
    ]);

    $this->field = MorphTo::make('Imageable', resource: new TestImageResource())
        ->resolveFill($this->item->toArray(), $this->item)
        ->types([
            Item::class => 'name',
            Category::class => 'name',
        ]);
});

describe('common field methods', function () {
    it('ModelRelationField is parent', function (): void {
        expect($this->field)
            ->toBeInstanceOf(ModelRelationField::class);
    });

    it('type', function (): void {
        expect($this->field->type())
            ->toBeEmpty();
    });

    it('correct interfaces', function (): void {
        expect($this->field)
            ->toBeInstanceOf(HasAsyncSearch::class)
            ->toBeInstanceOf(HasRelatedValues::class)
            ->toBeInstanceOf(HasDefaultValue::class)
            ->toBeInstanceOf(DefaultCanBeString::class)
            ->toBeInstanceOf(DefaultCanBeNumeric::class);
    });
});

describe('unique field methods', function () {
    it('async search', function (): void {
        expect($this->field->asyncSearch('name'))
            ->isAsyncSearch()
            ->toBeTrue()
            ->asyncSearchColumn()
            ->toBe('name');
    });

    it('types', function (): void {
        expect($this->field)
            ->getSearchColumn(Item::class)
            ->toBe('name')
            ->getTypes()
            ->toBe([
                Item::class => 'Item',
                Category::class => 'Category',
            ])
            ->getMorphType()
            ->toBe('imageable_type')
            ->getMorphKey()
            ->toBe('imageable_id');
    });
});

describe('basic methods', function () {
    it('change preview', function () {
        expect($this->field->changePreview(static fn () => 'changed'))
            ->preview()
            ->toBe('changed');
    });

    it('formatted value', function () {
        $field = MorphTo::make('Imageable', formatted: static fn () => ['changed'], resource: new TestImageResource())
            ->resolveFill($this->item->toArray(), $this->item);

        expect($field->toFormattedValue())
            ->toBe(['changed']);
    });

    it('applies', function () {
        expect()
            ->applies($this->field);
    });
});
