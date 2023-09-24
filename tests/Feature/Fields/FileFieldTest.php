<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use MoonShine\Fields\File;
use MoonShine\Resources\ModelResource;

uses()->group('fields');
uses()->group('file-field');

beforeEach(function () {
    $this->item = createItem(countComments: 0);
    $this->resource = createResourceField(
        File::make('File')
            ->dir('items')
    );

});

it('show field on pages', function () {
    asAdmin()->get(
        to_page($this->resource, 'index-page')
    )
        ->assertOk()
        ->assertSee('File')
    ;

    asAdmin()->get(
        to_page($this->resource, 'detail-page', ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('File')
    ;

    asAdmin()->get(
        to_page($this->resource, 'form-page', ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('File')
    ;
});

it('apply as base', function () {
    $file = saveFile($this->resource, $this->item);

    asAdmin()->get(
        to_page($this->resource, 'index-page')
    )
        ->assertOk()
        ->assertSee('File')
        ->assertSee('items/' . $file->hashName())
    ;

    asAdmin()->get(
        to_page($this->resource, 'detail-page', ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('items/' . $file->hashName())
    ;

    asAdmin()->get(
        to_page($this->resource, 'form-page', ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('items/' . $file->hashName())
    ;

});

it('before apply', function () {
    $file = saveFile($this->resource, $this->item);

    $file2 = UploadedFile::fake()->create('test2.csv');

    $data = ['file' => $file2];

    asAdmin()->put(
        $this->resource->route('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->file)
        ->toBe('items/'.$file2->hashName())
    ;

    Storage::disk('public')->assertExists('items/' . $file2->hashName());

    Storage::disk('public')->assertMissing('items/' . $file->hashName());
});

it('after destroy', function () {
    $file = saveFile($this->resource, $this->item);

    asAdmin()->delete(
        $this->resource->route('crud.destroy', $this->item->getKey()),
    )
        ->assertRedirect();

    Storage::disk('public')->assertMissing('items/' . $file->hashName());
});

function saveFile(ModelResource $resource, Model $item)
{
    $file = UploadedFile::fake()->create('test.csv');

    $data = ['file' => $file];

    asAdmin()->put(
        $resource->route('crud.update', $item->getKey()),
        $data
    )
        ->assertRedirect();

    $item->refresh();

    expect($item->file)
        ->toBe('items/' . $file->hashName())
    ;

    Storage::disk('public')->assertExists('items/' . $file->hashName());

    return $file;
}