<?php

use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasFullPageMode;
use MoonShine\Contracts\Fields\HasJsonValues;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Contracts\Fields\Relationships\HasResourceMode;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\HasMany;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');
uses()->group('relation-fields');
uses()->group('has-one-or-many-fields');

beforeEach(function () {
    $this->field = HasMany::make('Has many')->fields(
        exampleFields()->toArray()
    );
});

it('names', function () {
    expect($this->field)
        ->name()
        ->toBe('hasMany[]')
        ->name('1')
        ->toBe('hasMany[1]');
});

it('relation methods', function () {
    $resource = TestResourceBuilder::new();
    $this->field->setResource($resource);

    expect($this->field)
        ->label()
        ->toBe('Has many')
        ->relation()
        ->toBe('hasMany')
        ->resource()
        ->toBe($resource);
});

it('correct interfaces', function () {
    expect($this->field)
        ->toBeInstanceOf(HasRelationship::class)
        ->toBeInstanceOf(HasFields::class)
        ->toBeInstanceOf(HasResourceMode::class)
        ->toBeInstanceOf(HasJsonValues::class)
        ->toBeInstanceOf(RemovableContract::class)
        ->toBeInstanceOf(HasFullPageMode::class);
});

it('type', function () {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.has-many');
});

it('is group', function () {
    expect($this->field->isGroup())
        ->toBeTrue();
});

it('removable methods', function () {
    expect($this->field)
        ->isRemovable()
        ->toBeFalse()
        ->and($this->field->removable())
        ->isRemovable()
        ->toBeTrue();
});

it('resource mode', function () {
    $this->field->setResource(TestResourceBuilder::new());

    expect($this->field)
        ->isResourceMode()
        ->toBeFalse()
        ->and($this->field->resourceMode())
        ->isResourceMode()
        ->toBeTrue();
});

it('full page mode', function () {
    expect($this->field)
        ->isFullPage()
        ->toBeFalse()
        ->and($this->field->fullPage())
        ->isFullPage()
        ->toBeTrue();
});

it('resource mode throw exception', function () {
    $this->field->resourceMode();
})->throws(FieldException::class);

it('has fields', function () {
    expect($this->field->getFields())
        ->hasFields(exampleFields()->toArray())
        ->each(function ($field, $key) {
            $key++;

            $field->toBeInstanceOf(Text::class)
                ->name()
                ->toBe('hasMany[${index0}][field'.$key.']')
                ->id()
                ->toBe('has_many_field'.$key);
        });
});

it('json values', function () {
    expect($this->field->jsonValues())
        ->toBeArray()
        ->toBe(exampleFields()
            ->mapWithKeys(fn($f) => [$f->field() => ''])
            ->toArray());
});
