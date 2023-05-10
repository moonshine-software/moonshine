<?php

use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\MorphTo;
use MoonShine\Models\MoonshineUser;

uses()->group('fields');
uses()->group('relation-fields');
uses()->group('morph-fields');

beforeEach(function () {
    $this->field = MorphTo::make('Morph to');
});

it('belongs to is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(BelongsTo::class);
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.morph-to');
});

it('types', function () {
    expect($this->field->types([MoonshineUser::class => 'users']))
        ->getTypes()
        ->toBe([MoonshineUser::class => 'MoonshineUser'])
        ->and($this->field->getSearchColumn(MoonshineUser::class))
        ->toBe('users')
        ->and($this->field->isAsyncSearch())
        ->toBeTrue();
});
