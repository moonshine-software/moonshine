<?php

use MoonShine\Fields\BelongsToMany;
use MoonShine\Fields\MorphToMany;

uses()->group('fields');
uses()->group('relation-fields');
uses()->group('morph-fields');

beforeEach(function (): void {
    $this->field = MorphToMany::make('Morph to many');
});

it('belongs to many is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(BelongsToMany::class);
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.belongs-to-many');
});

it('is group', function (): void {
    expect($this->field->isGroup())
        ->toBeTrue();
});
