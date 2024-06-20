<?php

declare(strict_types=1);

use Illuminate\Contracts\Auth\Guard;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\MoonShineAuth;

uses()->group('core');

beforeEach(function (): void {
    //
});

it('moonshine guard', function (): void {
    expect(MoonShineAuth::getGuard())
        ->toBeInstanceOf(Guard::class);
});

it('moonshine model', function (): void {
    expect(MoonShineAuth::getModel())
        ->toBeInstanceOf(MoonshineUser::class);
});
