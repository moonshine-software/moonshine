<?php

use MoonShine\MoonShineRequest;

uses()->group('core');

it('recognizes internal request as MoonShine request', function (): void {

    $resource = $this->moonShineUserResource();

    asAdmin()
        ->get($resource->route('resource.page', query: ['pageUri' => 'index-page']))
        ->assertOk();

    expect(app(MoonShineRequest::class)->isMoonShineRequest())
        ->toBeTrue();

});

it('recognizes external request as non MoonShine request', function (): void {
    $this->post(
        route('moonshine.authenticate'),
        ['username' => $this->adminUser()->email, 'password' => 'test']
    )->assertValid();

    expect(app(MoonShineRequest::class)->isMoonShineRequest())
        ->toBeFalse();
});
