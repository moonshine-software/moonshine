<?php

use MoonShine\Models\MoonshineUser;
use MoonShine\MoonShine;

uses()->group('core');

it('recognizes internal request as MoonShine request', function (): void {
    $user = MoonshineUser::factory()->create();
    $resource = $this->moonShineUserResource();
    $resource->setItem($user);

    asAdmin()
        ->get($resource->route('edit', $user->getKey()))
        ->assertOk();

    expect(MoonShine::isMoonShineRequest())
        ->toBeTrue();
});

it('recognizes external request as non MoonShine request', function (): void {
    $this->post(
        route('moonshine.authenticate'),
        ['username' => $this->adminUser()->email, 'password' => 'test']
    )->assertValid();

    expect(MoonShine::isMoonShineRequest())
        ->toBeFalse();
});
