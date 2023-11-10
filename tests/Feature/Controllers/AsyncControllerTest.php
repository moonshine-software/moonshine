<?php

declare(strict_types=1);

it('table', function () {
    $item = createItem(10, 5);

    asAdmin()->get(route('moonshine.async.table', [
        '_component_name' => 'index-table',
        'resourceUri' => 'test-item-resource',
        'pageUri' => 'index-page',
    ]))
        ->assertSee('Name title')
        ->assertSee('Category title')
        ->assertDontSee('Content title')
        ->assertDontSee('Public at title')
        ->assertSee('Comments title')
        ->assertSee($item->id)
        ->assertSee($item->name)
        ->assertOk()
    ;

})->only();
