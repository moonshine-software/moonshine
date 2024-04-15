<?php

declare(strict_types=1);

use MoonShine\Components\Boolean;
use MoonShine\Fields\Checkbox;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Models\Item;

uses()->group('fields');

beforeEach(function () {
    $this->item = createItem();
    $this->field = Checkbox::make('Active');
});

it('show field on pages', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $view = Boolean::make($this->item->active)->render();

    asAdmin()->get(
        to_page(page: IndexPage::class, resource: $resource)
    )
        ->assertOk()
        ->assertSee('Active')
        ->assertSee($view, false)
    ;

    asAdmin()->get(
        to_page(page: DetailPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('Active')
        ->assertSee($view, false)
    ;

    asAdmin()->get(
        to_page(page: FormPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
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
        $resource->route('crud.update', $this->item->getKey()),
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
            ->onBeforeApply(function ($item, $data) {
                $item->name = 'Checkbox';

                return $item;
            })
    );

    $data = ['active' => 0];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
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
            ->onAfterApply(function ($item, $data) {
                $item->name = 'Checkbox';

                return $item;
            })
    );

    $data = ['active' => 1];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
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
        $resource->route('crud.update', $this->item->getKey())
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
        Checkbox::make('Active')->showOnExport()
    );

    $export = ExportHandler::make('');

    asAdmin()->get(
        $resource->route('handler', query: ['handlerUri' => $export->uriKey()])
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
        Checkbox::make('Active')->useOnImport()
    );

    $import = ImportHandler::make('');

    asAdmin()->post(
        $resource->route('handler', query: ['handlerUri' => $import->uriKey()]),
        [$import->getInputName() => $file]
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->active)
        ->toBe(false)
    ;
});
