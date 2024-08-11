<?php

declare(strict_types=1);

use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Support\Enums\PageType;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;
use MoonShine\UI\Fields\StackFields;

uses()->group('has-many-controller');

beforeEach(function (): void {
    $this->itemResource = app(TestItemResource::class);
});

it('search with pagination', function () {
    $item = createItem();

    $lastComment = $item->comments[count($item->comments) - 1];
    $firstComment = $item->comments[0];

    asAdmin()->get($this->moonshineCore->getRouter()->to('has-many.list', [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
    ]))
        ->assertOk()
        ->assertSee('page=1')
        ->assertSee('page=2')
        ->assertSee('asyncRequest')
        ->assertSee('pagination-list')
        ->assertSee(__('moonshine::ui.search'))
        ->assertSee($lastComment->content)
        ->assertDontSee($firstComment->content)
        ->assertDontSee('page=3')
    ;
});

it('pagination with page', function () {
    $item = createItem(countComments: 6);

    $comment = $item->comments[3];

    asAdmin()->get($this->moonshineCore->getRouter()->to("has-many.list", [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
        'page' => 2,
    ]))
        ->assertOk()
        ->assertSee('asyncRequest')
        ->assertSee('page=1')
        ->assertSee('page=2')
        ->assertSee('page=3')
        ->assertSee($comment->content)
        ->assertSee('pagination-list')
        ->assertSee(__('moonshine::ui.search'))
    ;
});

it('pagination sort', function () {
    $item = createItem();

    $lastComment = $item->comments[count($item->comments) - 1];
    $firstComment = $item->comments[0];

    asAdmin()->get($this->moonshineCore->getRouter()->to("has-many.list", [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
        'sort' => 'id',
    ]))
        ->assertOk()
        ->assertSee('asyncRequest')
        ->assertSee('page=1')
        ->assertSee('page=2')
        ->assertSee('pagination-list')
        ->assertDontSee($lastComment->content)
        ->assertSee($firstComment->content)
        ->assertSee(__('moonshine::ui.search'))
    ;
});

it('search empty result', function () {

    $item = createItem(countComments: 1);

    asAdmin()->get($this->moonshineCore->getRouter()->to('has-many.list', [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
        'search' => 'test_with_time_' . time(),
    ]))
        ->assertOk()
        ->assertSee('asyncRequest')
        ->assertSee('Records not found')
    ;
});

it('get form component', function () {

    $item = createItem(countComments: 1);

    asAdmin()->get($this->moonshineCore->getRouter()->to('has-many.form', [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->id,
        '_relation' => 'comments',
    ]))
        ->assertOk()
        ->assertSee('form')
    ;
});
