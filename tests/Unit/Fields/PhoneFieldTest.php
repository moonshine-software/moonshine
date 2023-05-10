<?php

use MoonShine\Fields\Phone;
use MoonShine\Fields\Text;

uses()->group('fields');

beforeEach(function () {
    $this->field = Phone::make('Phone');
});

it('text field is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function () {
    expect($this->field->type())
        ->toBe('tel');
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});
