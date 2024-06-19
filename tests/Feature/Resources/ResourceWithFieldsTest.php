<?php

declare(strict_types=1);

use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\StackFields;
use MoonShine\UI\Fields\Text;

uses()->group('resources-feature');

beforeEach(function (): void {
    $this->resource = TestResourceBuilder::new(Item::class)
        ->setTestImportFields([
            ID::make(),
        ])
        ->setTestExportFields([
            ID::make(),
        ])
        ->setTestIndexFields([
            ID::make()->sortable(),
            Text::make('Name title', 'name'),
            StackFields::make()->fields([
                Text::make('Index field'),
            ]),
        ])
        ->setTestDetailFields([
            ID::make(),
            Text::make('Name title', 'name'),
            Text::make('Detail field'),
            HasOne::make('Outside', 'outside', resource: new TestResource()),
        ])
        ->setTestFormFields([
            Box::make([
                ID::make(),
                Text::make('Name title', 'name'),
                Text::make('Form field'),
                HasOne::make('Outside', 'outside', resource: new TestResource()),
            ]),

        ])
        ->setTestFilters([
            Box::make([
                Text::make('Name title', 'name'),
            ]),
        ]);
});

it('index fields', function () {
    expect($this->resource->getIndexFields())
        ->toHaveCount(3);
});

it('form fields with outside', function () {
    expect($this->resource->getFormFields(withOutside: true))
        ->first()
        ->toBeInstanceOf(Box::class)
        ->onlyFields()
        ->toHaveCount(4);
});

it('form fields without outside', function () {
    expect($this->resource->getFormFields())
        ->first()
        ->toBeInstanceOf(Box::class)
        ->onlyFields()
        ->toHaveCount(3);
});

it('detail fields with outside', function () {
    expect($this->resource->getDetailFields(withOutside: true))
        ->toHaveCount(4);
});

it('detail fields without outside', function () {
    expect($this->resource->getDetailFields(withOutside: false))
        ->toHaveCount(3);
});

it('detail fields only outside', function () {
    expect($this->resource->getDetailFields(onlyOutside: true))
        ->toHaveCount(1);
});

it('outside fields', function () {
    expect($this->resource->getOutsideFields())
        ->toHaveCount(1)
        ->each->toBeInstanceOf(ModelRelationField::class);
});

it('filters fields', function () {
    expect($this->resource->getFilters())
        ->first()
        ->toBeInstanceOf(Box::class)
        ->onlyFields()
        ->toHaveCount(1)
        ->each(static fn ($expect) => $expect->getNameAttribute()->toContain('filters'));
});
