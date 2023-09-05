<?php

use MoonShine\MoonShineRouter;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;

uses()->group('relation-controller');

beforeEach(function (): void {
    $this->itemResource = new TestItemResource();
});

it('search relations with pagination', function () {

    $item = createItem();

    $lastComment = $item->comments[count($item->comments) - 1];
    $firstComment= $item->comments[0];

    asAdmin()->get(MoonShineRouter::to("relation.search-relations", [
        'pageUri' => 'form-page',
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
    ]))
        ->assertOk()
        ->assertSee('page=1')
        ->assertSee('page=2')
        ->assertSee('asyncQuery')
        ->assertSee('pagination-list')
        ->assertSee(__('moonshine::ui.search'))
        ->assertSee($lastComment->content)
        ->assertDontSee($firstComment->content)
        ->assertDontSee('page=3')
    ;
});

it('pagination has many with page', function () {

    $item = createItem(countComments: 6);

    $comment = $item->comments[3];

    asAdmin()->get(MoonShineRouter::to("relation.search-relations", [
        'pageUri' => 'form-page',
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
        'page' => 2
    ]))
        ->assertOk()
        ->assertSee('asyncQuery')
        ->assertSee('page=1')
        ->assertSee('page=2')
        ->assertSee('page=3')
        ->assertSee($comment->content)
        ->assertSee('pagination-list')
        ->assertSee(__('moonshine::ui.search'))
    ;
});

it('pagination has many sort', function () {
    $item = createItem();

    $lastComment = $item->comments[count($item->comments) - 1];
    $firstComment= $item->comments[0];

    asAdmin()->get(MoonShineRouter::to("relation.search-relations", [
        'pageUri' => 'form-page',
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
        'sort' => [
            'column' => 'id',
            'direction' => 'asc',
        ]
    ]))
        ->assertOk()
        ->assertSee('asyncQuery')
        ->assertSee('page=1')
        ->assertSee('page=2')
        ->assertSee('pagination-list')
        ->assertDontSee($lastComment->content)
        ->assertSee($firstComment->content)
        ->assertSee(__('moonshine::ui.search'))
    ;
});

it('search relations empty result', function () {

    $item = createItem(countComments: 1);

    asAdmin()->get(MoonShineRouter::to("relation.search-relations", [
        'pageUri' => 'form-page',
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
        'search' => 'test_with_time_'.time(),
    ]))
        ->assertOk()
        ->assertSee('asyncQuery')
        ->assertSee('Records not found')
    ;
});