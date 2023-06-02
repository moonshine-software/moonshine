<?php

use MoonShine\Actions\ExportAction;
use MoonShine\Actions\MassActions;
use MoonShine\BulkActions\BulkAction;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Text;
use MoonShine\Filters\Filters;
use MoonShine\Filters\TextFilter;
use MoonShine\FormActions\FormAction;
use MoonShine\ItemActions\ItemAction;
use MoonShine\ItemActions\ItemActions;
use MoonShine\Models\MoonshineUser;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('resource');

beforeEach(function () {
    $this->resource = TestResourceBuilder::new(
        MoonshineUser::class,
        true
    );
});

it('correct router', function () {
    expect($this->resource)
        ->routeParam()
        ->toBe('resourceItem')
        ->routeNameAlias()
        ->toBe('tests')
        ->routeName('index')
        ->toBe('moonshine.tests.index')
        ->route('index')
        ->toContain('/moonshine/resource/test-resource')
        ->route('show', 1)
        ->toContain('/moonshine/resource/test-resource/1')
        ->route('create')
        ->toContain('/moonshine/resource/test-resource/create')
        ->route('edit', 1)
        ->toContain('/moonshine/resource/test-resource/1/edit')
        ->route('destroy', 1)
        ->toContain('/moonshine/resource/test-resource/1')
        ->route('index', query: ['q' => 'test'])
        ->toContain('/moonshine/resource/test-resource?q=test')
    ;
});

it('resource uri key', function () {
    expect($this->resource->uriKey())
        ->toBe('test-resource');
});

it('resource fields', function () {
    $this->resource->setTestFields([
        Text::make('Label'),
    ]);

    expect($this->resource->getFields())
        ->toBeInstanceOf(Fields::class)
        ->toHaveCount(1)
        ->first()->toBeInstanceOf(Text::class);
});

it('find field', function () {
    $field = Text::make('Label');
    $this->resource->setTestFields([
        $field,
    ]);

    expect($this->resource->getField('label'))
        ->toBe($field);
});

it('resource filters', function () {
    $this->resource->setTestFilters([
        TextFilter::make('Label'),
    ]);

    expect($this->resource->getFilters())
        ->toBeInstanceOf(Filters::class)
        ->toHaveCount(1)
        ->first()->toBeInstanceOf(TextFilter::class);
});

it('find filter', function () {
    $field = TextFilter::make('Label');
    $this->resource->setTestFilters([
        $field,
    ]);

    expect($this->resource->getFilter('label'))
        ->toBe($field);
});

it('resource actions', function () {
    $action = ExportAction::make('Label');
    $this->resource->setTestActions([
        $action,
    ]);

    expect($this->resource->getActions())
        ->toBeInstanceOf(MassActions::class)
        ->toHaveCount(2)
        ->first()->toBeInstanceOf(ExportAction::class);
});

it('resource item actions', function () {
    $action = ItemAction::make('Label', static fn () => '');
    $this->resource->setTestItemActions([
        $action,
    ]);

    expect($this->resource->itemActionsCollection())
        ->toBeInstanceOf(ItemActions::class)
        ->toHaveCount(1)
        ->first()->toBeInstanceOf(ItemAction::class);
});

it('resource form actions', function () {
    $action = FormAction::make('Label', static fn () => '');
    $this->resource->setTestFormActions([
        $action,
    ]);

    expect($this->resource->formActionsCollection())
        ->toBeInstanceOf(ItemActions::class)
        ->toHaveCount(1)
        ->first()->toBeInstanceOf(FormAction::class);
});

it('resource bulk actions', function () {
    $action = BulkAction::make('Label', static fn () => '');
    $this->resource->setTestBulkActions([
        $action,
    ]);

    expect($this->resource->bulkActionsCollection())
        ->toBeInstanceOf(MassActions::class)
        ->toHaveCount(1)
        ->first()->toBeInstanceOf(BulkAction::class);
});

it('resource query tags', function () {
    $tag = QueryTag::make('Label', static fn () => MoonshineUser::query());
    $this->resource->setTestQueryTags([
        $tag,
    ]);

    expect($this->resource->queryTags())
        ->toBeArray()
        ->toHaveCount(1)
        ->each->toBeInstanceOf(QueryTag::class);
});

it('resource authorization', function () {
    asAdmin();

    expect($this->resource->can('view'))
        ->toBeTrue();

    $resource = TestResourceBuilder::new()
        ->setTestPolicy(true);

    expect($resource->can('view'))
        ->toBeFalse();
});

it('soft deletes', function () {
    expect($this->resource->softDeletes())
        ->toBeFalse();
});

it('precognition mode', function () {
    expect($this->resource->isPrecognition())
        ->toBeFalse()
        ->and($this->resource->precognitionMode()->isPrecognition())
        ->toBeTrue()
    ;
});

it('relatable mode', function () {
    expect($this->resource->isPrecognition())
        ->toBeFalse()
        ->and($this->resource->relatable('column', 'key'))
        ->isPrecognition()
        ->toBeTrue()
        ->isRelatable()
        ->toBeTrue()
    ;
});

it('modal', function () {
    expect($this->resource)
        ->isCreateInModal()
        ->toBeFalse()
        ->isInCreateOrEditModal()
        ->toBeFalse()
        ->isEditInModal()
        ->toBeFalse()
    ;
});

it('correct item', function () {
    $item = MoonshineUser::factory()->create();

    expect($this->resource->setItem($item))
        ->getItem()
        ->toBe($item)
    ;
});

it('is now on detail route', function () {
    $item = MoonshineUser::factory()->create();

    $this->get($this->resource->route('show', $item->getKey()));

    expect($this->resource)
        ->isNowOnIndex()
        ->toBeFalse()
        ->isNowOnForm()
        ->toBeFalse()
        ->isNowOnCreateForm()
        ->toBeFalse()
        ->isNowOnUpdateForm()
        ->toBeFalse()
        ->isNowOnDetail()
        ->toBeTrue()
    ;
});

it('is now on index route', function () {
    $this->get($this->resource->route('index'));

    expect($this->resource)
        ->isNowOnIndex()
        ->toBeTrue()
        ->isNowOnForm()
        ->toBeFalse()
        ->isNowOnCreateForm()
        ->toBeFalse()
        ->isNowOnUpdateForm()
        ->toBeFalse()
        ->isNowOnDetail()
        ->toBeFalse()
    ;
});

it('is now on create form route', function () {
    $this->get($this->resource->route('create'));

    expect($this->resource)
        ->isNowOnIndex()
        ->toBeFalse()
        ->isNowOnForm()
        ->toBeTrue()
        ->isNowOnCreateForm()
        ->toBeTrue()
        ->isNowOnUpdateForm()
        ->toBeFalse()
        ->isNowOnDetail()
        ->toBeFalse()
    ;
});

it('is now on update form route', function () {
    $item = MoonshineUser::factory()->create();

    $this->get($this->resource->route('edit', $item->getKey()));

    expect($this->resource)
        ->isNowOnIndex()
        ->toBeFalse()
        ->isNowOnForm()
        ->toBeTrue()
        ->isNowOnCreateForm()
        ->toBeFalse()
        ->isNowOnUpdateForm()
        ->toBeTrue()
        ->isNowOnDetail()
        ->toBeFalse()
    ;
});
