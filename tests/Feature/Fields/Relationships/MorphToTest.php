<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use MoonShine\Laravel\Handlers\ExportHandler;
use MoonShine\Laravel\Handlers\ImportHandler;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\ImageModel;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestImageResource;

uses()->group('model-relation-fields');

beforeEach(function (): void {
    $this->resource = new TestImageResource();
    $this->items = Item::factory(10)->create();
    $this->item = Item::factory()->createOne();
    $this->image = ImageModel::create([
        'imageable_id' => $this->item->getKey(),
        'imageable_type' => Item::class,
    ]);
});

it('show field on pages', function () {
    asAdmin()->get(
        toPage(page: IndexPage::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('Imageable')
        ->assertSee($this->image->imageable->name)
    ;

    asAdmin()->get(
        toPage(page: DetailPage::class, resource: $this->resource, params: ['resourceItem' => $this->image->getKey()])
    )
        ->assertOk()
        ->assertSee('Imageable')
        ->assertSee($this->image->imageable->name)
    ;


    asAdmin()->get(
        toPage(page: FormPage::class, resource: $this->resource, params: ['resourceItem' => $this->image->getKey()])
    )
        ->assertOk()
        ->assertSee('Imageable')
        ->assertSee($this->image->imageable->name)
    ;
});

it('apply as base', function () {
    saveImageable($this->resource, $this->image);

    asAdmin()->get(
        toPage(page: IndexPage::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('Imageable')
        ->assertSee($this->image->imageable->name)
    ;
});

it('export', function (): void {
    morphToExport($this->image, randomImageableId());
});

it('import', function (): void {

    $id = randomImageableId();

    $file = morphToExport($this->image, $id);

    $import = ImportHandler::make('');

    asAdmin()->post(
        $this->resource->getRoute('handler', query: ['handlerUri' => $import->getUriKey()]),
        [$import->getInputName() => $file]
    )->assertRedirect();

    $this->image->refresh();

    expect($this->image->imageable_id)
        ->toBe($id)
    ;
});

function morphToExport(ImageModel $item, int $newId): ?string
{
    $resource = new TestImageResource();
    $item->imageable_id = $newId;
    $item->imageable_type = Item::class;

    $item->save();

    $export = ExportHandler::make('');

    asAdmin()->get(
        $resource->getRoute('handler', query: ['handlerUri' => $export->getUriKey()])
    )->assertDownload();

    $file = Storage::disk('public')->get('test-image-resource.csv');

    expect($file)
        ->toContain('Imageable')
        ->toContain($item->imageable->name)
    ;

    return $file;
}

function saveImageable(ModelResource $resource, Model $item): void
{
    $id = randomImageableId();
    $data = ['imageable_id' => $id, 'imageable_type' => Item::class];

    asAdmin()->put(
        $resource->getRoute('crud.update', $item->getKey()),
        $data
    )
        ->assertRedirect();

    $item->refresh();

    $resource->getIndexFields()->each(function ($field) {
        $field->reset();
    });

    expect($item->imageable_id)
        ->toBe($id)
    ;
}

function randomImageableId(): int
{
    return Item::query()->inRandomOrder()->first()->id;
}
