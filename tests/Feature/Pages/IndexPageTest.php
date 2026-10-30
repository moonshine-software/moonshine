<?php

declare(strict_types=1);

uses()->group('pages-feature');

use Illuminate\Support\Collection;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Tests\Fixtures\Factories\CategoryFactory;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;
use ReflectionMethod;

beforeEach(function () {
    CategoryFactory::new()->count(3)->create();

    $this->resource = app(TestItemResource::class);
});

it('filters', function () {
    $item = createItem(5, 2);

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(
            page: IndexPage::class,
            resource: $this->resource,
            params: [
                'filter' => [
                    'name' => $item->name,
                    'category_id' => $item->category_id,
                ],
            ]
        )
    )
        ->assertSee($item->name)
        ->assertOk()
    ;
});

it('query tags', function () {
    $item = createItem(3, 0);

    $item->category_id = Category::query()->max('id');

    $item->save();

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(
            page: IndexPage::class,
            resource: $this->resource,
            params: [
                'query-tag' => 'item-1-query-tag',
            ]
        )
    )
        ->assertOk()
        ->assertSee($item->name)
    ;
});

it('treats an empty query tag as the default one', function () {
    $default = QueryTag::make('Default', static fn ($query) => $query)->alias('default')->default();
    $other = QueryTag::make('Other', static fn ($query) => $query)->alias('other');

    fakeRequest('/', parameters: ['query-tag' => '']);

    expect($default->isActive())->toBeTrue()
        ->and($other->isActive())->toBeFalse();

    fakeRequest('/', parameters: ['query-tag' => 'other']);

    expect($default->isActive())->toBeFalse()
        ->and($other->isActive())->toBeTrue();
});

it('prefers current filter params over cached query state', function () {
    $resource = app(SaveQueryStateTestItemResource::class);

    fakeRequest('/', parameters: [
        'p_filter' => [
            'name' => 'Fresh filter',
        ],
    ]);

    $resource->setQueryParams(
        $this->moonshineCore->getRequest()->getOnly($resource->getQueryParamsKeys())
    );

    $this->moonshineCore->getCache()->set(
        $resource->publicQueryCacheKey(),
        [
            'p_filter' => [
                'name' => 'Cached filter',
            ],
        ],
    );

    expect($resource->getFilterParams())
        ->toBe([
            'name' => 'Fresh filter',
        ]);
});

it('fills filter params from cached prefixed query state', function () {
    $resource = app(SaveQueryStateTestItemResource::class);

    fakeRequest('/');

    $resource->setQueryParams(
        $this->moonshineCore->getRequest()->getOnly($resource->getQueryParamsKeys())
    );

    $this->moonshineCore->getCache()->set(
        $resource->publicQueryCacheKey(),
        [
            'p_filter' => [
                'name' => 'Cached filter',
            ],
        ],
    );

    expect($resource->getFilterParams())
        ->toBe([
            'name' => 'Cached filter',
        ]);
});

it('stores saved query state as a plain array', function () {
    $resource = app(SaveQueryStateTestItemResource::class);

    fakeRequest('/', parameters: [
        'p_filter' => [
            'name' => 'Planiglass',
        ],
        'p_sort' => 'id',
    ]);

    $resource->setQueryParams(
        $this->moonshineCore->getRequest()->getOnly($resource->getQueryParamsKeys())
    );

    (new ReflectionMethod($resource, 'withCachedQueryParams'))->invoke($resource);

    $cached = $this->moonshineCore->getCache()->get($resource->publicQueryCacheKey());

    expect($cached)->toBeArray();
    expect($cached)->not->toBeInstanceOf(Collection::class);
    expect($cached['p_filter']['name'] ?? null)->toBe('Planiglass');
});

it('scopes saved query state by moonshine user', function () {
    $resource = app(SaveQueryStateTestItemResource::class);

    fakeRequest('/');

    $adminKey = $resource->publicQueryCacheKey();

    $user = MoonshineUser::factory()->create();

    $this->actingAs($user, 'moonshine')->get('/');

    expect($resource->publicQueryCacheKey())
        ->not->toBe($adminKey);
});

final class SaveQueryStateTestItemResource extends TestItemResource
{
    protected bool $saveQueryState = true;

    protected string $queryParamPrefix = 'p_';

    public function publicQueryCacheKey(): string
    {
        return $this->getQueryCacheKey();
    }
}
