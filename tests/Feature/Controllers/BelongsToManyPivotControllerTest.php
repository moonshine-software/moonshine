<?php

declare(strict_types=1);

use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Support\Enums\PageType;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

uses()->group('belongs-to-many-pivot-controller');
uses()->group('resources-policies');

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
});

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
