<?php

declare(strict_types=1);

use MoonShine\Enums\PageType;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Models\MoonshineUser;
use MoonShine\MoonShineRequest;
use MoonShine\Tests\Fixtures\Resources\TestImageResource;
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
        ->toBe($this->resource->uriKey());
});


it('onlyLink parameters', function (): void {
    fakeRequest('/admin/test-comment-resource/index-page?_parentId=test_image-99');

    expect(moonshineRequest()->getParentRelationId())
        ->toBe('99')
        ->and(moonshineRequest()->getParentRelationName())
        ->toBe('testImage');
});

it('correct relation name with key string', function (string $id): void {
    $this->get("/admin/resource/test-image-resource/index-page");

    $field = HasMany::make('Images', 'testImages', resource: new TestImageResource())->onlyLink();

    $relationName = $field->getOnlyLinkRelation();

    fakeRequest("/admin/resource/test-comment-resource/index-page?_parentId=$relationName-$id");

    expect(moonshineRequest()->getParentRelationId())
        ->toBe($id)
        ->and(moonshineRequest()->getParentRelationName())
        ->toBe((string) str($relationName)->camel());
})->with(['01J95W9RR73FH93AFCP0YP2VP1', 'ab193d8c-09d5-4185-a62d-d93ee1dd3bfe']);
