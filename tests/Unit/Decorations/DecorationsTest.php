<?php

use MoonShine\Decorations\Block;
use MoonShine\Decorations\Button;
use MoonShine\Decorations\Collapse;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\Heading;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Fields\Text;

uses()->group('decorations');

beforeEach(function () {
    $this->fields = [
        Text::make('Field 1'),
        Text::make('Field 2'),
    ];
});

it('block', function () {
    $decoration = Block::make('Label', $this->fields);

    expect($decoration)
        ->getFields()
        ->hasFields($this->fields)
        ->getView()
        ->toBe('moonshine::decorations.block')
        ->label()
        ->toBe('Label')
        ->id()
        ->toBe('label')
        ->name()
        ->toBe('label');
});

it('heading', function () {
    $decoration = Heading::make('Label');

    expect($decoration)
        ->getView()
        ->toBe('moonshine::decorations.heading')
        ->label()
        ->toBe('Label')
        ->id()
        ->toBe('label')
        ->name()
        ->toBe('label');
});

it('collapse', function () {
    $decoration = Collapse::make('Label', $this->fields);

    expect($decoration)
        ->getFields()
        ->hasFields($this->fields)
        ->getView()
        ->toBe('moonshine::decorations.collapse')
        ->label()
        ->toBe('Label')
        ->id()
        ->toBe('label')
        ->name()
        ->toBe('label')
        ->isShow()
        ->toBeFalse()
        ->and($decoration->show())
        ->isShow()
        ->toBeTrue();
});

it('button', function () {
    $link = fake()->url();
    $decoration = Button::make('Label', $link);

    expect($decoration)
        ->getView()
        ->toBe('moonshine::decorations.button')
        ->label()
        ->toBe('Label')
        ->getLinkName()
        ->toBe('Label')
        ->getLinkValue()
        ->toBe($link)
        ->isLinkBlank()
        ->toBeFalse();
});

it('divider', function () {
    $decoration = Divider::make();

    expect($decoration)
        ->getView()
        ->toBe('moonshine::decorations.divider');
});

it('column', function () {
    $decoration = Column::make($this->fields)->columnSpan(3, 4);

    expect($decoration)
        ->getFields()
        ->hasFields($this->fields)
        ->getView()
        ->toBe('moonshine::decorations.column')
        ->columnSpanValue()
        ->toBe(3)
        ->adaptiveColumnSpanValue()
        ->toBe(4);
});

it('grid', function () {
    $decoration = Grid::make($this->fields);

    expect($decoration)
        ->getFields()
        ->hasFields($this->fields)
        ->getView()
        ->toBe('moonshine::decorations.grid');
});

it('flex', function () {
    $decoration = Flex::make($this->fields)
        ->itemsAlign('right');

    expect($decoration)
        ->getFields()
        ->hasFields($this->fields)
        ->getView()
        ->toBe('moonshine::decorations.flex')
        ->getItemsAlign()
        ->toBe('right')
        ->isWithoutSpace()
        ->toBeFalse()
        ->and($decoration->withoutSpace())
        ->isWithoutSpace()
        ->toBeTrue();
});

it('tabs', function () {
    $decoration = Tabs::make([
        Tab::make('Tab 1', $this->fields),
        Tab::make('Tab 2', $this->fields),
    ]);

    expect($decoration)
        ->tabs()
        ->toBeCollection()
        ->toHaveCount(2)
        ->getFields()
        ->each->hasFields($this->fields)
        ->getView()
        ->toBe('moonshine::decorations.tabs');
});
