<?php

declare(strict_types=1);

use MoonShine\Core\MoonShineRouter;
use MoonShine\Tests\Fixtures\Pages\CategoryResource\CategoryPageIndex;
use MoonShine\Tests\Fixtures\Resources\TestImageResource;
use Symfony\Component\HttpFoundation\RedirectResponse;

uses()->group('core');

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
        ->toBe(['pageUri' => $this->page->uriKey()]);

    $this->router->withResource($this->resource);

    expect($this->router->getParams())
        ->toBe(['resourceUri' => $this->resource->uriKey(), 'pageUri' => $this->page->uriKey()]);

    $this->router->withResourceItem(3);

    expect($this->router->getParams())
        ->toBe(['resourceItem' => 3, 'resourceUri' => $this->resource->uriKey(), 'pageUri' => $this->page->uriKey()])
        ->and($this->router->getParams(['new' => 'new']))
        ->toBe(['resourceItem' => 3, 'resourceUri' => $this->resource->uriKey(), 'pageUri' => $this->page->uriKey(), 'new' => 'new', ]);
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
    expect($this->router->asyncMethod('someMethod', page: $this->page))
        ->toContain("/admin/async/method/{$this->page->uriKey()}?method=someMethod")
    ;

    $this->get($this->page->url());

    expect($this->router->asyncMethod('someMethod'))
        ->toContain("/admin/async/method/{$this->page->uriKey()}?method=someMethod")
    ;
});

it('default reactive', function (): void {
    expect($this->router->reactive(3, page: $this->page, resource: $this->resource))
        ->toContain("/admin/async/reactive/{$this->page->uriKey()}/{$this->resource->uriKey()}/3")
    ;
});

it('default async component', function (): void {
    $this->get($this->page->url());

    expect($this->router->asyncComponent('index-table'))
        ->toContain("/admin/async/component/{$this->page->uriKey()}?_component_name=index-table")
    ;
});

it('default update column', function (): void {
    expect($this->router->updateColumn($this->resource->uriKey(), $this->page->uriKey(), 3))
        ->toContain("/admin/column/resource/{$this->resource->uriKey()}/3?pageUri={$this->page->uriKey()}")
        ->and($this->router->updateColumn($this->resource->uriKey(), $this->page->uriKey(), 3, 'relation-name'))
        ->toContain("/admin/column/relation/{$this->resource->uriKey()}/{$this->page->uriKey()}/3")
    ;
});


it('default to relation', function (): void {
    expect($this->router->toRelation('search', pageUri: $this->page->uriKey()))
        ->toContain("/admin/relation/{$this->page->uriKey()}")
        ->and($this->router->toRelation('search-relations', pageUri: $this->page->uriKey()))
        ->toContain("/admin/relations/{$this->page->uriKey()}")
    ;
});

it('default to page', function (): void {
    expect($this->router->toPage($this->page))
        ->toContain("/admin/page/{$this->page->uriKey()}")
        ->and($this->router->toPage($this->page, fragment: 'index-table'))
        ->toContain("/admin/page/{$this->page->uriKey()}?_fragment-load=index-table")
        ->and($this->router->toPage($this->page, redirect: true))
        ->toBeInstanceOf(RedirectResponse::class)
    ;
});

it('home', function (): void {
    expect($this->router->home())
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
