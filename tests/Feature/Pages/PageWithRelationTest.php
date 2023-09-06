<?php

use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Factories\CategoryFactory;
use MoonShine\Tests\Fixtures\Factories\CoverFactory;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;

uses()->group('pages-feature');
uses()->group('pages-relation-feature');

beforeEach(function (): void {
    $this->categoryResource = new TestCategoryResource();
    $this->itemResource = new TestItemResource();
});

it('has one on index', function () {
    $category = CategoryFactory::new()
        ->has(
            CoverFactory::new()->count(1)
        )
        ->count(1)
        ->create()
        ->first();

    asAdmin()->get(
        to_page($this->categoryResource, IndexPage::class)
    )
        ->assertOk()
        ->assertSee($category->name)
    ;
});

it('empty has one on index', function () {
    $category = Category::factory()
        ->count(1)
        ->create()
        ->first();

    asAdmin()->get(
        to_page($this->categoryResource, IndexPage::class)
    )
        ->assertOk()
        ->assertSee($category->name)
    ;
});

it('has one dont see', function () {
    asAdmin()->get(
        to_page($this->categoryResource, FormPage::class)
    )
        ->assertOk()
        ->assertSee('Name title')
        ->assertSee('Content title')
        ->assertSee('Public at title')
        ->assertDontSee('Cover title')
    ;
});

it('has one see', function () {
    $category = Category::factory()->count(1)->create()->first();

    asAdmin()->get(
        to_page($this->categoryResource, FormPage::class, ['resourceItem' => $category->id])
    )
        ->assertOk()
        ->assertSee('Name title')
        ->assertSee('Content title')
        ->assertSee('Public at title')
        ->assertSee('Cover title')
    ;
});

it('has many on index', function () {

    $items = createItem(3);

    asAdmin()->get(
        to_page($this->itemResource, IndexPage::class)
    )
        ->assertOk()
        ->assertSee('Name title')
        ->assertSee('Category title')
        ->assertSee($items->first()->name)
        ->assertSee($items->first()->comments[0]->content)
        ->assertSee($items->first()->comments[1]->content)
        ->assertSee($items->first()->comments[2]->content)
    ;
});

it('empty has many on index', function () {

    $items = createItem(3, 0);

    asAdmin()->get(
        to_page($this->itemResource, IndexPage::class)
    )
        ->assertOk()
        ->assertSee('Name title')
        ->assertSee('Category title')
        ->assertSee($items->first()->name)
    ;
});

it('has many dont see', function () {

    asAdmin()->get(
        to_page($this->itemResource, FormPage::class)
    )
        ->assertOk()
        ->assertSee('Name title')
        ->assertSee('Category title')
        ->assertSee('Content title')
        ->assertDontSee('Comments title')
        ->assertDontSee('Images title')
    ;
});

it('has many see', function () {

    $item = createItem();

    $lastComment = $item->comments[count($item->comments) - 1];
    $firstComment = $item->comments[0];

    asAdmin()->get(
        to_page($this->itemResource, FormPage::class, ['resourceItem' => $item->id])
    )
        ->assertOk()
        ->assertSee('Name title')
        ->assertSee('Category title')
        ->assertSee('Content title')
        ->assertSee('Comments title')
        ->assertSee('Images title')
        ->assertSee('page=1')
        ->assertSee('page=2')
        ->assertSee('asyncRequest')
        ->assertSee('pagination-list')
        ->assertSee($lastComment->content)
        ->assertDontSee($firstComment->content)
    ;
});
