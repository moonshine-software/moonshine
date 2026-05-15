<?php

declare(strict_types=1);

use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Support\Enums\PageType;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\ImageModel;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestImageResource;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Fieldset;

uses()->group('async-search-controller');
uses()->group('fieldset');

beforeEach(function (): void {
    $this->itemResource = app(TestItemResource::class);
});

it('async search in form', function () {
    $item = createItem();
    $category = Category::factory()->create([
        'name' => 'test',
    ]);
    $item->categories()->attach($category);
    $item->refresh();
    $resource = app(TestCategoryResource::class);

    $field = Fieldset::make()->fields([
        BelongsToMany::make('Categories', resource: $resource)
            ->fillData($item),
    ]);

    addFieldsToTestResource($field);

    asAdmin()->get($this->moonshineCore->getRouter()->to("async-search", [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-resource',
        'resourceItem' => $item->id,
        '_component_name' => 'test-resource',
        '_relation' => 'categories',
        'query' => 'test',
    ]))
        ->assertOk()
        ->assertJson([
            [
                'value' => $category->getKey(),
                'label' => $category->name,
                'properties' => [
                    'image' => null,
                ],
            ],
        ])
    ;
});

it('forbids async search in form when update policy denies it', function () {
    $item = createItem();
    Category::factory()->create([
        'name' => 'denied-test',
    ]);

    $resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([
            BelongsToMany::make('Categories', resource: TestCategoryResource::class)
                ->asyncSearch(),
        ])
        ->setTestPolicy(true);

    MoonshineUser::query()->whereKey(1)->update([
        'name' => 'Policies test',
    ]);

    asAdmin()->get($this->moonshineCore->getRouter()->to("async-search", [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => $resource->getUriKey(),
        'resourceItem' => $item->id,
        '_component_name' => $resource->getUriKey(),
        '_relation' => 'categories',
        'query' => 'denied-test',
    ]))
        ->assertForbidden();
});

it('async search in form with fieldset', function () {
    $item = createItem();
    $category = Category::factory()->create([
        'name' => 'test',
    ]);
    $item->categories()->attach($category);
    $item->refresh();
    $resource = app(TestCategoryResource::class);

    $field = Fieldset::make('Fieldset', [
        BelongsToMany::make('Categories', resource: $resource)
            ->fillData($item),
    ]);

    addFieldsToTestResource($field);

    asAdmin()->get($this->moonshineCore->getRouter()->to("async-search", [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-resource',
        'resourceItem' => $item->id,
        '_component_name' => 'test-resource',
        '_relation' => 'categories',
        'query' => 'test',
    ]))
        ->assertOk()
        ->assertJson([
            [
                'value' => $category->getKey(),
                'label' => $category->name,
                'properties' => [
                    'image' => null,
                ],
            ],
        ])
    ;
});

it('async search in index', function () {
    $name = 'test-index-find';

    $item = createItem();
    $category = Category::factory()->create([
        'name' => $name,
    ]);
    $item->categories()->attach($category);
    $item->refresh();

    $response = asAdmin()->get($this->moonshineCore->getRouter()->to("async-search", [
        'pageUri' => PageType::INDEX->value,
        'resourceUri' => $this->itemResource->getUriKey(),
        '_relation' => 'category',
        'query' => 'index-f',
    ]))
        ->assertOk()
        ->assertJsonIsArray()
        ->assertJsonCount(1)
        ->content()
    ;

    $result = json_decode($response, true);

    expect($result[0])
        ->toBeArray()
        ->and($result[0]['label'])
        ->not()->toBeNull()
        ->and($result[0]['label'])
        ->toBe($name)
    ;
});

it('rejects morph to async search classes outside configured types', function () {
    $item = Item::factory()->create([
        'name' => 'morph-allowed',
    ]);

    $image = ImageModel::create([
        'imageable_id' => $item->getKey(),
        'imageable_type' => Item::class,
    ]);

    $resource = app(TestImageResource::class);

    asAdmin()->get($this->moonshineCore->getRouter()->to("async-search", [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => $resource->getUriKey(),
        'resourceItem' => $image->getKey(),
        '_component_name' => $resource->getUriKey(),
        '_relation' => 'imageable',
        'imageable_type' => Item::class,
        'query' => 'morph-allowed',
    ]))
        ->assertOk()
        ->assertJson([
            [
                'value' => $item->getKey(),
                'label' => $item->name,
                'properties' => [
                    'image' => null,
                ],
            ],
        ]);

    asAdmin()->get($this->moonshineCore->getRouter()->to("async-search", [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => $resource->getUriKey(),
        'resourceItem' => $image->getKey(),
        '_component_name' => $resource->getUriKey(),
        '_relation' => 'imageable',
        'imageable_type' => MoonshineUser::class,
        'query' => 'admin',
    ]))
        ->assertOk()
        ->assertExactJson([]);
});
