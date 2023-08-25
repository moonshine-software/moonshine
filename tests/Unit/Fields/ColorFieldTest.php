<?php

use MoonShine\Fields\Color;
use MoonShine\Fields\Text;

uses()->group('fields');


beforeEach(function (): void {
    $this->field = Color::make('Color');
});

it('text is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('text');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.color');
});
