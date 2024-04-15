<?php

declare(strict_types=1);

use MoonShine\Enums\PageType;
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
