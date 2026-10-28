<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;
use MoonShine\Contracts\Core\DependencyInjection\RequestContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Pages\Custom\NormalizedFormPage;
use MoonShine\Tests\Fixtures\Pages\TestIndexPage;
use MoonShine\Tests\Fixtures\Resources\TestImageResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Text;
use Psr\Http\Message\ServerRequestInterface;

uses()->group('core');

beforeEach(function (): void {
    $this->item = MoonshineUser::factory()->create();
    $this->resource = TestResourceBuilder::new(MoonshineUser::class);
});

it('interacts with request methods', function () {
    $this->get($this->resource->getIndexPageUrl(['foo' => 'var']));

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

it('shares the request wrapper until the request changes', function (): void {
    $this->get($this->resource->getIndexPageUrl(['foo' => 'first']));

    $first = $this->moonshineCore->getRequest();

    expect($this->moonshineCore->getRequest())
        ->toBe($first)
        ->and($first->get('foo'))
        ->toBe('first');

    $this->get($this->resource->getIndexPageUrl(['foo' => 'second']));

    $second = $this->moonshineCore->getRequest();

    expect($second)
        ->not->toBe($first)
        ->and($second->get('foo'))
        ->toBe('second');
});

it('persists values normalized after reading the request in the form page', function (): void {
    $item = createItem();
    $resource = $this->resource->setTestModel(Item::class)
        ->setTestFields([Text::make('Name')])
        ->setTestPages([TestIndexPage::class, NormalizedFormPage::class, DetailPage::class]);

    asAdmin()->put($resource->getRoute('crud.update', $item->getKey()), ['name' => 'normalized'])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect($item->fresh()->name)->toBe('NORMALIZED');
});

it('does not create a PSR request when only reading form errors', function (): void {
    app()->forgetInstance(RequestContract::class);
    $resolved = 0;
    app()->resolving(ServerRequestInterface::class, static function () use (&$resolved): void {
        $resolved++;
    });

    $request = $this->moonshineCore->getRequest();

    expect($request->getFormErrors())->toBe([])
        ->and($resolved)->toBe(0);

    $request->getAll();

    expect($resolved)->toBe(1);
});

it('reads body and query mutations through the shared request', function (): void {
    app()->instance('request', Illuminate\Http\Request::create(
        uri: '/?query=before',
        method: 'POST',
        parameters: ['name' => 'Before'],
    ));
    $request = $this->moonshineCore->getRequest();
    expect($request->get('name'))->toBe('Before');

    request()->merge(['name' => 'After']);
    request()->query->set('query', 'after');

    expect($request->get('name'))->toBe('After')
        ->and($request->get('query'))->toBe('after')
        ->and($request->getRequest()->getParsedBody()['name'])->toBe('After');
});

it('find resource', function (): void {
    asAdmin()
        ->get(
            $this->resource->getIndexPageUrl()
        )
        ->assertOk();

    $this->moonshineRequest = app(CrudRequestContract::class);

    expect($this->moonshineRequest->getResource())
        ->toBe($this->resource)
        ->and($this->moonshineRequest->hasResource())
        ->toBeTrue()
        ->and($this->moonshineRequest->getResourceUri())
        ->toBe($this->resource->getUriKey())
    ;
});


it('onlyLink parameters', function (): void {
    fakeRequest('/admin/test-comment-resource/index-page?_parentId=test_image-99');

    expect($this->moonshineRequest->getParentRelationId())
        ->toBe('99')
        ->and($this->moonshineRequest->getParentRelationName())
        ->toBe('testImage');
});

it('correct relation name with key string', function (string $id): void {
    $this->get("/admin/test-image-resource/index-page");

    $field = HasMany::make('Images', 'testImages', resource: TestImageResource::class)->relatedLink();

    $relationName = $field->getRelatedLinkRelation();

    fakeRequest("/admin/test-comment-resource/index-page?_parentId=$relationName-$id");

    expect($this->moonshineRequest->getParentRelationId())
        ->toBe($id)
        ->and($this->moonshineRequest->getParentRelationName())
        ->toBe((string) Str::of($relationName)->camel());
})->with(['01J95W9RR73FH93AFCP0YP2VP1', 'ab193d8c-09d5-4185-a62d-d93ee1dd3bfe']);
