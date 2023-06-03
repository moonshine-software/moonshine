<?php

use MoonShine\Models\MoonshineUser;
use MoonShine\MoonShineRequest;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('core');

beforeEach(function (): void {
    $this->item = MoonshineUser::factory()->create();
    $this->resource = TestResourceBuilder::new(
        MoonshineUser::class,
        true
    );
});

it('find resource', function (): void {
    fakeRequest($this->resource->route('index'));

    $request = app(MoonShineRequest::class);

    expect($request->getResource())
        ->toBe($this->resource)
        ->and($request->hasResource())
        ->toBeTrue()
        ->and($request->getResourceUri())
        ->toBe($this->resource->uriKey())
        ->and($request->getItem())
        ->toBeNull()
        ->and($request->getId())
        ->toBeNull();
});
