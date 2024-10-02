<?php

declare(strict_types=1);

use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Support\Enums\PageType;
use MoonShine\Tests\Fixtures\Resources\TestImageResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('core');

beforeEach(function (): void {
    $this->item = MoonshineUser::factory()->create();
    $this->resource = TestResourceBuilder::new(MoonshineUser::class);
});

it('interacts with request methods', function () {
    $this->get($this->resource->getRoute('resource.page', query: [
        'pageUri' => PageType::INDEX->value,
        'foo' => 'var',
    ]));

    expect($this->moonshineCore->getRequest()->getPath())
        ->toBe(request()->path())
        ->and($this->moonshineCore->getRequest()->getHost())
        ->toBe(request()->host())
        ->and($this->moonshineCore->getRequest()->urlIs('*resource*'))
        ->toBeTrue()
        ->and(request()->fullUrlIs('*resource*'))
        ->toBeTrue()
        ->and($this->moonshineCore->getRequest()->getUrlWithQuery(['bar' => 2]))
        ->toBe(request()->fullUrlWithQuery(['bar' => 2]))
        ->and($this->moonshineCore->getRequest()->getUrl())
        ->toBe(request()->url())
    ;
});

it('find resource', function (): void {
    asAdmin()
        ->get($this->resource->getRoute('resource.page', query: ['pageUri' => PageType::INDEX->value]))
        ->assertOk();


    expect(moonshineRequest()->getResource())
        ->toBe($this->resource)
        ->and(moonshineRequest()->hasResource())
        ->toBeTrue()
        ->and(moonshineRequest()->getResourceUri())
        ->toBe($this->resource->getUriKey())
    ;
});


it('onlyLink parameters', function (): void {
    fakeRequest('/admin/test-comment-resource/index-page?_parentId=test_image-99');

    expect(moonshineRequest()->getParentRelationId())
        ->toBe('99')
        ->and(moonshineRequest()->getParentRelationName())
        ->toBe('testImage');
});

it('correct relation name with key string', function (string $id): void {
    $this->get("/admin/test-image-resource/index-page");

    $field = HasMany::make('Images', 'testImages', resource: TestImageResource::class)->relatedLink();

    $relationName = $field->getRelatedLinkRelation();

    fakeRequest("/admin/test-comment-resource/index-page?_parentId=$relationName-$id");

    expect(moonshineRequest()->getParentRelationId())
        ->toBe($id)
        ->and(moonshineRequest()->getParentRelationName())
        ->toBe((string) str($relationName)->camel());
})->with(['01J95W9RR73FH93AFCP0YP2VP1', 'ab193d8c-09d5-4185-a62d-d93ee1dd3bfe']);
