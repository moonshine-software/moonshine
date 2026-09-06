<?php

declare(strict_types=1);

use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Tests\Fixtures\Enums\TestEnumBreadcrumbLabel;
use MoonShine\Tests\Fixtures\Enums\TestEnumColor;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;

uses()->group('model-relation-fields');

it('renders enum labels in relationship options', function (string $enumClass, string $raw, string $label) {
    $item = createItem(countComments: 1);
    $item->update(['name' => $raw]);
    $item->mergeCasts(['name' => $enumClass]);
    $comment = $item->comments->first()->setRelation('item', $item);

    $field = BelongsTo::make('Item', 'item', resource: TestItemResource::class)
        ->nowOn(page: app(TestCommentResource::class)->getFormPage(), resource: app(TestCommentResource::class))
        ->valuesQuery(fn ($query) => $query->whereKey($item->getKey())->withCasts(['name' => $enumClass]))
        ->fillData($comment);

    expect($field->getValues()->getValues()->first()->getLabel())->toBe($label)
        ->and((string) $field->render())->toContain($label);
})->with([
    'backed enum' => [TestEnumColor::class, 'R', 'R'],
    'custom label' => [TestEnumBreadcrumbLabel::class, 'blue', 'Blue label'],
]);

it('renders enum labels in async relationship search', function (bool $formatted, string $enumClass, string $raw, string $label) {
    $item = createItem(countComments: 0);
    $item->update(['name' => $raw]);
    $item->mergeCasts(['name' => $enumClass]);

    $field = BelongsTo::make('Item', 'item', resource: TestItemResource::class)->asyncSearch(
        'name',
        formatted: $formatted ? fn ($model) => $model->name : null,
    );

    expect($field->getAsyncSearchOption($item)->getLabel())->toBe($label);
})->with(['column' => false, 'callback' => true])->with([
    'backed enum' => [TestEnumColor::class, 'R', 'R'],
    'custom label' => [TestEnumBreadcrumbLabel::class, 'blue', 'Blue label'],
]);

it('renders enum labels in relationship previews', function (string $enumClass, string $raw, string $label) {
    $item = createItem(countComments: 1);
    $item->update(['name' => $raw]);
    $item->mergeCasts(['name' => $enumClass]);
    $comment = $item->comments->first()->setRelation('item', $item);

    $field = BelongsTo::make('Item', 'item', resource: TestItemResource::class)
        ->nowOn(page: app(TestCommentResource::class)->getIndexPage(), resource: app(TestCommentResource::class))
        ->fillData($comment);

    expect((string) $field->preview())->toContain($label);
})->with([
    'backed enum' => [TestEnumColor::class, 'R', 'R'],
    'custom label' => [TestEnumBreadcrumbLabel::class, 'blue', 'Blue label'],
]);
