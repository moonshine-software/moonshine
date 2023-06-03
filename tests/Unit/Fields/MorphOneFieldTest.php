<?php

use MoonShine\Fields\HasOne;
use MoonShine\Fields\MorphOne;

uses()->group('fields');
uses()->group('relation-fields');
uses()->group('has-one-or-many-fields');
uses()->group('morph-fields');

beforeEach(function (): void {
    $this->field = MorphOne::make('Morph one')->fields(
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
