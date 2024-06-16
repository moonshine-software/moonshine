<?php

declare(strict_types=1);

use MoonShine\Core\MoonShineRouter;
use MoonShine\Tests\Fixtures\Pages\CategoryResource\CategoryPageIndex;
use MoonShine\Tests\Fixtures\Resources\TestImageResource;
use Symfony\Component\HttpFoundation\RedirectResponse;

uses()->group('core');
uses()->group('router');

beforeEach(function () {
    $this->page = app(CategoryPageIndex::class);
    $this->resource = app(TestImageResource::class);
});

it('default name', function (): void {
    $this->router->withName('async.component');

    expect($this->router->getName())
        ->toBe('moonshine.async.component')
    ;

    $this->router->withName('async');

    expect($this->router->getName('component'))
        ->toBe('moonshine.async.component')
    ;
});

it('default params', function (): void {
    $this->router->withParams([
        'foo' => 'var',
    ]);

    expect($this->router->getParams())
        ->toBe([
            'foo' => 'var',
        ])
        ->and($this->router->getParams(['var' => 'bar']))
        ->toBe([
            'foo' => 'var',
            'var' => 'bar',
        ])
        ->and($this->router->getParams(['empty' => '']))
        ->toBe([
            'foo' => 'var',
        ])
        ->and($this->router->getParam('foo'))
        ->toBe('var')
        ->and($this->router->getParam('empty'))
        ->toBeNull()
        ->and($this->router->getParam('empty', 'empty'))
        ->toBe('empty')
        ->and($this->router->forgetParam('foo')->getParams())
        ->toBeEmpty()
    ;
});

it('default sugar params', function (): void {
    $this->router->withPage($this->page);

    expect($this->router->getParams())
        ->toBe(['pageUri' => $this->page->getUriKey()]);

    $this->router->withResource($this->resource);

    expect($this->router->getParams())
        ->toBe(['resourceUri' => $this->resource->getUriKey(), 'pageUri' => $this->page->getUriKey()]);

    $this->router->withResourceItem(3);

    expect($this->router->getParams())
        ->toBe(['resourceItem' => 3, 'resourceUri' => $this->resource->getUriKey(), 'pageUri' => $this->page->getUriKey()])
        ->and($this->router->getParams(['new' => 'new']))
        ->toBe(['resourceItem' => 3, 'resourceUri' => $this->resource->getUriKey(), 'pageUri' => $this->page->getUriKey(), 'new' => 'new', ]);
});

it('default to', function (): void {
    expect($this->router->to('index', ['foo' => 'bar']))
        ->toContain('/admin?foo=bar')
        ->and($this->router->to('index', ['var' => 'bar']))
        ->toContain('/admin?var=bar')
        ->and($this->router->to('index'))
        ->toContain('/admin')
    ;

    $this->router
        ->withParams(['foo' => 'bar'])
        ->withName('index');

    expect($this->router->to())
        ->toContain('/admin?foo=bar')
    ;
});

it('default async method', function (): void {
    expect($this->router->getEndpoints()->asyncMethod('someMethod', page: $this->page))
        ->toContain("/admin/async/method/{$this->page->getUriKey()}?method=someMethod")
    ;

    $this->get($this->page->getUrl());

    expect($this->router->getEndpoints()->asyncMethod('someMethod'))
        ->toContain("/admin/async/method/{$this->page->getUriKey()}?method=someMethod")
    ;
});

it('default reactive', function (): void {
    expect($this->router->getEndpoints()->reactive(page: $this->page, resource: $this->resource, extra: ['key' => 3]))
        ->toContain("/admin/async/reactive/{$this->page->getUriKey()}/{$this->resource->getUriKey()}/3")
    ;
});

it('default async component', function (): void {
    $this->get($this->page->getUrl());

    expect($this->router->getEndpoints()->asyncComponent('index-table'))
        ->toContain("/admin/async/component/{$this->page->getUriKey()}?_component_name=index-table")
    ;
});

it('default update column', function (): void {
    expect($this->router->getEndpoints()->updateColumn($this->resource, $this->page, extra: [
        'resourceItem' => 3,
    ]))
        ->toContain("/admin/column/resource/{$this->resource->getUriKey()}/3?pageUri={$this->page->getUriKey()}")
        ->and($this->router->getEndpoints()->updateColumn($this->resource, $this->page, extra: [
            'resourceItem' => 3,
            'relation' => 'relation-name',
        ]))
        ->toContain("/admin/column/relation/{$this->resource->getUriKey()}/{$this->page->getUriKey()}/3")
    ;
});


it('default to relation', function (): void {
    expect($this->router->getEndpoints()->toRelation('search', pageUri: $this->page->getUriKey()))
        ->toContain("/admin/relation/{$this->page->getUriKey()}")
        ->and($this->router->getEndpoints()->toRelation('search-relations', pageUri: $this->page->getUriKey()))
        ->toContain("/admin/relations/{$this->page->getUriKey()}")
    ;
});

it('default to page', function (): void {
    expect($this->router->getEndpoints()->toPage($this->page))
        ->toContain("/admin/page/{$this->page->getUriKey()}")
        ->and($this->router->getEndpoints()->toPage($this->page, extra: ['fragment' => 'index-table']))
        ->toContain("/admin/page/{$this->page->getUriKey()}?_fragment-load=index-table")
        ->and($this->router->getEndpoints()->toPage($this->page, extra: ['redirect' => true]))
        ->toBeInstanceOf(RedirectResponse::class)
    ;
});

it('home', function (): void {
    expect($this->router->getEndpoints()->home())
        ->toContain("/admin")
    ;
});

it('uri key', function (): void {
    expect(MoonShineRouter::uriKey($this->page::class))
        ->toBe("category-page-index")
    ;
});

it('to string', function (): void {
    $this->router->withName('index');

    expect((string) $this->router)
        ->toContain("/admin")
    ;
});
