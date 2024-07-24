<?php

declare(strict_types=1);

use MoonShine\Tests\Fixtures\Pages\CategoryResource\CategoryPageIndex;
use MoonShine\Tests\Fixtures\Resources\TestImageResource;
use Symfony\Component\HttpFoundation\RedirectResponse;

uses()->group('core');
uses()->group('router');

beforeEach(function () {
    $this->page = $this->moonshineCore->getContainer(CategoryPageIndex::class);
    $this->resource = $this->moonshineCore->getContainer(TestImageResource::class);
});

it('default name', function (): void {
    $this->moonshineCore->getRouter()->withName('async.component');

    expect($this->moonshineCore->getRouter()->getName())
        ->toBe('moonshine.async.component')
    ;

    $this->moonshineCore->getRouter()->withName('async');

    expect($this->moonshineCore->getRouter()->getName('component'))
        ->toBe('moonshine.async.component')
    ;
});

it('default params', function (): void {
    $this->moonshineCore->getRouter()->withParams([
        'foo' => 'var',
    ]);

    expect($this->moonshineCore->getRouter()->getParams())
        ->toBe([
            'foo' => 'var',
        ])
        ->and($this->moonshineCore->getRouter()->getParams(['var' => 'bar']))
        ->toBe([
            'foo' => 'var',
            'var' => 'bar',
        ])
        ->and($this->moonshineCore->getRouter()->getParams(['empty' => '']))
        ->toBe([
            'foo' => 'var',
        ])
        ->and($this->moonshineCore->getRouter()->getParam('foo'))
        ->toBe('var')
        ->and($this->moonshineCore->getRouter()->getParam('empty'))
        ->toBeNull()
        ->and($this->moonshineCore->getRouter()->getParam('empty', 'empty'))
        ->toBe('empty')
        ->and($this->moonshineCore->getRouter()->forgetParam('foo')->getParams())
        ->toBeEmpty()
    ;
});

it('default sugar params', function (): void {
    $this->moonshineCore->getRouter()->withPage($this->page);

    expect($this->moonshineCore->getRouter()->getParams())
        ->toBe(['pageUri' => $this->page->getUriKey()]);

    $this->moonshineCore->getRouter()->withResource($this->resource);

    expect($this->moonshineCore->getRouter()->getParams())
        ->toBe(['resourceUri' => $this->resource->getUriKey(), 'pageUri' => $this->page->getUriKey()]);

    $this->moonshineCore->getRouter()->withResourceItem(3);

    expect($this->moonshineCore->getRouter()->getParams())
        ->toBe(['resourceItem' => 3, 'resourceUri' => $this->resource->getUriKey(), 'pageUri' => $this->page->getUriKey()])
        ->and($this->moonshineCore->getRouter()->getParams(['new' => 'new']))
        ->toBe(['resourceItem' => 3, 'resourceUri' => $this->resource->getUriKey(), 'pageUri' => $this->page->getUriKey(), 'new' => 'new', ]);
});

it('default to', function (): void {
    expect($this->moonshineCore->getRouter()->to('index', ['foo' => 'bar']))
        ->toContain('/admin?foo=bar')
        ->and($this->moonshineCore->getRouter()->to('index', ['var' => 'bar']))
        ->toContain('/admin?var=bar')
        ->and($this->moonshineCore->getRouter()->to('index'))
        ->toContain('/admin')
    ;

    $this->moonshineCore->getRouter()
        ->withParams(['foo' => 'bar'])
        ->withName('index');

    expect($this->moonshineCore->getRouter()->to())
        ->toContain('/admin?foo=bar')
    ;
});

it('default async method', function (): void {
    expect($this->moonshineCore->getRouter()->getEndpoints()->asyncMethod('someMethod', page: $this->page))
        ->toContain("/admin/async/method/{$this->page->getUriKey()}?method=someMethod")
    ;

    $this->get($this->page->getUrl());

    expect($this->moonshineCore->getRouter()->getEndpoints()->asyncMethod('someMethod'))
        ->toContain("/admin/async/method/{$this->page->getUriKey()}?method=someMethod")
    ;
});

it('default reactive', function (): void {
    expect($this->moonshineCore->getRouter()->getEndpoints()->reactive(page: $this->page, resource: $this->resource, extra: ['key' => 3]))
        ->toContain("/admin/async/reactive/{$this->page->getUriKey()}/{$this->resource->getUriKey()}/3")
    ;
});

it('default async component', function (): void {
    $this->get($this->page->getUrl());

    expect($this->moonshineCore->getRouter()->getEndpoints()->asyncComponent('index-table'))
        ->toContain("/admin/async/component/{$this->page->getUriKey()}?_component_name=index-table")
    ;
});

it('default update column', function (): void {
    expect($this->moonshineCore->getRouter()->getEndpoints()->updateColumn($this->resource, $this->page, extra: [
        'resourceItem' => 3,
    ]))
        ->toContain("/admin/column/resource/{$this->resource->getUriKey()}/3?pageUri={$this->page->getUriKey()}")
        ->and($this->moonshineCore->getRouter()->getEndpoints()->updateColumn($this->resource, $this->page, extra: [
            'resourceItem' => 3,
            'relation' => 'relation-name',
        ]))
        ->toContain("/admin/column/relation/{$this->resource->getUriKey()}/{$this->page->getUriKey()}/3")
    ;
});


it('default to relation', function (): void {
    expect($this->moonshineCore->getRouter()->getEndpoints()->toRelation('search', pageUri: $this->page->getUriKey()))
        ->toContain("/admin/relation/{$this->page->getUriKey()}")
        ->and($this->moonshineCore->getRouter()->getEndpoints()->toRelation('search-relations', pageUri: $this->page->getUriKey()))
        ->toContain("/admin/relations/{$this->page->getUriKey()}")
    ;
});

it('default to page', function (): void {
    expect($this->moonshineCore->getRouter()->getEndpoints()->toPage($this->page))
        ->toContain("/admin/page/{$this->page->getUriKey()}")
        ->and($this->moonshineCore->getRouter()->getEndpoints()->toPage($this->page, extra: ['fragment' => 'index-table']))
        ->toContain("/admin/page/{$this->page->getUriKey()}?_fragment-load=index-table")
        ->and($this->moonshineCore->getRouter()->getEndpoints()->toPage($this->page, extra: ['redirect' => true]))
        ->toBeInstanceOf(RedirectResponse::class)
    ;
});

it('home', function (): void {
    expect($this->moonshineCore->getRouter()->getEndpoints()->home())
        ->toContain("/admin")
    ;
});

it('uri key', function (): void {
    expect($this->page->getUriKey())
        ->toBe("category-page-index")
    ;
});

it('to string', function (): void {
    $this->moonshineCore->getRouter()->withName('index');

    expect((string) $this->moonshineCore->getRouter())
        ->toContain("/admin")
    ;
});
