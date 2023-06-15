<?php

use MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\Select;
use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\MoonShine;
use MoonShine\Resources\MoonShineUserRoleResource;

uses()->group('fields');
uses()->group('relation-fields');

beforeEach(function (): void {
    $this->field = BelongsTo::make('Moonshine user role', resource: 'name');
    $this->item = MoonshineUser::factory()->create();
});

it('select is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Select::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBeEmpty();
});

it('index view value', function (): void {
    expect($this->field->indexViewValue($this->item))
        ->toBe(view('moonshine::ui.badge', [
            'color' => 'purple',
            'value' => "<a href=''>{$this->item->moonshineUserRole->name}</a>",
        ])->render());
});

it('correct interfaces', function (): void {
    expect($this->field)
        ->toBeInstanceOf(HasRelationship::class)
        ->toBeInstanceOf(HasRelatedValues::class)
        ->toBeInstanceOf(BelongsToRelation::class)
        ->toBeInstanceOf(HasAsyncSearch::class);
});

it('correctly value callback', function (): void {
    $this->field = BelongsTo::make('Moonshine user role', resource: fn ($item): string => $item->id . $item->name);

    expect($this->field->indexViewValue($this->item, false))
        ->toBe($this->item->moonshineUserRole->id . $this->item->moonshineUserRole->name)
        ->and($this->field->formViewValue($this->item))
        ->toBe($this->item->moonshineUserRole->id);
});

it('multiple always false', function (): void {
    expect($this->field->isMultiple())
        ->toBeFalse()
        ->and($this->field->multiple())
        ->isMultiple()
        ->toBeFalse();
});

it('related values', function (): void {
    expect($this->field->relatedValues($this->item))
        ->toBe(
            MoonshineUserRole::query()->pluck('name', 'id')->toArray()
        );
});

it('async search', function (): void {
    expect($this->field->asyncSearch('name'))
        ->isAsyncSearch()
        ->toBeTrue()
        ->asyncSearchColumn()
        ->toBe('name');
});

it('selected correctly', function (): void {
    expect($this->field->isSelected($this->item, $this->item->moonshine_user_role_id))
        ->toBeTrue();
});
