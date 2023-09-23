<?php

declare(strict_types=1);

use MoonShine\Applies\Filters\RangeModelApply;
use MoonShine\Fields\RangeField;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Tests\Fixtures\Models\Item;

use function Pest\Laravel\get;

uses()->group('fields');

beforeEach(function () {
    $this->item = createItem(countComments: 0);
});

it('show field on pages', function () {
    $resource = createResourceField(
        RangeField::make('Range')->fromTo('start_point', 'end_point')
    );

    asAdmin()->get(
        to_page($resource, 'index-page')
    )
        ->assertOk()
        ->assertSee('Range')
        ->assertSee('0 - 100')
    ;

    asAdmin()->get(
        to_page($resource, 'detail-page', ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('Range')
        ->assertSee('0 - 100')
    ;

    asAdmin()->get(
        to_page($resource, 'form-page', ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('range')
        ->assertSee('start_point')
        ->assertSee('end_point')
    ;
});

it('apply as base', function () {
    $resource = createResourceField(
        RangeField::make('Range')->fromTo('start_point', 'end_point')
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
    $resource = createResourceField(
        RangeField::make('Range')->fromTo('start_point', 'end_point')
            ->onBeforeApply(function ($item, $data) {
                if(empty($data['name'])) {
                    $item->name = $data['start_point'] . ' - ' . $data['end_point'];
                }

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
    $resource = createResourceField(
        RangeField::make('Range')->fromTo('start_point', 'end_point')
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

it('apply as base date', function () {
    $resource = createResourceField(
        RangeField::make('Range')
            ->fromTo('start_date', 'end_date')
            ->dates()
    );

    $start = now()->format('Y-m-d');
    $end = now()->add('+1', 'day')->format('Y-m-d');

    $data = ['start_date' => $start, 'end_date' => $end];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        ['range' => $data]
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->start_date)
        ->toBe($start)
        ->and($this->item->end_date)
        ->toBe($end)
    ;
});

it('apply as base with default', function () {
    $data = ['start_point' => 10, 'end_point' => 90];

    $resource = createResourceField(
        RangeField::make('Range')->fromTo('start_point', 'end_point')->default($data)
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
    $field = RangeField::make('Range', 'start_point')
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

it('apply as filter date', function (): void {
    $field = RangeField::make('Range', 'start_date')
        ->dates()
        ->wrapName('filters');

    $query = Item::query();

    $start = now()->format('Y-m-d');
    $end = now()->add('+1', 'day')->format('Y-m-d');

    get("/?filters[start_date][from]=$start&filters[start_date][to]=$end");

    $field
        ->onApply((new RangeModelApply())->apply($field))
        ->apply(
            static fn (Builder $query) => $query,
            $query
        )
    ;

    expect($query->toRawSql())
        ->toContain('start_date', $start, $end)
    ;
});

function rangeExport(Item $item): ?string
{
    $data = ['start_point' => 10, 'end_point' => 90];

    $item->start_point = $data['start_point'];
    $item->end_point = $data['end_point'];

    $item->save();

    $resource = createResourceField(
        RangeField::make('Range')->fromTo('start_point', 'end_point')->showOnExport()
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

    $resource = createResourceField(
        RangeField::make('Range')->fromTo('start_point', 'end_point')->useOnImport()
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
