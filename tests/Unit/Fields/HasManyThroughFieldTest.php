<?php

use MoonShine\Fields\HasMany;
use MoonShine\Fields\HasManyThrough;

uses()->group('fields');
uses()->group('relation-fields');
uses()->group('has-one-or-many-fields');

beforeEach(function () {
    $this->field = HasManyThrough::make('Has many')->fields(
        exampleFields()->toArray()
    );
});

it('has many is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(HasMany::class);
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.has-many');
});

it('is group', function () {
    expect($this->field->isGroup())
        ->toBeTrue();
});
