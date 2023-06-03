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

beforeEach(function (): void {
    $this->fields = [
        Text::make('Field 1'),
        Text::make('Field 2'),
    ];
});

it('block', function (): void {
    $decoration = Block::make('Label', $this->fields);

    expect($decoration)
        ->getFields()
        ->hasFields($this->fields)
        ->getView()
        ->toBe('moonshine::decorations.block')
        ->label()
        ->toBe('Label')
        ->id()
        ->toBeString();
});

it('heading', function (): void {
    $decoration = Heading::make('Label');

    expect($decoration)
        ->getView()
        ->toBe('moonshine::decorations.heading')
        ->label()
        ->toBe('Label')
        ->id()
        ->toBeString();
});

it('collapse', function (): void {
    $decoration = Collapse::make('Label', $this->fields);

    expect($decoration)
        ->getFields()
        ->hasFields($this->fields)
        ->getView()
        ->toBe('moonshine::decorations.collapse')
        ->label()
        ->toBe('Label')
        ->id()
        ->toBeString()
        ->isShow()
        ->toBeFalse()
        ->and($decoration->show())
        ->isShow()
        ->toBeTrue();
});

it('button', function (): void {
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

it('divider', function (): void {
    $decoration = Divider::make();

    expect($decoration)
        ->getView()
        ->toBe('moonshine::decorations.divider');
});

it('column', function (): void {
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

it('grid', function (): void {
    $decoration = Grid::make($this->fields);

    expect($decoration)
        ->getFields()
        ->hasFields($this->fields)
        ->getView()
        ->toBe('moonshine::decorations.grid');
});

it('flex', function (): void {
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

it('tabs', function (): void {
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
