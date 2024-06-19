<?php

declare(strict_types=1);

use Illuminate\Contracts\Auth\Guard;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\MoonShineAuth;

uses()->group('core');

beforeEach(static function (): void {
    //
});

it('moonshine guard', static function (): void {
    expect(MoonShineAuth::getGuard())
        ->toBeInstanceOf(Guard::class);
});

it('moonshine model', static function (): void {
    expect(MoonShineAuth::getModel())
        ->toBeInstanceOf(MoonshineUser::class);
});
