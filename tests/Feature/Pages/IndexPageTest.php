<?php

declare(strict_types=1);

uses()->group('pages-feature');

use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Factories\CategoryFactory;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;

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
