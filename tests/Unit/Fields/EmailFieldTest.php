<?php

use MoonShine\Fields\Email;
use MoonShine\Fields\Text;

uses()->group('fields');

beforeEach(function () {
    $this->field = Email::make('Email');
});

it('text field is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function () {
    expect($this->field->type())
        ->toBe('email');
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});
