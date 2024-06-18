<?php

declare(strict_types=1);

use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Support\Enums\PageType;

uses()->group('core');

it('recognizes internal request as MoonShine request', function (): void {

    $resource = $this->moonShineUserResource();

    asAdmin()
        ->get($resource->getRoute('resource.page', query: ['pageUri' => PageType::INDEX->value]))
        ->assertOk();

    expect(app(MoonShineRequest::class)->isMoonShineRequest())
        ->toBeTrue();

});

it('recognizes external request as non MoonShine request', function (): void {
    $this->get('/')->assertValid();

    expect(app(MoonShineRequest::class)->isMoonShineRequest())
        ->toBeFalse();
});
