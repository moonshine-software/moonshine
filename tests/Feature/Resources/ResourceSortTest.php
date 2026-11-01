<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
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

function resourceSortItemResource(): TestResource
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
            Number::make('End point', 'end_point')->sortable()->canSee(static fn (): bool => false),
            Fieldset::make('Group', [
                Text::make('Nested', 'nested'),
            ])->sortable(),
        ]);
}

/**
 * The order by clauses the resource query gets for the request params
 */
function resourceSortQueryOrders(array $params, ?TestResource $resource = null): array
{
    $resource ??= resourceSortItemResource();

    return collect($resource->setQueryParams($params)->getQuery()->getQuery()->orders)
        ->map(static fn (array $order): string => $order['column'] . ' ' . $order['direction'])
        ->all();
}

/**
 * The order by clauses of the logged select queries on the table
 */
function resourceSortLoggedOrders(string $table): array
{
    return collect(DB::getQueryLog())
        ->pluck('query')
        // identifier quotes differ per driver (" on sqlite/pgsql, ` on mysql)
        ->map(static fn (string $query): string => str_replace(['"', '`'], '', $query))
        ->filter(static fn (string $query): bool => str_starts_with($query, "select * from $table "))
        ->map(static fn (string $query): string => Str::between($query, 'order by ', ' limit'))
        ->values()
        ->all();
}

/**
 * The order by clauses the relation table of the HasMany field gets for the request params
 */
function resourceSortRelationOrders(HasMany $field, array $params = []): array
{
    $item = createItem(countComments: 3);

    $resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([ID::make(), $field]);

    DB::enableQueryLog();

    asAdmin()->get(moonshineRouter()->to('has-many.list', [
        'pageUri' => PageType::FORM->value,
        'resourceUri' => $resource->getUriKey(),
        'resourceItem' => $item->getKey(),
        '_relation' => 'comments',
        ...$params,
    ]))->assertOk();

    return resourceSortLoggedOrders('comments');
}

function resourceSortComments(?string $resource = null): HasMany
{
    return HasMany::make('Comments', 'comments', resource: $resource ?? TestCommentResource::class);
}

it('sorts by a sortable field from the request', function () {
    expect(resourceSortQueryOrders(['sort' => '-name']))->toBe(['name desc'])
        ->and(resourceSortQueryOrders(['sort' => 'name']))->toBe(['name asc']);
});

it('sorts by the column or callback the field declares', function () {
    expect(resourceSortQueryOrders(['sort' => 'public_at']))->toBe(['created_at asc'])
        ->and(resourceSortQueryOrders(['sort' => '-category_id']))->toBe(['updated_at desc']);
});

it('sorts by a sortable wrapper the table shows as a column', function () {
    expect(resourceSortQueryOrders(['sort' => '-group']))->toBe(['group desc']);
});

it('falls back to the default sort for a field that is not sortable', function () {
    expect(resourceSortQueryOrders(['sort' => 'content']))->toBe(['id desc']);
});

it('falls back to the default sort for a sortable field the user cannot see', function () {
    expect(resourceSortQueryOrders(['sort' => '-end_point']))->toBe(['id desc']);
});

it('falls back to the default sort for a column without a field', function () {
    expect(resourceSortQueryOrders(['sort' => 'title']))->toBe(['id desc'])
        ->and(resourceSortQueryOrders(['sort' => '-data->x']))->toBe(['id desc']);
});

it('falls back to the default sort for a field nested in a wrapper', function () {
    expect(resourceSortQueryOrders(['sort' => 'nested']))->toBe(['id desc']);
});

it('keeps the default sort column with the requested direction', function () {
    expect(resourceSortQueryOrders(['sort' => 'id']))->toBe(['id asc']);
});

it('sorts by a column the resource allows without a field', function () {
    $resource = app(ExtraColumnSortTestResource::class)
        ->setTestModel(Item::class)
        ->setTestIndexFields([ID::make()]);

    expect(resourceSortQueryOrders(['sort' => '-start_point'], clone $resource))->toBe(['start_point desc'])
        ->and(resourceSortQueryOrders(['sort' => '-end_point'], clone $resource))->toBe(['id desc']);
});

it('reads the sort under the resource query param prefix', function () {
    $resource = app(PrefixedSortTestResource::class)
        ->setTestModel(Item::class)
        ->setTestIndexFields([ID::make(), Text::make('Name', 'name')->sortable()]);

    app(CoreContract::class)->resources([$resource]);

    expect(resourceSortQueryOrders(['p_sort' => '-name'], clone $resource))->toBe(['name desc'])
        ->and(resourceSortQueryOrders(['p_sort' => '-content'], clone $resource))->toBe(['id desc']);
});

it('lists the index page in the default order for a column without a field', function () {
    createItem(2, 0);

    DB::enableQueryLog();

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(
            page: IndexPage::class,
            resource: resourceSortItemResource(),
            params: ['sort' => '-external_id'],
        )
    )->assertOk();

    expect(resourceSortLoggedOrders('items'))->toBe(['id desc']);
});

it('sorts a relation table by its own sortable fields', function () {
    $field = resourceSortComments()->fields([
        ID::make(),
        Number::make('User id', 'user_id')->sortable(),
        Text::make('Comment title', 'content'),
    ]);

    expect(resourceSortRelationOrders($field, ['sort' => '-user_id']))->toBe(['user_id desc']);
});

it('falls back to the default sort for a relation table field that is not sortable', function () {
    $field = resourceSortComments()->fields([
        ID::make(),
        Text::make('Comment title', 'content'),
    ]);

    expect(resourceSortRelationOrders($field, ['sort' => 'content']))->toBe(['id desc']);
});

it('sorts a relation table by the fields modifyTable() shows', function () {
    $field = resourceSortComments()
        ->fields([ID::make(), Text::make('Comment title', 'content')])
        ->modifyTable(static fn (TableBuilderContract $table): TableBuilderContract => $table->fields([
            ID::make(),
            Number::make('User id', 'user_id')->sortable(),
        ]));

    expect(resourceSortRelationOrders($field, ['sort' => '-user_id']))->toBe(['user_id desc']);
});

it('does not sort a relation table by a field modifyTable() removed', function () {
    $field = resourceSortComments()
        ->fields([ID::make(), Number::make('User id', 'user_id')->sortable()])
        ->modifyTable(static fn (TableBuilderContract $table): TableBuilderContract => $table->fields([
            ID::make(),
            Text::make('Comment title', 'content'),
        ]));

    expect(resourceSortRelationOrders($field, ['sort' => '-user_id']))->toBe(['id desc']);
});

it('keeps the resource default sort mapping for a relation table without that field', function () {
    app(CoreContract::class)->resources([DefaultSortCommentResource::class]);

    $field = resourceSortComments(DefaultSortCommentResource::class)->fields([ID::make()]);

    expect(resourceSortRelationOrders($field))->toBe(['content desc']);
});

final class PrefixedSortTestResource extends TestResource
{
    protected string $queryParamPrefix = 'p_';
}

final class ExtraColumnSortTestResource extends TestResource
{
    protected function isSortableColumn(string $column, FieldsContract $listFields): bool
    {
        return $column === 'start_point' || parent::isSortableColumn($column, $listFields);
    }
}

final class DefaultSortCommentResource extends TestCommentResource
{
    protected string $sortColumn = 'title';

    protected function indexFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title', 'title')->sortable('content'),
        ];
    }
}
