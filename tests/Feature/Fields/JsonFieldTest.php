<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\Laravel\Applies\Filters\JsonModelApply;
use MoonShine\Laravel\Fields\Relationships\RelationRepeater;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Fields\Fieldset;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Select;
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
        ],
    ];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        $data
    )->assertRedirect();

    $this->item->refresh();

    $savedData = $this->item->data->toArray();

    expect($savedData)
        ->toHaveCount(2)
        ->and($savedData[0])->toMatchArray(['title' => 'Title 1', 'value' => 'Value 1', 'file' => $file->hashName()])
        ->and($savedData[1])->toMatchArray(['title' => 'Title 2', 'value' => 'Value 2', 'file' => $file->hashName()]);
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
        ],
    ];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        $data
    )->assertRedirect();

    $this->item->refresh();

    $savedData = $this->item->data->toArray();

    expect($savedData)
        ->toHaveCount(2)
        ->and($savedData[0])->toMatchArray(['title' => 'Title 1', 'value' => 'Value 1', 'file' => null])
        ->and($savedData[1])->toMatchArray(['title' => 'Title 2', 'value' => 'Value 2', 'file' => $file->hashName()]);
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

it('apply as object', function () {
    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Text::make('Title'),
            Json::make('Inner data')->fields([
                Text::make('Inner Title'),
                Json::make('Only value')->onlyValue(),
            ])->object(),
        ])->object()
    );

    $data = [
        'title' => 'Value',
        'inner_data' => [
            'inner_title' => 'Inner Value',
            'only_value' => [
                ['value' => 'value1'],
                ['value' => 'value2'],
            ],
        ],
    ];

    testJsonValue($resource, $this->item, $data, [
        'title' => 'Value',
        'inner_data' => [
            'only_value' => [
                'value1',
                'value2',
            ],
            'inner_title' => 'Inner Value',
        ],
    ]);
})->todo('Changed sort of inner_title');

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
        ->wrapName('filter');

    $query = Item::query();

    get('/?filter[json][0][title]=test');

    $field
        ->onApply((new JsonModelApply())->apply($field))
        ->apply(
            static fn (Builder $query) => $query,
            $query
        );

    $sql = strtolower($query->toRawSql());

    expect(str_contains($sql, 'json_contains') || str_contains($sql, 'json_each'))
        ->toBeTrue();
});

it('renders preview with nested layout fields', function (): void {
    $field = Json::make('Data')->fields([
        Text::make('Title'),

        Json::make('Object')->fields([
            Text::make('Title'),
            Text::make('Value'),

            Json::make('Inner')->fields([
                Fieldset::make('fieldset', [
                    Flex::make([
                        Text::make('One'),
                        Text::make('Two'),
                    ]),
                ]),

                Select::make('Multiple')->options([1 => 1, 2 => 2, 3 => 3])->multiple(),

                Json::make('KV')->keyValue(),
            ])->object(),
        ])->object(),
    ]);

    $html = (string) $field
        ->previewMode()
        ->fillData([
            'data' => [
                [
                    'title' => 'Title',
                    'object' => [
                        'title' => 'Title',
                        'value' => 'Value',
                        'inner' => [
                            'one' => 'One',
                            'two' => 'Two',
                            'multiple' => [1, 2],
                            'kv' => [
                                'key 1' => 'value 1',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

    expect($html)
        ->toContain('fieldset')
        ->toContain('One')
        ->toContain('Two');
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
