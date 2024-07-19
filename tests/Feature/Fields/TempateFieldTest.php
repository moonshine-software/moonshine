<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResource;

use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Template;

uses()->group('fields');
uses()->group('template-field');

beforeEach(function () {
    $this->item = createItem(countComments: 0);

    expect($this->item->data)
        ->toBeEmpty();
});

function testTemplateValue(TestResource $resource, Item $item, array $data, ?array $expectedData = null)
{
    asAdmin()->put(
        $resource->getRoute('crud.update', $item->getKey()),
        [
            'data' => $data,
        ]
    )->assertRedirect();

    $item->refresh();

    expect($item->data->toArray())->toBe($expectedData ?? $data);
}

function baseTemplateSaveFile(Item $item, ?UploadedFile $changedFile = null, ?array $changedData = null): void
{
    $resource = addFieldsToTestResource(
        Template::make('Data')
            ->fields([
                File::make('File'),
            ])
            ->changeFill(fn (Item $data, Template $field) => data_get($data, $field->getColumn(), []))
            ->onApply(onApply: function (Item $item, $value, Template $field) {
                $column = $field->getColumn();
                $applyValues = [];

                foreach ($field->getPreparedFields() as $f) {
                    $apply = $f->apply(
                        fn ($data): mixed => data_set($data, $f->getColumn(), $value[$f->getColumn()] ?? ''),
                        $value
                    );

                    data_set(
                        $applyValues,
                        $f->getColumn(),
                        data_get($apply, $f->getColumn())
                    );
                }

                return data_set(
                    $item,
                    $column,
                    $applyValues
                );
            })
    );

    $file = $changedFile ?? UploadedFile::fake()->create('test.csv');

    $data = $changedData ?? ['file' => $file];

    testTemplateValue($resource, $item, $data, ['file' => $file->hashName()]);
}

function baseTemplateIterableSaveFile(Item $item, ?UploadedFile $changedFile = null, ?array $changedData = null): void
{
    $resource = addFieldsToTestResource(
        Template::make('Data')
            ->fields([
                File::make('File'),
            ])
            ->changeFill(fn (Item $data, Template $field) => data_get($data, $field->getColumn(), []))
            ->onApply(onApply: function (Item $item, $values, Template $field) {
                $column = $field->getColumn();
                $applyValues = [];

                foreach ($values as $index => $value) {
                    foreach ($field->getFields()->prepareReindex($field) as $f) {
                        $f->setNameIndex($index);

                        $apply = $f->apply(
                            fn ($data): mixed => data_set($data, $f->getColumn(), $value[$f->getColumn()] ?? ''),
                            $value
                        );

                        data_set(
                            $applyValues[$index],
                            $f->getColumn(),
                            data_get($apply, $f->getColumn())
                        );
                    }
                }

                return data_set(
                    $item,
                    $column,
                    $applyValues
                );
            })
    );

    $file = $changedFile ?? UploadedFile::fake()->create('test.csv');

    $data = $changedData ?? [
        ['file' => $file],
    ];

    testTemplateValue($resource, $item, $data, [
        ['file' => $file->hashName()],
    ]);
}

it('apply as base with file', function () {
    baseTemplateSaveFile($this->item);
});

it('apply as base with file stay hidden', function () {
    baseTemplateSaveFile($this->item);

    $this->item->refresh();

    $file = UploadedFile::fake()->create('test.csv');

    $data = ['hidden_file' => $file->hashName()];

    baseTemplateSaveFile($this->item, $file, $data);
});

it('apply iterable as base with file', function () {
    baseTemplateIterableSaveFile($this->item);
});

it('apply iterable as base with file stay hidden', function () {
    baseTemplateIterableSaveFile($this->item);

    $this->item->refresh();

    $file = UploadedFile::fake()->create('test.csv');

    $data = [
        ['hidden_file' => $file->hashName()],
    ];

    baseTemplateIterableSaveFile($this->item, $file, $data);
});
