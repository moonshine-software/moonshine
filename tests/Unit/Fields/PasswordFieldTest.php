<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Password;
use MoonShine\Fields\Text;

uses()->group('fields');

beforeEach(function () {
    $this->field = Password::make('Password');
    $this->item = new class () extends Model {
        public string $password = '';
    };
});

it('text field is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function () {
    expect($this->field->type())
        ->toBe('password');
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});

it('index view value', function () {
    expect($this->field->indexViewValue($this->item))
        ->toBe('***');
});

it('export view value', function () {
    expect($this->field->exportViewValue($this->item))
        ->toBe('***');
});

it('save', function () {
    fakeRequest(parameters: ['password' => 12345]);

    expect($this->field->save($this->item))
        ->password
        ->toBeString();
});
