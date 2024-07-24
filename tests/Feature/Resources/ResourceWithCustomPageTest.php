<?php

declare(strict_types=1);

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
            CustomPageIndex::class,
            CustomPageForm::class,
            CustomPageDetail::class,
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
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: CustomPageIndex::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('CustomPageIndex')
        ->assertSee('To Form')
        ->assertSee('To Detail')
    ;
});

it('form page', function () {
    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: CustomPageForm::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('CustomPageForm')
    ;
});

it('detail page', function () {
    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: CustomPageDetail::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('CustomPageDetail')
    ;
});

it('no page type resource', function () {
    $resource = TestResourceBuilder::new(Item::class)
        ->setTestPages([
            CustomNoTypeIndex::class,
            CustomNoTypeForm::class,
        ])
    ;

    expect($this->moonshineCore->getRouter()->getEndpoints()->toPage('custom-no-type-form', resource: $resource))
        ->toContain('custom-no-type-form');
});
