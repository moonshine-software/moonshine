<?php

declare(strict_types=1);

uses()->group('pages-feature');

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Factories\CategoryFactory;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;

beforeEach(function () {
    CategoryFactory::new()->count(3)->create();

    $this->resource = app(TestItemResource::class);
});

it('filters', function () {
    $item = createItem(5, 2);

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(
            page: IndexPage::class,
            resource: $this->resource,
            params: [
                'filters' => [
                    'name' => $item->name,
                    'category_id' => $item->category_id,
                ],
            ]
        )
    )
        ->assertSee($item->name)
        ->assertOk()
    ;
});

it('query tags', function () {
    $item = createItem(3, 0);

    $item->category_id = Category::query()->max('id');

    $item->save();

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(
            page: IndexPage::class,
            resource: $this->resource,
            params: [
                'query-tag' => 'item-1-query-tag',
            ]
        )
    )
        ->assertOk()
        ->assertSee($item->name)
    ;
});
