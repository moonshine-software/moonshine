<?php

declare(strict_types=1);

use MoonShine\Enums\PageType;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('pages');

beforeEach(function (): void {
    $this->resource = TestResourceBuilder::new();
});

it('page urls', function () {
    expect($this->resource->getPages())
        ->findByUri(PageType::INDEX->value)
        ->toBeInstanceOf(IndexPage::class)
        ->findByUri(PageType::FORM->value)
        ->toBeInstanceOf(FormPage::class)
        ->findByUri(PageType::DETAIL->value)
        ->toBeInstanceOf(DetailPage::class)
    ;
});

it('to page index', function () {
    expect(
        app('router')
            ->getRoutes()
            ->match(
                app('request')->create(
                    to_page(page: IndexPage::class, resource: $this->resource)
                )
            )
    )
        ->getName()
        ->toBe('moonshine.resource.page')
        ->hasParameter('resourceUri')
        ->toBeTrue()
        ->hasParameter('pageUri')
        ->toBeTrue()
        ->parameter('resourceUri')
        ->toBe('test-resource')
        ->parameter('pageUri')
        ->toBe(PageType::INDEX->value)
    ;
});

it('to page form', function () {

    $url = to_page(page: FormPage::class, resource: $this->resource, params: ['resourceItem' => 1]);

    expect(
        app('router')
            ->getRoutes()
            ->match(
                app('request')->create($url)
            )
    )
        ->getName()
        ->toBe('moonshine.resource.page')
        ->hasParameter('resourceUri')
        ->toBeTrue()
        ->hasParameter('pageUri')
        ->toBeTrue()
        ->parameter('resourceUri')
        ->toBe('test-resource')
        ->parameter('pageUri')
        ->toBe(PageType::FORM->value)
        ->and(parse_url($url))
        ->query
        ->toBe('resourceItem=1')
    ;
});

it('to page show', function () {

    $url = to_page(page: DetailPage::class, resource: $this->resource, params: ['resourceItem' => 1]);

    expect(
        app('router')
            ->getRoutes()
            ->match(
                app('request')->create($url)
            )
    )
        ->getName()
        ->toBe('moonshine.resource.page')
        ->hasParameter('resourceUri')
        ->toBeTrue()
        ->hasParameter('pageUri')
        ->toBeTrue()
        ->parameter('resourceUri')
        ->toBe('test-resource')
        ->parameter('pageUri')
        ->toBe(PageType::DETAIL->value)
        ->and(parse_url($url))
        ->query
        ->toBe('resourceItem=1')
    ;
});
