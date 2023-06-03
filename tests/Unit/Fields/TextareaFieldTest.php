<?php

use MoonShine\Fields\Field;
use MoonShine\Fields\FormElement;
use MoonShine\Fields\Textarea;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Textarea::make('Field name');
});

it('field and form element is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Field::class)
        ->toBeInstanceOf(FormElement::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.textarea');
});
