<?php

use MoonShine\Fields\Date;
use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;

uses()->group('fields');

beforeEach(function () {
    $this->field = Date::make('Created at');
});

it('text field is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function () {
    expect($this->field->type())
        ->toBe('date');
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});

it('default format', function () {
    expect($this->field->getFormat())
        ->toBe('Y-m-d H:i:s');
});

it('change format', function () {
    $this->field->format('d.m.Y');

    expect($this->field->getFormat())
        ->toBe('d.m.Y');
});

it('with time', function () {
    $this->field->withTime();

    expect($this->field->type())
        ->toBe('datetime-local');
});

it('index view value', function () {
    $item = MoonshineUser::factory()->create();

    expect($this->field->indexViewValue($item))
        ->toBe($item->created_at->format('Y-m-d H:i:s'))
        ->and($this->field->format('d.m'))
        ->indexViewValue($item)
        ->toBe($item->created_at->format('d.m'))
    ;
});

it('form view value', function () {
    $item = MoonshineUser::factory()->create();
    $itemDateNull = MoonshineUser::factory()->create([
        'created_at' => null
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


