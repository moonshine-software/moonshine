<?php

use MoonShine\Models\MoonshineUser;
use MoonShine\MoonShineRequest;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('core');

beforeEach(function (): void {
    $this->item = MoonshineUser::factory()->create();
    $this->resource = TestResourceBuilder::new(MoonshineUser::class);
});

it('find resource', function (): void {
    asAdmin()
        ->get($this->resource->route('resource.page', query: ['pageUri' => 'index-page']))
        ->assertOk();


    $request = app(MoonShineRequest::class);

    expect($request->getResource())
        ->toBe($this->resource)
        ->and($request->hasResource())
        ->toBeTrue()
        ->and($request->getResourceUri())
        ->toBe($this->resource->uriKey())
    ;
});
