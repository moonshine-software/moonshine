<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\UI\Components\Boolean;
use MoonShine\UI\Fields\Checkbox;

uses()->group('fields');

beforeEach(function () {
    $this->item = createItem();
    $this->field = Checkbox::make('Active');
});

it('show field on pages', function () {
    $resource = addFieldsToTestResource(
        $this->field,
    )->setTestFields([$this->field]);

    $view = Boolean::make($this->item->active)->render();

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: IndexPage::class, resource: $resource)
    )
        ->assertOk()
        ->assertSee('Active')
        ->assertSee($view, false)
    ;

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: DetailPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('Active')
        ->assertSee($view, false)
    ;

    $resource = addFieldsToTestResource(
        $this->field,
        'formFields'
    );

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('active')
    ;
});

it('apply as base', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $data = ['active' => 1];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->active)
        ->toBeTrue()
    ;
});

it('before apply', function () {
    $resource = addFieldsToTestResource(
        Checkbox::make('Active')
            ->onBeforeApply(static function ($item, $data) {
                $item->name = 'Checkbox';

                return $item;
            })
    );

    $data = ['active' => 0];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->name)
        ->toBe('Checkbox')
    ;
});

it('after apply', function () {
    $resource = addFieldsToTestResource(
        Checkbox::make('Active')
            ->onAfterApply(static function ($item, $data) {
                $item->name = 'Checkbox';

                return $item;
            })
    );

    $data = ['active' => 1];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->name)
        ->toBe('Checkbox')
        ->and($this->item->active)
        ->toBe(true)
    ;
});

it('apply as base with default', function () {
    $resource = addFieldsToTestResource(
        Checkbox::make('Active')->default(true)
    );

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey())
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->active)
        ->toBe(true)
    ;
});

function checkboxExport(Item $item): ?string
{
    $data = ['active' => 0];

    $item->active = $data['active'];

    $item->save();

    $resource = addFieldsToTestResource(
        Checkbox::make('Active'),
        'exportFields'
    );

    $export = ExportHandler::make('');

    asAdmin()->get(
        $resource->getRoute('handler', query: ['handlerUri' => $export->getUriKey()])
    )->assertDownload();

    $file = Storage::disk('public')->get('test-resource.csv');

    expect($file)
        ->toContain('Active', '0');

    return $file;
}

it('export', function (): void {
    checkboxExport($this->item);
});

it('import', function (): void {

    $file = checkboxExport($this->item);

    $resource = addFieldsToTestResource(
        Checkbox::make('Active'),
        'importFields'
    );

    $import = ImportHandler::make('');

    asAdmin()->post(
        $resource->getRoute('handler', query: ['handlerUri' => $import->getUriKey()]),
        [$import->getInputName() => $file]
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->active)
        ->toBe(false)
    ;
});
