<?php

use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Relationships\HasManyThrough;

uses()->group('fields');
uses()->group('relation-fields');
uses()->group('has-one-or-many-fields');

beforeEach(function (): void {
    $this->field = HasManyThrough::make('Has many')->fields(
        exampleFields()->toArray()
    );
});

it('has many is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(HasMany::class);
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.has-many');
});

it('is group', function (): void {
    expect($this->field->isGroup())
        ->toBeTrue();
});
