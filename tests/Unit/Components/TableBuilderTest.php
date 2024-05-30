<?php

use MoonShine\Laravel\TypeCasts\ModelCast;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\UI\Components\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

uses()->group('table-builder');

beforeEach(function () {
    $this->items = Item::factory(10)->create();

    $this->builder = TableBuilder::make()
        ->fields([
            ID::make(),
            Text::make('Name'),
        ])
        ->items(Item::query()->get())
        ->cast(ModelCast::make(Item::class))
    ;
});

it('without duplicates', function () {
    expect((string) $this->builder->render())
        ->toContain(
            $this->items[0]->name,
            $this->items[1]->name,
            $this->items[2]->name,
            $this->items[3]->name,
        )
    ;
});
