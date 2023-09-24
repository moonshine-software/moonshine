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
        File::make('Files')
            ->multiple()
            ->dir('items')
    );
});

it('show field on pages', function () {
    asAdmin()->get(
        to_page($this->resource, 'index-page')
    )
        ->assertOk()
        ->assertSee('Files')
    ;

    asAdmin()->get(
        to_page($this->resource, 'detail-page', ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('Files')
    ;

    asAdmin()->get(
        to_page($this->resource, 'form-page', ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('Files')
    ;
});

it('apply as base', function () {
    $files = saveMultipleFiles($this->resource, $this->item);

    asAdmin()->get(
        to_page($this->resource, 'index-page')
    )
        ->assertOk()
        ->assertSee('File')
        ->assertSee('items/' . $files[0]->hashName())
        ->assertSee('items/' . $files[1]->hashName())
    ;

    asAdmin()->get(
        to_page($this->resource, 'detail-page', ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('items/' . $files[0]->hashName())
        ->assertSee('items/' . $files[1]->hashName())
    ;

    asAdmin()->get(
        to_page($this->resource, 'form-page', ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('items/' . $files[0]->hashName())
        ->assertSee('items/' . $files[1]->hashName())
    ;
});

it('before apply', function () {
    $files = saveMultipleFiles($this->resource, $this->item);

    $file3 = UploadedFile::fake()->create('test3.csv');

    $data = ['files' => [$file3], 'hidden_files' => ['items/' . $files[0]->hashName(), 'items/' . $files[1]->hashName()]];

    asAdmin()->put(
        $this->resource->route('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->files->toArray())
        ->toBe([
            'items/'.$files[0]->hashName(),
            'items/'.$files[1]->hashName(),
            'items/'.$file3->hashName(),
        ])
    ;

    Storage::disk('public')->assertExists('items/' . $files[0]->hashName());

    Storage::disk('public')->assertExists('items/' . $files[1]->hashName());

    Storage::disk('public')->assertExists('items/' . $file3->hashName());
});

it('after destroy', function () {
    $files = saveMultipleFiles($this->resource, $this->item);

    asAdmin()->delete(
        $this->resource->route('crud.destroy', $this->item->getKey()),
    )
        ->assertRedirect();

    Storage::disk('public')->assertMissing('items/' . $files[0]->hashName());

    Storage::disk('public')->assertMissing('items/' . $files[1]->hashName());
});

function saveMultipleFiles(ModelResource $resource, Model $item): array
{
    $file1 = UploadedFile::fake()->create('test1.csv');
    $file2 = UploadedFile::fake()->create('test2.csv');

    $data = ['files' => [$file1, $file2]];

    asAdmin()->put(
        $resource->route('crud.update', $item->getKey()),
        $data
    )
        ->assertRedirect();

    $item->refresh();

    expect($item->files->toArray())
        ->toBe(['items/'.$file1->hashName(), 'items/'.$file2->hashName()])
    ;

    Storage::disk('public')->assertExists('items/' . $file1->hashName());
    Storage::disk('public')->assertExists('items/' . $file2->hashName());

    return [$file1, $file2];
}