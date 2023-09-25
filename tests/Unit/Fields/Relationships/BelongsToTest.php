<?php

declare(strict_types=1);

use MoonShine\Fields\Relationships\BelongsTo;
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
});

describe('basic methods', function () {
    it('change preview', function () {
        expect(BelongsTo::make('User', resource: new MoonShineUserResource())->changePreview(static fn () => 'changed'))
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
        $field = BelongsTo::make('User', resource: new MoonShineUserResource())
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