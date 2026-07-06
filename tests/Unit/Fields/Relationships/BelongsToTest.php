<?php

declare(strict_types=1);

use MoonShine\Crud\Contracts\Fields\HasAsyncSearchContract;
use MoonShine\Crud\Contracts\Fields\HasRelatedValuesContact;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Tests\Fixtures\Enums\TestEnumLabeled;
use MoonShine\Tests\Fixtures\Models\Comment;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestEnumColumnResource;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeObject;
use MoonShine\UI\Contracts\HasDefaultValueContract;

uses()->group('model-relation-fields');
uses()->group('belongs-to-field');

beforeEach(function (): void {
    $this->user = MoonshineUser::factory()
        ->count(5)
        ->create();

    $this->item = Item::factory()
        ->create();

    $this->field = BelongsTo::make('User', 'moonshineUser', resource: MoonShineUserResource::class);
});

describe('common field methods', function () {
    it('ModelRelationField is parent', function (): void {
        expect($this->field)
            ->toBeInstanceOf(ModelRelationField::class);
    });

    it('type', function (): void {
        expect($this->field->getAttributes()->get('type'))
            ->toBeEmpty();
    });

    it('correct interfaces', function (): void {
        expect($this->field)
            ->toBeInstanceOf(HasAsyncSearchContract::class)
            ->toBeInstanceOf(HasRelatedValuesContact::class)
            ->toBeInstanceOf(HasDefaultValueContract::class)
            ->toBeInstanceOf(CanBeObject::class);
    });
});

describe('unique field methods', function () {
    it('async search', function (): void {
        expect($this->field->asyncSearch('name'))
            ->isAsyncSearch()
            ->toBeTrue()
            ->getAsyncSearchColumn()
            ->toBe('name');
    });
});

describe('basic methods', function () {
    it('change preview', function () {
        expect($this->field->changePreview(static fn () => 'changed'))
            ->preview()
            ->toBe('changed');
    });

    it('formatted value', function () {
        $field = BelongsTo::make('User', formatted: static fn () => ['changed'], resource: MoonShineUserResource::class)
            ->fillData($this->item);

        expect($field->toFormattedValue())
            ->toBe(['changed']);
    });

    it('stringifies an enum display column of the related resource in preview', function () {
        $item = Item::factory()->create(['status' => TestEnumLabeled::Web]);
        $comment = Comment::factory()->create(['item_id' => $item->getKey()]);

        $field = BelongsTo::make('Item', 'item', resource: TestEnumColumnResource::class)
            ->fillData($comment);

        expect((string) $field->preview())
            ->toContain('web-platform');
    });

    it('does not load missing relation when model does not define it', function () {
        $field = BelongsTo::make('Post', 'post', resource: MoonShineUserResource::class)
            ->fillData($this->item);

        expect($field->toValue())
            ->toBeNull();
    });

    it('applies', function () {
        expect()
            ->applies($this->field);
    });
});

describe('reactive', function () {
    it('prepare value', function (): void {
        $except = [];
        $value = $this->user->first();
        $this->field->fillData($this->item);
        $this->field->setValue($this->user->first());

        $expect = $this->field->prepareReactivityValue($value->getKey(), $this->item, $except);

        expect($expect?->getKey())->toBe($value->getKey());
    });

    it('get value', function (): void {
        $value = $this->user->first();
        $this->field->setValue($this->user->first());
        $expect = $this->field->getReactiveValue();

        expect($expect)
            ->toBe($value->getKey())
        ;
    });
});
