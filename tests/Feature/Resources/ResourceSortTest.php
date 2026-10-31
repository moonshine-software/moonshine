<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\PageType;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Fieldset;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

uses()->group('resources-feature');

function sortableItemResource(): TestResource
{
    return TestResourceBuilder::new(Item::class)
        ->setTestIndexFields([
            ID::make(),
            Text::make('Name', 'name')->sortable(),
            Text::make('Content', 'content'),
            Text::make('Public at', 'public_at')->sortable('created_at'),
            Text::make('Category', 'category_id')->sortable(
                static fn (Builder $query, string $column, string $direction) => $query->orderBy('updated_at', $direction)
            ),
            Fieldset::make('Group', [
                Text::make('Nested', 'nested'),
            ])->sortable(),
        ]);
}

function sortOrders(array $params): array
{
    return collect(sortableItemResource()->setQueryParams($params)->getQuery()->getQuery()->orders)
        ->map(static fn (array $order): string => $order['column'] . ' ' . $order['direction'])
        ->all();
}

it('sorts by a sortable field from the request', function () {
    expect(sortOrders(['sort' => '-name']))->toBe(['name desc'])
        ->and(sortOrders(['sort' => 'name']))->toBe(['name asc']);
});

it('sorts by the column or callback the field declares', function () {
    expect(sortOrders(['sort' => 'public_at']))->toBe(['created_at asc'])
        ->and(sortOrders(['sort' => '-category_id']))->toBe(['updated_at desc']);
});

it('sorts by a sortable wrapper the table shows as a column', function () {
    expect(sortOrders(['sort' => '-group']))->toBe(['group desc']);
});

it('falls back to the default sort for a field that is not sortable', function () {
    expect(sortOrders(['sort' => 'content']))->toBe(['id desc']);
});

it('falls back to the default sort for a column without a field', function () {
    expect(sortOrders(['sort' => 'title']))->toBe(['id desc'])
        ->and(sortOrders(['sort' => '-data->x']))->toBe(['id desc']);
});

it('falls back to the default sort for a field nested in a wrapper', function () {
    expect(sortOrders(['sort' => 'nested']))->toBe(['id desc']);
});

it('keeps the default sort column with the requested direction', function () {
    expect(sortOrders(['sort' => 'id']))->toBe(['id asc']);
});

it('reads the sort under the resource query param prefix', function () {
    $resource = app(PrefixedSortTestResource::class)
        ->setTestModel(Item::class)
        ->setTestIndexFields([ID::make(), Text::make('Name', 'name')->sortable()]);

    app(CoreContract::class)->resources([$resource]);

    $orders = static fn (array $params): array => collect(
        (clone $resource)->setQueryParams($params)->getQuery()->getQuery()->orders
    )->map(static fn (array $order): string => $order['column'] . ' ' . $order['direction'])->all();

    expect($orders(['p_sort' => '-name']))->toBe(['name desc'])
        ->and($orders(['p_sort' => '-content']))->toBe(['id desc']);
});

it('lists the index page in the default order for a column without a field', function () {
    createItem(2, 0);

    DB::enableQueryLog();

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(
            page: IndexPage::class,
            resource: sortableItemResource(),
            params: ['sort' => '-external_id'],
        )
    )->assertOk();

    expect(
        collect(DB::getQueryLog())
            ->pluck('query')
            // identifier quotes differ per driver (" on sqlite/pgsql, ` on mysql)
            ->map(static fn (string $query): string => str_replace(['"', '`'], '', $query))
            ->filter(static fn (string $query): bool => str_starts_with($query, 'select * from items '))
            ->map(static fn (string $query): string => Str::between($query, 'order by ', ' limit'))
            ->values()
            ->all()
    )->toBe(['id desc']);
});

function relationSortOrders(array $params): array
{
    $item = createItem(countComments: 3);

    $resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([
            ID::make(),
            HasMany::make('Comments', 'comments', resource: TestCommentResource::class)->fields([
                ID::make(),
                Number::make('User id', 'user_id')->sortable(),
                Text::make('Comment title', 'content'),
            ]),
        ]);

    DB::enableQueryLog();

    asAdmin()->get(moonshineRouter()->to('has-many.list', [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => $resource->getUriKey(),
        'resourceItem' => $item->getKey(),
        '_relation' => 'comments',
        ...$params,
    ]))->assertOk();

    return collect(DB::getQueryLog())
        ->pluck('query')
        // identifier quotes differ per driver (" on sqlite/pgsql, ` on mysql)
        ->map(static fn (string $query): string => str_replace(['"', '`'], '', $query))
        ->filter(static fn (string $query): bool => str_starts_with($query, 'select * from comments '))
        ->map(static fn (string $query): string => Str::between($query, 'order by ', ' limit'))
        ->values()
        ->all();
}

it('sorts a relation table by its own sortable fields', function () {
    expect(relationSortOrders(['sort' => '-user_id']))->toBe(['user_id desc']);
});

it('falls back to the default sort for a relation table field that is not sortable', function () {
    expect(relationSortOrders(['sort' => 'content']))->toBe(['id desc']);
});

final class PrefixedSortTestResource extends TestResource
{
    protected string $queryParamPrefix = 'p_';
}
