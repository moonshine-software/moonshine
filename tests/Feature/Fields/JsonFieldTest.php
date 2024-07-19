<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MoonShine\Laravel\Applies\Filters\RepeaterModelApply;
use MoonShine\Laravel\Fields\Relationships\RelationRepeater;
use MoonShine\Laravel\Handlers\ExportHandler;
use MoonShine\Laravel\Handlers\ImportHandler;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Text;

use function Pest\Laravel\get;

uses()->group('fields');
uses()->group('json-field');

beforeEach(function () {
    $this->item = createItem(countComments: 0);

    expect($this->item->data)
        ->toBeEmpty();
});

function testJsonValue(TestResource $resource, Item $item, array $data, ?array $expectedData = null)
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

it('apply as base with file', function () {
    $file = UploadedFile::fake()->create('test.csv');

    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Text::make('Title'),
            Text::make('Value'),
            File::make('File'),
        ])
    );

    $data = [
        'data' => [
            ['title' => 'Title 1', 'value' => 'Value 1', 'file' => $file],
            ['title' => 'Title 2', 'value' => 'Value 2', 'file' => $file],
        ]
    ];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        $data
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->data->toArray())->toBe(
        [
            ['file' => $file->hashName(), 'title' => 'Title 1', 'value' => 'Value 1',],
            ['file' => $file->hashName(), 'title' => 'Title 2', 'value' => 'Value 2',],
        ]
    );
});

it('apply as base with file stay hidden', function () {
    $file = UploadedFile::fake()->create('test.csv');

    $this->item->data = [
        ['title' => 'Title 1', 'value' => 'Value 1', 'file' => $file->hashName()],
        ['title' => 'Title 2', 'value' => 'Value 2'],
    ];

    $this->item->save();

    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Text::make('Title'),
            Text::make('Value'),
            File::make('File'),
        ])
    );

    $data = [
        'data' => [
            ['title' => 'Title 1', 'value' => 'Value 1'],
            ['title' => 'Title 2', 'value' => 'Value 2', 'hidden_file' => $file->hashName()],
        ]
    ];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        $data
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->data->toArray())->toBe(
        [
            ['file' => null, 'title' => 'Title 1', 'value' => 'Value 1'],
            ['file' => $file->hashName(), 'title' => 'Title 2', 'value' => 'Value 2'],
        ]
    );
});

it('apply as base', function () {
    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Text::make('Title'),
            Text::make('Value'),
        ])
    );

    $data = [
        ['title' => 'Title 1', 'value' => 'Value 1'],
        ['title' => 'Title 2', 'value' => 'Value 2'],
    ];

    testJsonValue($resource, $this->item, $data);
});

it('apply as base with default', function () {
    $data = [
        ['title' => 'Title 1', 'value' => 'Value 1'],
        ['title' => 'Title 2', 'value' => 'Value 2'],
    ];

    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Text::make('Title'),
            Text::make('Value'),
        ])->default($data)
    );

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey())
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->data->toArray())->toBe($data);
});

it('apply as key value', function () {
    $resource = addFieldsToTestResource(
        Json::make('Data')->keyValue()
    );

    $data = [
        ['key' => 'Title 1', 'value' => 'Value 1'],
        ['key' => 'Title 2', 'value' => 'Value 2'],
    ];

    testJsonValue($resource, $this->item, $data, ['Title 1' => 'Value 1', 'Title 2' => 'Value 2']);
});

it('apply as only value', function () {
    $resource = addFieldsToTestResource(
        Json::make('Data')->onlyValue()
    );

    $data = [
        ['value' => 'Value 1'],
        ['value' => 'Value 2'],
    ];

    testJsonValue($resource, $this->item, $data, ['Value 1', 'Value 2']);
});

it('apply as relation', function () {
    $resource = addFieldsToTestResource(
        RelationRepeater::make('Comments', resource: TestCommentResource::class)
    );

    $data = [
        ['id' => '', 'content' => 'Test', 'user_id' => 1],
    ];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        [
            'comments' => $data,
        ]
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->comments->first())
        ->content
        ->toBe('Test');

    $data = [
        ['id' => $this->item->comments->first()->getKey(), 'content' => 'Test 2', 'user_id' => 1],
    ];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        [
            'comments' => $data,
        ]
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->comments->first())
        ->content
        ->toBe('Test 2');
});

it('apply as filter', function (): void {
    $field = Json::make('Json')
        ->fields(exampleFields()->toArray())
        ->wrapName('filters');

    $query = Item::query();

    get('/?filters[json][0][title]=test');

    $field
        ->onApply((new RepeaterModelApply())->apply($field))
        ->apply(
            static fn (Builder $query) => $query,
            $query
        );

    expect($query->toRawSql())
        ->toContain('json_contains');
});

function jsonExport(Item $item): ?string
{
    $data = [
        ['title' => 'Title 1', 'value' => 'Value 1'],
        ['title' => 'Title 2', 'value' => 'Value 2'],
    ];

    $item->data = $data;
    $item->save();

    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Text::make('Title'),
            Text::make('Value'),
        ]),
        'exportFields'
    );

    $export = ExportHandler::make('');

    asAdmin()->get(
        $resource->getRoute('handler', query: ['handlerUri' => $export->getUriKey()])
    )->assertDownload();

    $file = Storage::disk('public')->get('test-resource.csv');

    expect($file)
        ->toContain('Title 1', 'Title 2', 'Value 1', 'Value 2');

    return $file;
}
it('export', function (): void {
    jsonExport($this->item);
});

it('import', function (): void {
    $data = [
        ['title' => 'Title 1', 'value' => 'Value 1'],
        ['title' => 'Title 2', 'value' => 'Value 2'],
    ];

    $file = jsonExport($this->item);

    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Text::make('Title'),
            Text::make('Value'),
        ]),
        'importFields'
    );

    $import = ImportHandler::make('');

    asAdmin()->post(
        $resource->getRoute('handler', query: ['handlerUri' => $import->getUriKey()]),
        [$import->getInputName() => $file]
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->data->toArray())
        ->toBe($data);

});
