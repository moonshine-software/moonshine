<?php

use MoonShine\Fields\Phone;
use MoonShine\Fields\Text;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Phone::make('Phone');
});

it('text field is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('tel');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});
