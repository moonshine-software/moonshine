<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use MoonShine\Fields\Select;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Models\MoonshineUser;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Models\Item;

uses()->group('fields');

beforeEach(function () {
    $this->item = createItem();
    $this->values = MoonshineUser::query()->pluck('email', 'id')->toArray();

    $this->field = Select::make('User', 'moonshine_user_id')
        ->options($this->values);
});

it('show field on pages', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    asAdmin()->get(
        toPage(page: IndexPage::class, resource: $resource)
    )
        ->assertOk()
        ->assertSee('User')
        ->assertSee($this->item->moonshineUser->email)
    ;

    asAdmin()->get(
        toPage(page: DetailPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('User')
        ->assertSee($this->item->moonshineUser->email)
    ;

    asAdmin()->get(
        toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('User')
    ;
});

it('apply as base', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $data = ['moonshine_user_id' => array_rand($this->values)];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->moonshine_user_id)
        ->toBe($data['moonshine_user_id'])
    ;
});

it('before apply', function () {
    $resource = addFieldsToTestResource(
        $this->field
            ->onBeforeApply(function ($item, $data) {
                $item->name = 'Select';

                return $item;
            })
    );

    $data = ['moonshine_user_id' => array_rand($this->values)];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->name)
        ->toBe('Select')
    ;
});

it('after apply', function () {
    $resource = addFieldsToTestResource(
        $this->field
            ->onAfterApply(function ($item, $data) {
                $item->name = 'Select';

                return $item;
            })
    );

    $data = ['moonshine_user_id' => array_rand($this->values)];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->name)
        ->toBe('Select')
        ->and($this->item->moonshine_user_id)
        ->toBe($data['moonshine_user_id'])
    ;
});

it('apply as base with default', function () {
    $default = array_rand($this->values);
    $resource = addFieldsToTestResource(
        $this->field->default($default)
    );

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey())
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->moonshine_user_id)
        ->toBe($default)
    ;
});

function selectExport(Item $item, Select $field, int $value, string $label): ?string
{
    $data = ['moonshine_user_id' => $value];

    $item->moonshine_user_id = $data['moonshine_user_id'];

    $item->save();

    $resource = addFieldsToTestResource(
        $field->showOnExport()
    );

    $export = ExportHandler::make('');

    asAdmin()->get(
        $resource->route('handler', query: ['handlerUri' => $export->uriKey()])
    )->assertDownload();

    $file = Storage::disk('public')->get('test-resource.csv');

    expect($file)
        ->toContain('User', $label);

    return $file;
}

it('export', function (): void {
    $value = array_rand($this->values);
    selectExport($this->item, $this->field, $value, $this->values[$value]);
});

it('import', function (): void {
    $value = array_rand($this->values);
    $file = selectExport($this->item, $this->field, $value, $this->values[$value]);

    $resource = addFieldsToTestResource(
        $this->field->useOnImport()
    );

    $import = ImportHandler::make('');

    asAdmin()->post(
        $resource->route('handler', query: ['handlerUri' => $import->uriKey()]),
        [$import->getInputName() => $file]
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->moonshine_user_id)
        ->toBe($value)
    ;
});
