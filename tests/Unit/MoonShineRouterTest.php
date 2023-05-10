<?php

use MoonShine\MoonShineRouter;
use MoonShine\Resources\MoonShineUserResource;

uses()->group('core');

beforeEach(function () {
    //
});

it('uri key', function () {
    expect(MoonShineRouter::uriKey(MoonShineUserResource::class))
        ->toBe('moon-shine-user-resource');
});

it('route', function () {
    expect(MoonShineRouter::to('index'))
        ->toBe(route('moonshine.index'));
});
