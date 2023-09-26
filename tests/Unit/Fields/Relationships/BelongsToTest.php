<?php

declare(strict_types=1);

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Models\MoonshineUser;
use MoonShine\Resources\MoonShineUserResource;
use MoonShine\Tests\Fixtures\Models\Item;

uses()->group('model-relation-fields');
uses()->group('belongs-to-field');

beforeEach(function (): void {

    $this->user = MoonshineUser::factory()
        ->count(5)
        ->create();

    $this->item = Item::factory()
        ->create();

    $this->field = BelongsTo::make('User', resource: new MoonShineUserResource());

});

it('ModelRelationField is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(ModelRelationField::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBeEmpty();
});

it('correct interfaces', function (): void {
    expect($this->field)
        ->toBeInstanceOf(HasAsyncSearch::class)
        ->toBeInstanceOf(HasRelatedValues::class)
        ->toBeInstanceOf(HasDefaultValue::class)
        ->toBeInstanceOf(DefaultCanBeString::class)
        ->toBeInstanceOf(DefaultCanBeNumeric::class)
    ;
});

it('async search', function (): void {
    expect($this->field->asyncSearch('name'))
        ->isAsyncSearch()
        ->toBeTrue()
        ->asyncSearchColumn()
        ->toBe('name');
});

describe('basic methods', function () {
    it('change preview', function () {
        expect($this->field->changePreview(static fn () => 'changed'))
            ->preview()
            ->toBe('changed');
    });

    it('formatted value', function () {
        $field = BelongsTo::make('User', formatted: static fn () => ['changed'], resource: new MoonShineUserResource())
            ->fill($this->item->toArray(), $this->item);

        expect($field->toFormattedValue())
            ->toBe(['changed']);
    });

    it('applies', function () {
        $field = $this->field
            ->onApply(fn ($data) => ['onApply']);

        expect($field->onApply(fn ($data) => ['onApply'])->apply(fn ($data) => $data, []))
            ->toBe(['onApply'])
            ->and($field->onBeforeApply(fn ($data) => ['onBeforeApply'])->beforeApply([]))
            ->toBe(['onBeforeApply'])
            ->and($field->onAfterApply(fn ($data) => ['onAfterApply'])->afterApply([]))
            ->toBe(['onAfterApply'])
            ->and($field->onAfterDestroy(fn ($data) => ['onAfterDestroy'])->afterDestroy([]))
            ->toBe(['onAfterDestroy']);
    });
});
