<?php

use MoonShine\MoonShine;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('components');

beforeEach(function () {
    MoonShine::menu([
        TestResourceBuilder::new()
            ->setTestTitle('Testing menu item')
    ]);
});

it('show menu component', function () {
    test()
        ->blade('<x-moonshine::menu-component />')
        ->assertSee('Testing menu item');
});
