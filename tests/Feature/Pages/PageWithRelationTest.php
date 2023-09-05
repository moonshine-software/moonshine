<?php

use MoonShine\Pages\Crud\FormPage;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;

uses()->group('pages-feature');
uses()->group('pages-relation-feature');

beforeEach(function (): void {
    $this->categoryResource = new TestCategoryResource();
    $this->itemResource = new TestItemResource();
});

it('has one dont see', function () {
    asAdmin()->get(
        to_page($this->categoryResource, FormPage::class)
    )
        ->assertSee('Name title')
        ->assertSee('Content title')
        ->assertSee('Public at title')
        ->assertDontSee('Cover title')
        ->assertOk()
    ;
});

it('has one see', function () {
    $category = Category::factory()->count(1)->create()->first();

    asAdmin()->get(
        to_page($this->categoryResource, FormPage::class, ['resourceItem' => $category->id])
    )
        ->assertSee('Name title')
        ->assertSee('Content title')
        ->assertSee('Public at title')
        ->assertSee('Cover title')
        ->assertOk()
    ;
});

it('has many dont see', function () {

    asAdmin()->get(
        to_page($this->itemResource, FormPage::class)
    )
        ->assertSee('Name title')
        ->assertSee('Category title')
        ->assertSee('Content title')
        ->assertDontSee('Comments title')
        ->assertDontSee('Images title')
        ->assertOk()
    ;
});

it('has many see', function () {

    $item = createItem();

    $lastComment = $item->comments[count($item->comments) - 1];
    $firstComment = $item->comments[0];

    asAdmin()->get(
        to_page($this->itemResource, FormPage::class, ['resourceItem' => $item->id])
    )
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
        ->assertOk()
    ;
});
