<?php

declare(strict_types=1);

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Pages\Custom\CustomPageDetail;
use MoonShine\Tests\Fixtures\Pages\Custom\CustomPageForm;
use MoonShine\Tests\Fixtures\Pages\Custom\CustomPageIndex;
use MoonShine\Tests\Fixtures\Pages\NoType\CustomNoTypeForm;
use MoonShine\Tests\Fixtures\Pages\NoType\CustomNoTypeIndex;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

uses()->group('resources-feature');
uses()->group('pages-custom');

beforeEach(function (): void {
    $this->resource = TestResourceBuilder::new(Item::class)
        ->setTestPages([
            CustomPageIndex::make('CustomPageIndex'),
            CustomPageForm::make('CustomPageForm'),
            CustomPageDetail::make('CustomPageDetail'),
        ])
        ->setTestExportFields([
            ID::make(),
        ])
        ->setTestImportFields([
            ID::make(),
        ])
        ->setTestFields([
            ID::make()->sortable(),
            Text::make('Name title', 'name')->sortable(),
        ])
    ;
});

it('index page', function () {
    asAdmin()->get(
        toPage(page: IndexPage::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('CustomPageIndex')
        ->assertSee('To Form')
        ->assertSee('To Detail')
    ;
});

it('form page', function () {
    asAdmin()->get(
        toPage(page: FormPage::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('CustomPageForm')
    ;
});

it('detail page', function () {
    asAdmin()->get(
        toPage(page: DetailPage::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('CustomPageDetail')
    ;
});

it('no page type resource', static function () {
    $resource = TestResourceBuilder::new(Item::class)
        ->setTestPages([
            CustomNoTypeIndex::make('CustomNoTypeIndex', 'page-1'),
            CustomNoTypeForm::make('CustomNoTypeForm', 'page-2'),
        ])
    ;

    expect(toPage('page-2', resource: $resource))
        ->toContain('page-2');
});
