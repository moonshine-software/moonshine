<?php

use MoonShine\Fields\Field;
use MoonShine\Fields\FormElement;
use MoonShine\Fields\Textarea;

uses()->group('fields');

beforeEach(function () {
    $this->field = Textarea::make('Field name');
});

it('field and form element is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(Field::class)
        ->toBeInstanceOf(FormElement::class);
});

it('type', function () {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.textarea');
});
