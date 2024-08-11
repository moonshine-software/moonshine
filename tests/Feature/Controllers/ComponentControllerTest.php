<?php

declare(strict_types=1);

uses()->group('crud-controller');

it('get component', function () {
    $item = createItem(3, 2);

    asAdmin()->get($this->moonshineCore->getRouter()->to('component', [
        '_component_name' => 'index-table-test-item-resource',
        'resourceUri' => 'test-item-resource',
        'pageUri' => 'index-page',
    ]))
        ->assertSee('Name title')
        ->assertSee('Category title')
        ->assertDontSee('Content title')
        ->assertDontSee('Public at title')
        ->assertSee('Comments title')
        ->assertSee('Images title')
        ->assertSee($item->id)
        ->assertSee($item->name)
        ->assertOk()
    ;
});
