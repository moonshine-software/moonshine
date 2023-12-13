<?php

use MoonShine\Components\FormBuilder;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\LineBreak;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Fields\Field;
use MoonShine\Fields\Switcher;
use MoonShine\Pages\PageComponents;

uses()->group('core');
uses()->group('new');

beforeEach(function (): void {
    $this->data = Block::make([
        FormBuilder::make()->fields([
            Tabs::make([
                Tab::make([
                    LineBreak::make()->name('line-break'),
                    FormBuilder::make()->name('inner-form')->fields([
                        Switcher::make('Switcher')
                    ]),

                ]),
                Tab::make([
                    TableBuilder::make()
                        ->name('first-table'),

                    TableBuilder::make()
                        ->name('second-table')
                ]),
            ])
        ])->name('parent-form'),

        TableBuilder::make()
            ->name('parent-table')
    ])->name('block');

    $this->collection = PageComponents::make([
        $this->data
    ]);
});

it('find table', function () {
    expect($this->collection->findTable('second-table'))
        ->toBeInstanceOf(TableBuilder::class)
        ->getName()
        ->toBe('second-table');
});

it('only forms', function () {
    expect($this->collection->onlyForms())
        ->toHaveCount(2)
        ->each->toBeInstanceOf(FormBuilder::class);
});

it('only tables', function () {
    expect($this->collection->onlyTables())
        ->toHaveCount(3)
        ->each->toBeInstanceOf(TableBuilder::class);
});

it('only fields', function () {
    expect($this->collection->onlyFields())
        ->toHaveCount(1)
        ->each->toBeInstanceOf(Field::class);
});

it('find form', function () {
    expect($this->collection->findForm('inner-form'))
        ->toBeInstanceOf(FormBuilder::class)
        ->getName()
        ->toBe('inner-form');
});

it('find field', function () {
    expect($this->collection->onlyFields()->findByColumn('switcher'))
        ->toBeInstanceOf(Switcher::class)
        ->name()
        ->toBe('switcher');
});

it('find by name', function () {
    expect($this->collection->findByName('line-break'))
        ->toBeInstanceOf(LineBreak::class)
        ->getName()
        ->toBe('line-break');
});
