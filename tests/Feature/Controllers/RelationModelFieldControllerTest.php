<?php

declare(strict_types=1);

use MoonShine\Enums\PageType;
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Fields\StackFields;
use MoonShine\MoonShineRouter;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;

uses()->group('relation-controller');

beforeEach(function (): void {
    $this->itemResource = new TestItemResource();
});

it('search relations with pagination', function () {
    $item = createItem();

    $lastComment = $item->comments[count($item->comments) - 1];
    $firstComment = $item->comments[0];

    asAdmin()->get(MoonShineRouter::to("relation.search-relations", [
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

it('pagination has many with page', function () {

    $item = createItem(countComments: 6);

    $comment = $item->comments[3];

    asAdmin()->get(MoonShineRouter::to("relation.search-relations", [
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

it('pagination has many sort', function () {
    $item = createItem();

    $lastComment = $item->comments[count($item->comments) - 1];
    $firstComment = $item->comments[0];

    asAdmin()->get(MoonShineRouter::to("relation.search-relations", [
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

it('search relations empty result', function () {

    $item = createItem(countComments: 1);

    asAdmin()->get(MoonShineRouter::to("relation.search-relations", [
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

it('async search', function () {
    $item = createItem();
    $category = Category::factory()->create([
        'name' => 'test',
    ]);
    $item->categories()->attach($category);
    $item->refresh();

    $field = StackFields::make()->fields([
        BelongsToMany::make('Categories', resource: new TestCategoryResource())
            ->resolveFill($item->toArray(), $item),
    ]);

    addFieldsToTestResource($field);

    asAdmin()->get(MoonShineRouter::to("relation.search", [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-resource',
        'resourceItem' => $item->id,
        '_component_name' => 'crud',
        '_relation' => 'categories',
        'query' => 'test',
    ]))
        ->assertOk()
        ->assertJson([
            [
                'value' => $category->getKey(),
                'label' => $category->name,
                'customProperties' => [
                    'image' => null,
                ],
            ],
        ])
    ;
});
