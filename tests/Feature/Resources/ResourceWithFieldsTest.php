<?php

declare(strict_types=1);

use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Relationships\HasOne;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Fields\StackFields;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('resources-feature');

beforeEach(function (): void {
    $this->resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([
            Block::make([
                ID::make()->sortable()->useOnImport()->showOnExport(),
                Text::make('Name title', 'name')
                    ->sortable(),

                Text::make('Form field')
                    ->hideOnDetail()
                    ->showOnForm()
                    ->hideOnIndex(),

                StackFields::make()->fields([
                    Text::make('Index field')
                        ->hideOnDetail()
                        ->hideOnForm()
                        ->showOnIndex(),
                ]),

                Text::make('Detail field')
                    ->hideOnIndex()
                    ->hideOnForm()
                    ->showOnDetail(),

                HasOne::make('Outside', 'outside', resource: new TestResource()),
            ]),

        ])
        ->setTestFilters([
            Block::make([
                Text::make('Name title', 'name')
                    ->sortable(),
            ]),
        ]);
});

it('index fields', function () {
    expect($this->resource->getIndexFields())
        ->toHaveCount(4)
        ->each(fn($expect) => $expect->isOnIndex()->toBeTrue());
});

it('form fields with outside', function () {
    expect($this->resource->getFormFields(withOutside: true))
        ->first()
        ->toBeInstanceOf(Block::class)
        ->onlyFields()
        ->toHaveCount(4)
        ->each(fn($expect) => $expect->isOnForm()->toBeTrue());
});

it('form fields without outside', function () {
    expect($this->resource->getFormFields())
        ->first()
        ->toBeInstanceOf(Block::class)
        ->onlyFields()
        ->toHaveCount(3)
        ->each(fn($expect) => $expect->isOnForm()->toBeTrue());
});

it('detail fields with outside', function () {
    expect($this->resource->getDetailFields())
        ->toHaveCount(4)
        ->each(fn($expect) => $expect->isOnDetail()->toBeTrue());
});

it('detail fields only outside', function () {
    expect($this->resource->getDetailFields(onlyOutside: true))
        ->toHaveCount(1)
        ->each(fn($expect) => $expect->isOnDetail()->toBeTrue());
});

it('outside fields', function () {
    expect($this->resource->getOutsideFields())
        ->toHaveCount(1)
        ->each->toBeInstanceOf(ModelRelationField::class);
});

it('filters fields', function () {
    expect($this->resource->getFilters())
        ->first()
        ->toBeInstanceOf(Block::class)
        ->onlyFields()
        ->toHaveCount(1)
        ->each(fn($expect) => $expect->name()->toContain('filters'));
});
