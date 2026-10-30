<?php

declare(strict_types=1);

use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Http\Controllers\BelongsToManyPivotController;
use MoonShine\Laravel\Http\Requests\Relations\BelongsToManyPivotRequest;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Support\Enums\PageType;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestReadOnlyResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

uses()->group('belongs-to-many-pivot-controller');
uses()->group('resources-policies');

mutates(BelongsToManyPivotController::class, BelongsToManyPivotRequest::class);

beforeEach(function (): void {
    $this->item = createItem();
    $this->category = Category::factory()->create();

    $this->resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([
            ID::make(),
            Text::make('Name', 'name'),
            BelongsToMany::make('Categories', resource: TestCategoryResource::class)
                ->fields([
                    Text::make('Pivot 1', 'pivot_1'),
                ]),
        ])
        ->setTestPolicy(true);
});

it('stores belongs to many pivot records when update policy allows it', function (): void {
    asAdmin()
        ->post($this->moonshineCore->getRouter()->to('belongs-to-many-pivot.store', [
            'pageUri' => PageType::FORM->value,
            'resourceUri' => $this->resource->getUriKey(),
            'resourceItem' => $this->item->getKey(),
            '_relation' => 'categories',
            '_key' => $this->category->getKey(),
        ]), [
            'pivot_1' => 'allowed',
        ])
        ->assertOk();

    expect($this->item->categories()->whereKey($this->category->getKey())->exists())
        ->toBeTrue();

    expect($this->item->categories()->sole()->pivot->pivot_1)->toBe('allowed');
});

it('updates or deletes only the selected pivot record', function (string $method, string $route): void {
    $otherCategory = Category::factory()->create();
    $this->item->categories()->attach($this->category->getKey(), ['pivot_1' => 'original']);
    $this->item->categories()->attach($otherCategory->getKey(), ['pivot_1' => 'keep']);

    asAdmin()->{$method}($this->resource->getRoute($route, $this->item->getKey(), query: [
        'pageUri' => PageType::FORM->value,
        '_relation' => 'categories',
        '_key' => $this->category->getKey(),
    ]), ['pivot_1' => 'updated'])->assertOk();

    $related = $this->item->categories()->whereKey($this->category->getKey())->first();

    if ($method === 'put') {
        expect($related?->pivot->pivot_1)->toBe('updated');
    } else {
        expect($related)->toBeNull();
    }

    expect($this->item->categories()->whereKey($otherCategory->getKey())->sole()->pivot->pivot_1)->toBe('keep');
})->with([
    'update' => ['put', 'belongs-to-many-pivot.update'],
    'delete' => ['delete', 'belongs-to-many-pivot.destroy'],
]);

it('preserves existing pivot data when update policy denies a change', function (string $method, string $route): void {
    $this->item->categories()->attach($this->category->getKey(), ['pivot_1' => 'original']);
    MoonshineUser::query()->whereKey(1)->update(['name' => 'Policies test']);

    asAdmin()->{$method}($this->resource->getRoute($route, $this->item->getKey(), query: [
        'pageUri' => PageType::FORM->value,
        '_relation' => 'categories',
        '_key' => $this->category->getKey(),
    ]), ['pivot_1' => 'denied'])->assertForbidden();

    expect($this->item->categories()->sole()->pivot->pivot_1)->toBe('original');
})->with([
    'update' => ['put', 'belongs-to-many-pivot.update'],
    'delete' => ['delete', 'belongs-to-many-pivot.destroy'],
]);

it('rejects pivot changes when the update action is disabled', function (): void {
    $resource = app(TestReadOnlyResource::class)
        ->setTestModel(Item::class)
        ->setTestPolicy(false)
        ->setTestFields($this->resource->getFormFields()->all());
    $this->moonshineCore->resources([$resource]);

    asAdmin()->post($resource->getRoute('belongs-to-many-pivot.store', $this->item->getKey(), query: [
        'pageUri' => PageType::FORM->value,
        '_relation' => 'categories',
        '_key' => $this->category->getKey(),
    ]), ['pivot_1' => 'denied'])->assertForbidden();

    expect($this->item->categories()->exists())->toBeFalse();
});

it('rejects pivot changes without a related key', function (string $method, string $route): void {
    $this->item->categories()->attach($this->category->getKey(), ['pivot_1' => 'original']);

    asAdmin()->{$method}($this->resource->getRoute($route, $this->item->getKey(), query: [
        'pageUri' => PageType::FORM->value,
        '_relation' => 'categories',
    ]), ['pivot_1' => 'invalid'])->assertBadRequest();

    expect($this->item->categories()->sole()->pivot->pivot_1)->toBe('original');
})->with([
    'store' => ['post', 'belongs-to-many-pivot.store'],
    'update' => ['put', 'belongs-to-many-pivot.update'],
    'delete' => ['delete', 'belongs-to-many-pivot.destroy'],
]);

it('forbids belongs to many pivot mutation when update policy denies it', function (): void {
    MoonshineUser::query()->whereKey(1)->update([
        'name' => 'Policies test',
    ]);

    asAdmin()
        ->post($this->moonshineCore->getRouter()->to('belongs-to-many-pivot.store', [
            'pageUri' => PageType::FORM->value,
            'resourceUri' => $this->resource->getUriKey(),
            'resourceItem' => $this->item->getKey(),
            '_relation' => 'categories',
            '_key' => $this->category->getKey(),
        ]), [
            'pivot_1' => 'denied',
        ])
        ->assertForbidden();

    expect($this->item->categories()->whereKey($this->category->getKey())->exists())
        ->toBeFalse();
});

it('applies pivot field transformations before persisting the value', function (): void {
    $this->resource->setTestFields([
        BelongsToMany::make('Categories', resource: TestCategoryResource::class)->fields([
            Text::make('Pivot 1', 'pivot_1')->onApply(
                static fn (mixed $data, mixed $value): array => ['pivot_1' => strtoupper(trim($value))],
            ),
        ]),
    ]);

    asAdmin()->post($this->resource->getRoute('belongs-to-many-pivot.store', $this->item->getKey(), query: [
        'pageUri' => PageType::FORM->value,
        '_relation' => 'categories',
        '_key' => $this->category->getKey(),
    ]), ['pivot_1' => '  normalized  '])->assertOk();

    expect($this->item->categories()->sole()->pivot->pivot_1)->toBe('NORMALIZED');
});
