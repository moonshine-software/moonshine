<?php

declare(strict_types=1);

use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = PasswordRepeat::make('Password');
});

it('password field is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Password::class);
});

it('type', function (): void {
    expect($this->field->getAttributes()->get('type'))
        ->toBe('password');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});
