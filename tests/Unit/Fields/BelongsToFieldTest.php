<?php

use MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\Select;
use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;

uses()->group('fields');
uses()->group('relation-fields');

beforeEach(function () {
    $this->field = BelongsTo::make('Moonshine user role', resource: 'name');
    $this->item = MoonshineUser::factory()->create();
});

it('select is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(Select::class);
});

it('type', function () {
    expect($this->field->type())
        ->toBeEmpty();
});

it('index view value', function () {
    expect($this->field->indexViewValue($this->item))
        ->toBe(view('moonshine::ui.badge', [
            'color' => 'purple',
            'value' => $this->item->moonshineUserRole->name,
        ])->render());
});

it('correct interfaces', function () {
    expect($this->field)
        ->toBeInstanceOf(HasRelationship::class)
        ->toBeInstanceOf(HasRelatedValues::class)
        ->toBeInstanceOf(BelongsToRelation::class)
        ->toBeInstanceOf(HasAsyncSearch::class);
});

it('correctly value callback', function () {
    $this->field = BelongsTo::make('Moonshine user role', resource: function ($item) {
        return $item->id.$item->name;
    });

    expect($this->field->indexViewValue($this->item, false))
        ->toBe($this->item->moonshineUserRole->id.$this->item->moonshineUserRole->name)
        ->and($this->field->formViewValue($this->item))
        ->toBe($this->item->moonshineUserRole->id);
});

it('multiple always false', function () {
    expect($this->field->isMultiple())
        ->toBeFalse()
        ->and($this->field->multiple())
        ->isMultiple()
        ->toBeFalse();
});

it('related values', function () {
    expect($this->field->relatedValues($this->item))
        ->toBe(
            MoonshineUserRole::query()->pluck('name', 'id')->toArray()
        );
});

it('async search', function () {
    expect($this->field->asyncSearch('name'))
        ->isAsyncSearch()
        ->toBeTrue()
        ->asyncSearchColumn()
        ->toBe('name');
});

it('selected correctly', function () {
    expect($this->field->isSelected($this->item, $this->item->moonshine_user_role_id))
        ->toBeTrue();
});


