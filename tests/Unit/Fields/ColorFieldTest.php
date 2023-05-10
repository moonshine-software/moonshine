<?php

use MoonShine\Fields\Color;
use MoonShine\Fields\Text;

uses()->group('fields');

beforeEach(function () {
    $this->field = Color::make('Color');
});

it('text is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function () {
    expect($this->field->type())
        ->toBe('text');
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.color');
});
