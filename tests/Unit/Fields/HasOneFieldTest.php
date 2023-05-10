<?php

use MoonShine\Fields\HasMany;
use MoonShine\Fields\HasOne;

uses()->group('fields');
uses()->group('relation-fields');
uses()->group('has-one-or-many-fields');

beforeEach(function () {
    $this->field = HasOne::make('Has one')->fields(
        exampleFields()->toArray()
    );
});

it('has many is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(HasMany::class);
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.has-one');
});

it('is group', function () {
    expect($this->field->isGroup())
        ->toBeTrue();
});
