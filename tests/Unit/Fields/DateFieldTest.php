<?php

use MoonShine\Fields\Date;
use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Date::make('Created at');
});

it('text field is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('date');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});

it('default format', function (): void {
    expect($this->field->getFormat())
        ->toBe('Y-m-d H:i:s');
});

it('change format', function (): void {
    $this->field->format('d.m.Y');

    expect($this->field->getFormat())
        ->toBe('d.m.Y');
});

it('with time', function (): void {
    $this->field->withTime();

    expect($this->field->type())
        ->toBe('datetime-local');
});

it('index view value', function (): void {
    $item = MoonshineUser::factory()->create();

    expect($this->field->indexViewValue($item))
        ->toBe($item->created_at->format('Y-m-d H:i:s'))
        ->and($this->field->format('d.m'))
        ->indexViewValue($item)
        ->toBe($item->created_at->format('d.m'))
    ;
});

it('form view value', function (): void {
    $item = MoonshineUser::factory()->create();
    $itemDateNull = MoonshineUser::factory()->create([
        'created_at' => null,
    ]);

    expect($this->field->formViewValue($item))
        ->toBe($item->created_at->format('Y-m-d'))
        ->and($this->field->nullable())
        ->formViewValue($itemDateNull)
        ->toBeEmpty()
        ->and($this->field->default('2000-01-12'))
        ->formViewValue($itemDateNull)
        ->toBe('2000-01-12')
        ->and($this->field->withTime())
        ->formViewValue($item)
        ->toBe($item->created_at->format('Y-m-d\TH:i'))
    ;
});
