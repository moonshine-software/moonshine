<?php

use MoonShine\Http\Middleware\ChangeLocale;

uses()->group('middleware');

beforeEach(function () {
    //
});

it('successful changed', function () {
    asAdmin()->get(route('moonshine.index', [
        ChangeLocale::KEY => 'en',
    ]))->assertSee('Dashboard');

    asAdmin()->get(route('moonshine.index', [
        ChangeLocale::KEY => 'ru',
    ]))->assertSee('Панель');
});
