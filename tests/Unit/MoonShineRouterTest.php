<?php

use MoonShine\MoonShineRouter;
use MoonShine\Resources\MoonShineUserResource;

uses()->group('core');

beforeEach(function (): void {
    //
});

it('uri key', function (): void {
    expect(MoonShineRouter::uriKey(MoonShineUserResource::class))
        ->toBe('moon-shine-user-resource');
});

it('route', function (): void {
    expect(MoonShineRouter::to('index'))
        ->toBe(route('moonshine.index'));
});
