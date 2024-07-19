<?php

declare(strict_types=1);

use MoonShine\Tests\Fixtures\Resources\TestItemResource;

uses()->group('fields');

beforeEach(function () {
    $this->item = createItem();
});

it('empty value save', function () {
    $resource = app(TestItemResource::class);

    $data = [
        'content' => '',
    ];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->content)
        ->toBeNull()
    ;
});
