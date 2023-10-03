<?php

declare(strict_types=1);

use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Pages\Custom\CustomPageDetail;
use MoonShine\Tests\Fixtures\Pages\Custom\CustomPageForm;
use MoonShine\Tests\Fixtures\Pages\Custom\CustomPageIndex;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('pages-feature');
uses()->group('pages-custom');

beforeEach(function (): void {
    $this->resource = TestResourceBuilder::new(Item::class)
        ->setTestPages([
            CustomPageIndex::make('CustomPageIndex'),
            CustomPageForm::make('CustomPageForm'),
            CustomPageDetail::make('CustomPageDetail'),
        ])
        ->setTestFields([
            ID::make()->sortable()->useOnImport()->showOnExport(),
            Text::make('Name title', 'name')->sortable(),
        ])
    ;
});

it('index page', function () {
    asAdmin()->get(
        to_page(page: IndexPage::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('CustomPageIndex')
        ->assertSee('To Form')
        ->assertSee('To Detail')
    ;
});

it('form page', function () {
    asAdmin()->get(
        to_page(page: FormPage::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('CustomPageForm')
    ;
});

it('detail page', function () {
    asAdmin()->get(
        to_page(page: DetailPage::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('CustomPageDetail')
    ;
});
