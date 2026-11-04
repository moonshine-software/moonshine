<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\PageType;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Fieldset;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

uses()->group('resources-feature');

final class PrefixedSortTestResource extends TestResource
{
    protected string $queryParamPrefix = 'p_';
}

function sortTestResource(?TestResource $resource = null): TestResource
{
    return ($resource ?? TestResourceBuilder::new())
        ->setTestModel(Item::class)
        ->setTestIndexFields([
            ID::make()->sortable(),
            Text::make('Name', 'name')->sortable(),
            Text::make('Content', 'content'),
            Text::make('Public at', 'public_at')->sortable('created_at'),
            Text::make('Category', 'category_id')->sortable(
                static fn (Builder $query, string $column, string $direction): Builder => $query->orderBy('updated_at', $direction)
            ),
            Number::make('End point', 'end_point')->sortable()->canSee(static fn (): bool => false),
            Fieldset::make('Group', [
                Text::make('Start point', 'start_point')->sortable(),
            ]),
        ]);
}

/**
 * @param array<string, mixed> $params
 * @return list<string>
 */
function sortTestOrders(array $params, ?TestResource $resource = null): array
{
    return collect(($resource ?? sortTestResource())->setQueryParams($params)->getQuery()->getQuery()->orders)
        ->map(static fn (array $order): string => "{$order['column']} {$order['direction']}")
        ->all();
}

/**
 * @return list<string>
 */
function sortTestLoggedOrders(string $table): array
{
    return collect(DB::getQueryLog())
        ->pluck('query')
        ->map(static fn (string $query): string => str_replace(['"', '`'], '', $query))
        ->filter(static fn (string $query): bool => str_starts_with($query, "select * from $table "))
        ->map(static fn (string $query): string => (string) str($query)->between('order by ', ' limit'))
        ->values()
        ->all();
}

it('sorts by a sortable index field in both directions', function (string $sort, string $order): void {
    expect(sortTestOrders(['sort' => $sort]))->toBe([$order]);
})->with([
    ['name', 'name asc'],
    ['-name', 'name desc'],
]);

it('sorts by the column or callback the sortable field declares', function (string $sort, string $order): void {
    expect(sortTestOrders(['sort' => $sort]))->toBe([$order]);
})->with([
    ['public_at', 'created_at asc'],
    ['-category_id', 'updated_at desc'],
]);

it('accepts the default sort column with the requested direction', function (): void {
    expect(sortTestOrders(['sort' => 'id']))->toBe(['id asc'])
        ->and(sortTestOrders([]))->toBe(['id desc']);
});

it('keeps the default order for a column the list does not offer for sorting', function (mixed $sort): void {
    expect(sortTestOrders(['sort' => $sort]))->toBe(['id desc']);
})->with([
    'a field that is not sortable' => 'content',
    'a sortable field the user cannot see' => '-end_point',
    'a field nested in a wrapper' => 'start_point',
    'a column without a field' => '-password',
    'a JSON path' => 'data->secret',
    'an empty value' => '',
    'a non-string value' => [['name']],
]);

it('reads the sort under the query parameter prefix of the resource', function (): void {
    $resource = sortTestResource(app(PrefixedSortTestResource::class));
    $this->moonshineCore->resources([$resource]);

    expect(sortTestOrders(['p_sort' => '-name'], clone $resource))->toBe(['name desc'])
        ->and(sortTestOrders(['p_sort' => '-content'], clone $resource))->toBe(['id desc']);
});

it('lists the index page in the default order for an unknown sort column', function (string $sort): void {
    createItem(2, 0);
    DB::enableQueryLog();

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(
            page: IndexPage::class,
            resource: sortTestResource(),
            params: ['sort' => $sort],
        )
    )->assertOk();

    expect(sortTestLoggedOrders('items'))->toBe(['id desc']);
})->with(['-missing_column', 'data->secret']);

it('returns the JSON index in the default order for an unknown sort column', function (): void {
    createItem(2, 0);
    $resource = sortTestResource();
    $this->moonshineCore->resources([$resource]);
    DB::enableQueryLog();

    asAdmin()->getJson($resource->getRoute('crud.index', query: ['sort' => '-missing_column']))
        ->assertOk();

    expect(sortTestLoggedOrders('items'))->toBe(['id desc']);
});

it('sorts a relation list only by the sortable index fields of the related resource', function (string $sort, string $order): void {
    $item = createItem(countComments: 3);
    DB::enableQueryLog();

    asAdmin()->get($this->moonshineCore->getRouter()->to('has-many.list', [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => 'test-item-resource',
        'resourceItem' => $item->getKey(),
        '_relation' => 'comments',
        'sort' => $sort,
    ]))->assertOk();

    expect(sortTestLoggedOrders('comments'))->toBe([$order]);
})->with([
    'a sortable field' => ['-content', 'content desc'],
    'a field that is not sortable' => ['user_id', 'id desc'],
    'a column without a field' => ['-item_id', 'id desc'],
]);
