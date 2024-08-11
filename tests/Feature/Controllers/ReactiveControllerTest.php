<?php

declare(strict_types=1);

uses()->group('reactive-controller');

it('get response', function () {
    asAdmin()->post($this->moonshineCore->getRouter()->to('reactive', [
        '_component_name' => 'test-item-resource',
        'resourceUri' => 'test-item-resource',
        'pageUri' => 'form-page',
        'values' => [
            'name' => 'new name',
        ],
    ]))
        ->assertJson(['values' => [
            'name' => 'new name',
        ]])
        ->assertOk()
    ;
});
