<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestFileResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

uses()->group('resources-feature');
uses()->group('resources-has-one-file');

beforeEach(function (): void {

    $this->item = createItem(1, 1);

    $this->resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([
            ID::make()->sortable(),
            Text::make('Name', 'name')->sortable(),
            HasOne::make('Files', 'itemFile', resource: new TestFileResource()),
        ])
    ;
});

it('resource with has one', function () {
    asAdmin()->get(
        toPage(page: FormPage::class, resource: $this->resource, params: ['resourceItem' => $this->item->id])
    )
        ->assertOk()
        ->assertSee('Name')
        ->assertSee('itemFile')
    ;
});

it('delete a has one file after delete item', function () {
    $file = addHasOneFile(new TestFileResource(), $this->item);

    $this->resource->setDeleteRelationships();

    deleteItemWithHasOne($this->resource, $this->item->getKey());

    Storage::disk('public')->assertMissing($file->hashName());

});

it('not delete a has many file after delete item', function () {
    $file = addHasOneFile(new TestFileResource(), $this->item);

    deleteItemWithHasOne($this->resource, $this->item->getKey());

    Storage::disk('public')->assertExists($file->hashName());
});

function addHasOneFile(ModelResource $resource, Model $item)
{
    $file = UploadedFile::fake()->create('test.csv');

    $data = [
        'path' => $file,
        'item_id' => $item->id,
    ];

    asAdmin()->post(
        $resource->getRoute('crud.store', $item->getKey()),
        $data
    )
        ->assertRedirect();

    $item->refresh();

    expect($item->itemFile)
        ->not()->toBeNull()
        ->and($item->itemFile->path)
        ->toBe($file->hashName())
    ;

    Storage::disk('public')->assertExists($file->hashName());

    return $file;
}

function deleteItemWithHasOne(ModelResource $resource, int $itemId): void
{
    asAdmin()->delete(
        $resource->getRoute('crud.destroy', $itemId),
    )
        ->assertRedirect()
    ;

    $item = Item::query()->where('id', $itemId)->first();

    expect($item)->toBeNull();
}
