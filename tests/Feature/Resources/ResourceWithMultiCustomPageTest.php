<?php

declare(strict_types=1);

use MoonShine\Tests\Fixtures\Factories\CategoryFactory;
use MoonShine\Tests\Fixtures\Factories\CoverFactory;
use MoonShine\Tests\Fixtures\Resources\WithCustomPages\TestCategoryPageResource;

uses()->group('resources-feature');
uses()->group('multi-custom-pages');

beforeEach(function (): void {
    $this->category = CategoryFactory::new()
        ->has(
            CoverFactory::new()->count(1)
        )
        ->count(1)
        ->create()
        ->first();

    $this->resource = app(TestCategoryPageResource::class);
});

it('index page', function () {
    asAdmin()->get(
        toPage(page: 'category-page-index', resource: $this->resource)
    )
        ->assertOk()
        ->assertSee($this->category->name)
    ;
});

it('form add page', function () {
    asAdmin()->get(
        toPage(page: 'category-page-form', resource: $this->resource)
    )
        ->assertOk()
    ;
});

it('form edit page', function () {
    asAdmin()->get(
        toPage(page: 'category-page-form', resource: $this->resource, params: ['resourceItem' => $this->category->id])
    )
        ->assertOk()
        ->assertSee($this->category->name)
        ->assertSee($this->category->cover->image)
    ;
});

it('detail page', function () {
    asAdmin()->get(
        toPage(page: 'category-page-detail', resource: $this->resource, params: ['resourceItem' => $this->category->id])
    )
        ->assertOk()
        ->assertSee($this->category->name)
        ->assertSee($this->category->cover->image)
    ;
});
