<?php

declare(strict_types=1);

use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Pages\Custom\CustomPageDetail;
use MoonShine\Tests\Fixtures\Pages\Custom\CustomPageForm;
use MoonShine\Tests\Fixtures\Pages\Custom\CustomPageIndex;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('resources');

it('to page url', function () {
    $this->resource = TestResourceBuilder::new(Item::class)
        ->setTestPages([
            CustomPageIndex::class,
            CustomPageForm::class,
            CustomPageDetail::class,
        ])
    ;

    $url = $this->resource->getPageUrl(CustomPageForm::class, params: ['resourceItem' => 1]);
    expect($url)
        ->toContain('/admin/test-resource/custom-page-form/1')
        ->and(
            $this->resource->getPageUrl(
                CustomPageIndex::class,
                params: ['resourceItem' => 1]
            )
        )
        ->toContain('/admin/test-resource/custom-page-index/1')
        ->and(
            $this->resource->getPageUrl(
                CustomPageDetail::class,
                params: ['resourceItem' => 1]
            )
        )
        ->toContain('/admin/test-resource/custom-page-detail/1')
    ;

    $urlFromHelper = toPage(CustomPageForm::class, $this->resource, params: ['resourceItem' => 1]);
    expect($urlFromHelper)
        ->toContain('/admin/test-resource/custom-page-form/1')
        ->toEqual($url)
    ;
});
