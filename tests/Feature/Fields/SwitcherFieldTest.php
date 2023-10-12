<?php

declare(strict_types=1);

use MoonShine\Fields\Switcher;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Models\Item;

uses()->group('fields');

beforeEach(function () {
    $this->item = createItem();
    $this->field = Switcher::make('Active')->updateOnPreview(url: fn() => '/');
});

it('show field on pages', function () {
    $resource = createResourceField(
        $this->field
    );


    asAdmin()->get(
        to_page(page: IndexPage::class, resource: $resource)
    )
        ->assertOk()
        ->assertSee('Active')
        ->assertSee('form-switcher')
    ;

    asAdmin()->get(
        to_page(page: DetailPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('Active')
        ->assertSee('form-switcher')
    ;

    asAdmin()->get(
        to_page(page: FormPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('Active')
        ->assertSee('form-switcher')
    ;
});

it('apply as base', function () {
    $resource = createResourceField(
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
        ->toBe(true)
    ;
});

it('before apply', function () {
    $resource = createResourceField(
        Switcher::make('Active')
            ->onBeforeApply(function ($item) {
                $item->name = 'Switcher';

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
        ->toBe('Switcher')
    ;
});

it('after apply', function () {
    $resource = createResourceField(
        Switcher::make('Active')
            ->onAfterApply(function ($item, $data) {
                $item->name = 'Switcher';

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
        ->toBe('Switcher')
        ->and($this->item->active)
        ->toBe(true)
    ;
});

it('apply as base with default', function () {
    $resource = createResourceField(
        Switcher::make('Active')->default(1)
    );

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey())
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->active)
        ->toBe(true)
    ;
});

function switcherExport(Item $item): ?string
{
    $data = ['active' => 0];

    $item->active = $data['active'];

    $item->save();

    $resource = createResourceField(
        Switcher::make('Active')->showOnExport()
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
    switcherExport($this->item);
});

it('import', function (): void {

    $file = switcherExport($this->item);

    $resource = createResourceField(
        Switcher::make('Active')->useOnImport()
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