<?php

use Illuminate\Contracts\Auth\Guard;
use MoonShine\Models\MoonshineUser;
use MoonShine\MoonShineAuth;

uses()->group('core');

beforeEach(function () {
    //
});

it('moonshine guard', function () {
    expect(MoonShineAuth::guard())
        ->toBeInstanceOf(Guard::class);
});

it('moonshine model', function () {
    expect(MoonShineAuth::model())
        ->toBeInstanceOf(MoonshineUser::class);
});
