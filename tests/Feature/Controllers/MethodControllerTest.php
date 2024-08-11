<?php

declare(strict_types=1);

uses()->group('method-controller');

it('get response', function () {
    asAdmin()->get($this->moonshineCore->getRouter()->to('method', [
        'method' => 'testAsyncMethod',
        'resourceUri' => 'test-item-resource',
        'pageUri' => 'index-page',
        'var' => 'foo',
    ]))
        ->assertJson(['var' => 'foo'])
        ->assertOk()
    ;
});
