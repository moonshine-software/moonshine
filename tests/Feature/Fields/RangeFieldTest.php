<?php

declare(strict_types=1);

use MoonShine\Applies\Filters\RangeModelApply;
use MoonShine\Fields\Range;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Models\Item;

use function Pest\Laravel\get;

uses()->group('fields');

beforeEach(function () {
    $this->item = createItem(countComments: 0);
});

it('show field on pages', function () {
    $resource = addFieldsToTestResource(
        Range::make('Range')->fromTo('start_point', 'end_point')
    );

    asAdmin()->get(
        to_page(page: IndexPage::class, resource: $resource)
    )
        ->assertOk()
        ->assertSee('Range')
        ->assertSee('0 - 100')
    ;

    asAdmin()->get(
        to_page(page: DetailPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('Range')
        ->assertSee('0 - 100')
    ;

    asAdmin()->get(
        to_page(page: FormPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('range')
        ->assertSee('start_point')
        ->assertSee('end_point')
    ;
});

it('apply as base', function () {
    $resource = addFieldsToTestResource(
        Range::make('Range')->fromTo('start_point', 'end_point')
    );

    $data = ['start_point' => 10, 'end_point' => 90];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        ['range' => $data]
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->start_point)
        ->toBe(10)
        ->and($this->item->end_point)
        ->toBe(90)
    ;
});

it('before apply', function () {
    $resource = addFieldsToTestResource(
        Range::make('Range')->fromTo('start_point', 'end_point')
            ->onBeforeApply(function ($item, $data) {
                $item->name = $data['start_point'] . ' - ' . $data['end_point'];

                return $item;
            })
    );

    $data = ['start_point' => 10, 'end_point' => 90];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        ['range' => $data]
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->name)
        ->toBe('10 - 90')
    ;
});

it('after apply', function () {
    $resource = addFieldsToTestResource(
        Range::make('Range')->fromTo('start_point', 'end_point')
        ->onAfterApply(function ($item) {
            $item->start_point = $item->start_point * 1000;
            $item->end_point = $item->end_point * 1000;

            return $item;
        })
    );

    $data = ['start_point' => 10, 'end_point' => 90];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        ['range' => $data]
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->start_point)
        ->toBe(10000)
        ->and($this->item->end_point)
        ->toBe(90000)
    ;
});

it('apply as base with default', function () {
    $data = ['start_point' => 10, 'end_point' => 90];

    $resource = addFieldsToTestResource(
        Range::make('Range')->fromTo('start_point', 'end_point')->default($data)
    );

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey())
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->start_point)
        ->toBe(10)
        ->and($this->item->end_point)
        ->toBe(90)
    ;
});

it('apply as filter', function (): void {
    $field = Range::make('Range', 'start_point')
        ->wrapName('filters');

    $query = Item::query();

    get('/?filters[start_point][from]=10&filters[start_point][to]=20');

    $field
        ->onApply((new RangeModelApply())->apply($field))
        ->apply(
            static fn (Builder $query) => $query,
            $query
        )
    ;

    expect($query->toRawSql())
        ->toContain('start_point', '10', '20')
    ;
});

function rangeExport(Item $item): ?string
{
    $data = ['start_point' => 10, 'end_point' => 90];

    $item->start_point = $data['start_point'];
    $item->end_point = $data['end_point'];

    $item->save();

    $resource = addFieldsToTestResource(
        Range::make('Range')->fromTo('start_point', 'end_point')->showOnExport()
    );

    $export = ExportHandler::make('');

    asAdmin()->get(
        $resource->route('handler', query: ['handlerUri' => $export->uriKey()])
    )->assertDownload();

    $file = Storage::disk('public')->get('test-resource.csv');

    expect($file)
        ->toContain('Range');

    return $file;
}

it('export', function (): void {
    rangeExport($this->item);
});

it('import', function (): void {

    $file = rangeExport($this->item);

    $resource = addFieldsToTestResource(
        Range::make('Range')->fromTo('start_point', 'end_point')->useOnImport()
    );

    $import = ImportHandler::make('');

    asAdmin()->post(
        $resource->route('handler', query: ['handlerUri' => $import->uriKey()]),
        [$import->getInputName() => $file]
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->start_point)
        ->toBe(10)
        ->and($this->item->end_point)
        ->toBe(90)
    ;
});
