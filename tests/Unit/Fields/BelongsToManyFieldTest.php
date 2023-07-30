<?php

use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasPivot;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;

uses()->group('fields');
uses()->group('relation-fields');

beforeEach(function (): void {
    $this->field = BelongsToMany::make('Role', 'roles', resource: 'name');
    $this->fieldWithPivot = BelongsToMany::make('Pivot', 'pivots', resource: 'name')
        ->fields(exampleFields()->toArray());
});

it('select is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Select::class);
});

it('correct interfaces', function (): void {
    expect($this->field)
        ->toBeInstanceOf(HasRelationship::class)
        ->toBeInstanceOf(HasRelatedValues::class)
        ->toBeInstanceOf(HasFields::class)
        ->toBeInstanceOf(HasPivot::class)
        ->toBeInstanceOf(HasAsyncSearch::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBeEmpty();
});

it('async search', function (): void {
    expect($this->field->asyncSearch('name'))
        ->isAsyncSearch()
        ->toBeTrue()
        ->asyncSearchColumn()
        ->toBe('name');
});

it('transformed to select', function (): void {
    $this->field->select();

    expect($this->field->isSelect())
        ->toBeTrue();
});

it('names', function (): void {
    expect($this->field)
        ->field()
        ->toBe('roles')
        ->name()
        ->toBe('roles[]')
        ->id()
        ->toBe('roles')
        ->name('1')
        ->toBe('roles[1]')
        ->id('1')
        ->toBe('roles_1')
        ->relation()
        ->toBe('roles')
        ->label()
        ->toBe('Role');
});

it('pivot fields', function (): void {
    expect($this->fieldWithPivot->getFields())
        ->hasFields(exampleFields()->toArray())
        ->each(function ($field, $index): void {
            $index++;
            $fieldName = "field$index";

            $field->toBeInstanceOf(Text::class)
                ->field()
                ->toBe($fieldName)
                ->name()
                ->toBe("pivots_{$fieldName}[]")
                ->id()
                ->toBe("pivots_$fieldName")
                ->relation()
                ->toBeNull()
                ->label()
                ->toBe("Field $index");
        })
        ->and($this->fieldWithPivot->hasFields())
        ->toBeTrue();
});
