<?php

use MoonShine\Fields\ID;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Relationships\HasOne;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\CategoryImage;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('controllers');
uses()->group('relation-field-controllers');

beforeEach(function (): void {
    $this->imageResource = TestResourceBuilder::new()
        ->setTestModel(CategoryImage::class)
        ->setTestFields([
            ID::make(),
            Text::make('Name'),
        ])
        ->setTestUriKey('category-image-resource')
        ->addRoutes();

    $this->itemResource = TestResourceBuilder::new()
        ->setTestModel(Item::class)
        ->setTestFields([
            ID::make(),
            Text::make('Name'),
        ])
        ->setTestUriKey('item-resource')
        ->addRoutes();

    $this->item = Category::query()->create([
        'name' => 'Testing',
        'content' => 'Testing',
    ]);

    $this->resource = TestResourceBuilder::new()
        ->setTestModel(Category::class)
        ->setTestFields([
            HasOne::make('Image', resource: $this->imageResource)->resourceMode(),
            HasMany::make('Items', resource: $this->itemResource)->resourceMode(),
        ])
        ->setTestUriKey('category-resource')
        ->addRoutes();
});

function relationFieldItemsUrl(string $relation)
{
    return test()->resource->route('relation-field-items', test()->item->getKey(), query: [
        '_field_relation' => $relation,
    ]);
}

function relationFieldFormUrl(string $relation, string|int $key)
{
    return test()->resource->route('relation-field-form', test()->item->getKey(), query: [
        '_field_relation' => $relation,
        '_related_key' => $key,
    ]);
}

it('index has many successful response', function (): void {
    asAdmin()->get(relationFieldItemsUrl('items'))
        ->assertOk()
        ->assertViewIs($this->resource->itemsView())
        ->assertViewHas('resource', $this->itemResource)
        ->assertViewHas('resources', $this->itemResource->paginate())
        ->assertSee('Records not found');

    $itemWithCategory = Item::query()->create([
        'name' => 'Testing',
        'category_id' => $this->item->getKey(),
        'content' => 'Testing',
    ]);

    $itemWithoutCategory = Item::query()->create([
        'name' => 'Testing',
        'category_id' => null,
        'content' => 'Testing',
    ]);

    expect(asAdmin()->get(relationFieldItemsUrl('items')))
        ->assertSee($itemWithCategory->name)
        ->not->assertSee($itemWithoutCategory->name);
});

it('index has many alert response', function (): void {
    asAdmin()->get(relationFieldItemsUrl('test'))
        ->assertOk()
        ->assertSee('Field not found');
});

it('form has one successful response', function (): void {
    asAdmin()->get(relationFieldFormUrl('image', $this->item->getKey()))
        ->assertOk()
        ->assertViewIs($this->resource->formView())
        ->assertViewHas('resource', $this->imageResource)
        ->assertViewHas('item', $this->itemResource->getModel());
});

it('form has one alert response', function (): void {
    asAdmin()->get(relationFieldFormUrl('test', $this->item->getKey()))
        ->assertOk()
        ->assertSee('Field not found');
});
