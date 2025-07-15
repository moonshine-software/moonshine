<?php

declare(strict_types=1);


use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;

uses()->group('core');

it('recognizes internal request as MoonShine request', function (): void {

    $resource = $this->moonShineUserResource();

    asAdmin()
        ->get($resource->getIndexPageUrl())
        ->assertOk();


    $this->moonshineRequest = app(CrudRequestContract::class);

    expect($this->moonshineRequest->isMoonShineRequest())
        ->toBeTrue();

});

it('recognizes external request as non MoonShine request', function (): void {
    $this->get('/')->assertValid();

    expect($this->moonshineRequest->isMoonShineRequest())
        ->toBeFalse();
});
