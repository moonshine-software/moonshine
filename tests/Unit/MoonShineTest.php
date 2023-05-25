<?php

use MoonShine\Menu\MenuSection;
use MoonShine\Models\MoonshineUser;
use MoonShine\MoonShine;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('core');

beforeEach(function () {
    $this->resource = TestResourceBuilder::new();
});

it('find resource by uri key', function () {
    MoonShine::resources([
        $this->resource,
    ]);

    expect(MoonShine::getResourceFromUriKey($this->resource->uriKey()))
        ->toBe($this->resource);
});

it('menu', function () {
    MoonShine::menu([
        $this->resource,
    ]);

    expect(MoonShine::getMenu())
        ->toBeCollection()
        ->toHaveCount(1)
        ->first()
        ->toBeInstanceOf(MenuSection::class);
});

it('recognizes internal request as MoonShine request', function () {
    $user = MoonshineUser::factory()->create();
    $resource = $this->moonShineUserResource();
    $resource->setItem($user);

    asAdmin()
        ->get($resource->route('edit', $user->getKey()))
        ->assertOk();

    expect(MoonShine::isMoonShineRequest())
        ->toBeTrue();
});

it('recognizes external request as non MoonShine request', function () {
    $this->post(
        route('moonshine.authenticate'),
        ['username' => $this->adminUser()->email, 'password' => 'test']
    )->assertValid();

    expect(MoonShine::isMoonShineRequest())
        ->toBeFalse();
});
