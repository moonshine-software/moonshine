<?php

use MoonShine\Fields\Password;
use MoonShine\Fields\PasswordRepeat;

uses()->group('fields');

beforeEach(function () {
    $this->field = PasswordRepeat::make('Password');
});

it('password field is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(Password::class);
});

it('type', function () {
    expect($this->field->type())
        ->toBe('password');
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});

