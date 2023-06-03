<?php

use MoonShine\Fields\HasOne;
use MoonShine\Fields\HasOneThrough;

uses()->group('fields');
uses()->group('relation-fields');
uses()->group('has-one-or-many-fields');

beforeEach(function (): void {
    $this->field = HasOneThrough::make('Has one')->fields(
        exampleFields()->toArray()
    );
});

it('has one is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(HasOne::class);
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.has-one');
});

it('is group', function (): void {
    expect($this->field->isGroup())
        ->toBeTrue();
});
