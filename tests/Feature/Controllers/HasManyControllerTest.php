<?php

declare(strict_types=1);

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\RelationRepeater;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Support\Enums\PageType;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

uses()->group('has-many-controller');

beforeEach(function (): void {
    $this->itemResource = app(TestItemResource::class);
});

it('search with pagination', function () {
    $item = createItem();

    $lastComment = $item->comments[\count($item->comments) - 1];
    $firstComment = $item->comments[0];

    asAdmin()->get($this->moonshineCore->getRouter()->to('has-many.list', [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
    ]))
        ->assertOk()
        ->assertSee('page=1')
        ->assertSee('page=2')
        ->assertSee('asyncRequest')
        ->assertSee('pagination-list')
        ->assertSee(__('moonshine::ui.search'))
        ->assertSee($lastComment->content)
        ->assertDontSee($firstComment->content)
        ->assertDontSee('page=3')
    ;
});

it('pagination with page', function () {
    $item = createItem(countComments: 6);

    $comment = $item->comments[3];

    asAdmin()->get($this->moonshineCore->getRouter()->to("has-many.list", [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
        'page' => 2,
    ]))
        ->assertOk()
        ->assertSee('asyncRequest')
        ->assertSee('page=1')
        ->assertSee('page=2')
        ->assertSee('page=3')
        ->assertSee($comment->content)
        ->assertSee('pagination-list')
        ->assertSee(__('moonshine::ui.search'))
    ;
});

it('pagination sort', function () {
    $item = createItem();

    $lastComment = $item->comments[\count($item->comments) - 1];
    $firstComment = $item->comments[0];

    asAdmin()->get($this->moonshineCore->getRouter()->to("has-many.list", [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
        'sort' => 'id',
    ]))
        ->assertOk()
        ->assertSee('asyncRequest')
        ->assertSee('page=1')
        ->assertSee('page=2')
        ->assertSee('pagination-list')
        ->assertDontSee($lastComment->content)
        ->assertSee($firstComment->content)
        ->assertSee(__('moonshine::ui.search'))
    ;
});

it('search empty result', function () {

    $item = createItem(countComments: 1);

    asAdmin()->get($this->moonshineCore->getRouter()->to('has-many.list', [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
        'search' => 'test_with_time_' . time(),
    ]))
        ->assertOk()
        ->assertSee('asyncRequest')
        ->assertSee('Records not found')
    ;
});

it('get form component', function () {

    $item = createItem(countComments: 1);

    asAdmin()->get($this->moonshineCore->getRouter()->to('has-many.form', [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
    ]))
        ->assertOk()
        ->assertSee('form')
    ;
});

it('uses has many field when a relation repeater has the same relation', function (): void {
    $item = createItem(countComments: 1);

    $resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([
            RelationRepeater::make('Comments', 'comments', resource: TestCommentResource::class),
            HasMany::make('Comments List', 'comments', resource: TestCommentResource::class)
                ->creatable(),
        ]);

    asAdmin()->get($this->moonshineCore->getRouter()->to('has-many.form', [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => $resource->getUriKey(),
        'resourceItem' => $item->getKey(),
        '_relation' => 'comments',
    ]))
        ->assertOk()
        ->assertSee('form');
});

it('forbids relation data when update policy denies access to the parent resource', function (): void {
    $item = createItem(countComments: 1);

    $resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([
            ID::make(),
            HasMany::make('Comments', resource: TestCommentResource::class),
        ])
        ->setTestPolicy(true);

    MoonshineUser::query()->whereKey(1)->update([
        'name' => 'Policies test',
    ]);

    asAdmin()->get($this->moonshineCore->getRouter()->to('has-many.list', [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => $resource->getUriKey(),
        'resourceItem' => $item->getKey(),
        '_relation' => 'comments',
    ]))
        ->assertForbidden();
});

it('uses related resource context for nested relation fields in form component', function (): void {
    $category = Category::factory()->create();
    $item = Item::factory()->create([
        'category_id' => $category->getKey(),
    ]);

    $itemResource = (new TestResource(app(CoreContract::class)))
        ->setTestModel(Item::class)
        ->setTestUriKey('nested-item-resource')
        ->setTestFields([
            ID::make(),
            Text::make('Name', 'name'),
            BelongsToMany::make('Categories', resource: TestCategoryResource::class)
                ->fields([
                    Text::make('Pivot 1', 'pivot_1'),
                ])
                ->pivotModalMode()
                ->creatable(),
        ]);

    $categoryResource = (new TestResource(app(CoreContract::class)))
        ->setTestModel(Category::class)
        ->setTestUriKey('nested-category-resource')
        ->setTestFields([
            ID::make(),
            Text::make('Name', 'name'),
            HasMany::make('Items', 'items', resource: $itemResource)
                ->creatable(),
        ]);

    app(CoreContract::class)->resources([
        $categoryResource,
        $itemResource,
    ]);

    $expectedNestedPivotUrl = $this->moonshineCore->getRouter()->to('belongs-to-many-pivot.form', [
        'pageUri' => $itemResource->getFormPage()->getUriKey(),
        'resourceUri' => $itemResource->getUriKey(),
        'resourceItem' => $item->getKey(),
        '_relation' => 'categories',
    ]);

    asAdmin()->get($this->moonshineCore->getRouter()->to('has-many.form', [
        'pageUri' => $categoryResource->getFormPage()->getUriKey(),
        'resourceUri' => $categoryResource->getUriKey(),
        'resourceItem' => $category->getKey(),
        '_relation' => 'items',
        '_key' => $item->getKey(),
    ]))
        ->assertOk()
        ->assertSee($expectedNestedPivotUrl, false)
        ->assertDontSee("belongs-to-many-pivot/form/{$categoryResource->getFormPage()->getUriKey()}/{$categoryResource->getUriKey()}/{$category->getKey()}?_relation=categories", false)
    ;
});
