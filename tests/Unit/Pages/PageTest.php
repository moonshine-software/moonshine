<?php

declare(strict_types=1);

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\PageType;
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
                    $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: IndexPage::class, resource: $this->resource)
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

    $url = $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $this->resource, params: ['resourceItem' => 1]);

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
        ->parameter('resourceItem')
        ->toBe('1')
    ;
});

it('to page show', function () {

    $url = $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: DetailPage::class, resource: $this->resource, params: ['resourceItem' => 1]);

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
        ->parameter('resourceItem')
        ->toBe('1')
    ;
});
