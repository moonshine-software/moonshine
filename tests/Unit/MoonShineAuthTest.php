<?php

declare(strict_types=1);

use Illuminate\Contracts\Auth\Guard;
use MoonShine\Models\MoonshineUser;
use MoonShine\MoonShineAuth;

uses()->group('core');

beforeEach(function (): void {
    //
});

it('moonshine guard', function (): void {
    expect(MoonShineAuth::guard())
        ->toBeInstanceOf(Guard::class);
});

it('moonshine model', function (): void {
    expect(MoonShineAuth::model())
        ->toBeInstanceOf(MoonshineUser::class);
});
