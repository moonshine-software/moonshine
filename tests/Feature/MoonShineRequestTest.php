<?php

declare(strict_types=1);

use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Support\Enums\PageType;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('core');

beforeEach(function (): void {
    $this->item = MoonshineUser::factory()->create();
    $this->resource = TestResourceBuilder::new(MoonshineUser::class);
});

it('interacts with request methods', function () {
    $this->get($this->resource->route('resource.page', query: [
        'pageUri' => PageType::INDEX->value,
        'foo' => 'var',
    ]));

    expect(moonshine()->getRequest()->getPath())
        ->toBe(request()->path())
        ->and(moonshine()->getRequest()->getHost())
        ->toBe(request()->host())
        ->and(moonshine()->getRequest()->urlIs('*resource*'))
        ->toBeTrue()
        ->and(request()->fullUrlIs('*resource*'))
        ->toBeTrue()
        ->and(moonshine()->getRequest()->getUrlWithQuery(['bar' => 2]))
        ->toBe(request()->fullUrlWithQuery(['bar' => 2]))
        ->and(moonshine()->getRequest()->getUrl())
        ->toBe(request()->url())
    ;
});

it('find resource', function (): void {
    asAdmin()
        ->get($this->resource->route('resource.page', query: ['pageUri' => PageType::INDEX->value]))
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


it('onlyLink parameters', function (): void {
    fakeRequest('/admin/test-comment-resource/index-page?_parentId=test-image-99');

    expect(moonshineRequest()->getParentRelationId())
        ->toBe('99')
        ->and(moonshineRequest()->getParentRelationName())
        ->toBe('testImage');
});
