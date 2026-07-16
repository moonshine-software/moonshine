<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\Laravel\Applies\Filters\JsonModelApply;
use MoonShine\Laravel\Fields\Relationships\RelationRepeater;
use MoonShine\Laravel\Http\Requests\MoonShineFormRequest;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

use function Pest\Laravel\get;

uses()->group('fields');
uses()->group('json-field');

beforeEach(function (): void {
    $this->item = createItem(countComments: 0);

    expect($this->item->data)
        ->toBeEmpty();
});

it('applies json rows with file uploads', function (): void {
    $file = UploadedFile::fake()->create('test.csv');

    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Text::make('Title'),
            Text::make('Value'),
            File::make('File'),
        ]),
    );

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        [
            'data' => [
                ['title' => 'Title 1', 'value' => 'Value 1', 'file' => $file],
                ['title' => 'Title 2', 'value' => 'Value 2', 'file' => $file],
            ],
        ],
    )->assertRedirect();

    $this->item->refresh();

    $savedData = $this->item->data->toArray();

    expect($savedData)
        ->toHaveCount(2)
        ->and($savedData[0])->toMatchArray(['title' => 'Title 1', 'value' => 'Value 1', 'file' => $file->hashName()])
        ->and($savedData[1])->toMatchArray(['title' => 'Title 2', 'value' => 'Value 2', 'file' => $file->hashName()]);
});

it('keeps hidden file values in json rows', function (): void {
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
        ]),
    );

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        [
            'data' => [
                ['title' => 'Title 1', 'value' => 'Value 1'],
                ['title' => 'Title 2', 'value' => 'Value 2', 'hidden_file' => $file->hashName()],
            ],
        ],
    )->assertRedirect();

    $this->item->refresh();

    $savedData = $this->item->data->toArray();

    expect($savedData)
        ->toHaveCount(2)
        ->and($savedData[0])->toMatchArray(['title' => 'Title 1', 'value' => 'Value 1', 'file' => null])
        ->and($savedData[1])->toMatchArray(['title' => 'Title 2', 'value' => 'Value 2', 'file' => $file->hashName()]);
});

function updateJsonValue(TestResource $resource, Item $item, array|string $data): void
{
    asAdmin()->put(
        $resource->getRoute('crud.update', $item->getKey()),
        ['data' => $data],
    )->assertRedirect();

    $item->refresh();
}

function assertJsonValue(TestResource $resource, Item $item, array|string $data, array $expected): void
{
    updateJsonValue($resource, $item, $data);

    expect($item->data->toArray())
        ->toBe($expected);
}

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
        'exportFields',
    );

    $export = ExportHandler::make('');

    asAdmin()->get(
        $resource->getRoute('handler', query: ['handlerUri' => $export->getUriKey()]),
    );

    Storage::disk('public')->assertExists('test-resource.csv');

    $file = Storage::disk('public')->get('test-resource.csv');

    expect($file)
        ->toContain('Title 1', 'Title 2', 'Value 1', 'Value 2');

    return $file;
}

it('applies json rows from array input', function (): void {
    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Text::make('Title'),
            Text::make('Value'),
        ]),
    );

    updateJsonValue($resource, $this->item, [
        ['title' => 'Title 1', 'value' => 'Value 1'],
        ['title' => 'Title 2', 'value' => 'Value 2'],
    ]);

    expect($this->item->data->toArray())
        ->toBe([
            ['title' => 'Title 1', 'value' => 'Value 1'],
            ['title' => 'Title 2', 'value' => 'Value 2'],
        ]);
});

it('applies json rows from encoded payload', function (): void {
    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Select::make('Key')
                ->options(['vk' => 'VK', 'email' => 'E-mail']),
            Text::make('Value'),
        ]),
    );

    updateJsonValue($resource, $this->item, json_encode([
        ['key' => 'vk', 'value' => '111'],
        ['key' => 'email', 'value' => '222'],
    ], JSON_THROW_ON_ERROR));

    expect($this->item->data->toArray())
        ->toBe([
            ['key' => 'vk', 'value' => '111'],
            ['key' => 'email', 'value' => '222'],
        ]);
});

it('uses default json rows when request value is missing', function (): void {
    $default = [
        ['title' => 'Title 1', 'value' => 'Value 1'],
    ];

    $resource = addFieldsToTestResource(
        Json::make('Data')
            ->fields([
                Text::make('Title'),
                Text::make('Value'),
            ])
            ->default($default),
    );

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->data->toArray())
        ->toBe($default);
});

it('applies key value json payloads', function (): void {
    $resource = addFieldsToTestResource(
        Json::make('Data')->keyValue(),
    );

    assertJsonValue($resource, $this->item, [
        ['key' => 'Title 1', 'value' => 'Value 1'],
        ['key' => 'Title 2', 'value' => 'Value 2'],
    ], [
        'Title 1' => 'Value 1',
        'Title 2' => 'Value 2',
    ]);

    assertJsonValue($resource, $this->item, json_encode([
        'Title 3' => 'Value 3',
    ], JSON_THROW_ON_ERROR), [
        'Title 3' => 'Value 3',
    ]);
});

it('applies only value json payloads', function (): void {
    $resource = addFieldsToTestResource(
        Json::make('Data')->onlyValue(),
    );

    assertJsonValue($resource, $this->item, [
        ['value' => 'Value 1'],
        ['value' => 'Value 2'],
    ], [
        'Value 1',
        'Value 2',
    ]);

    assertJsonValue($resource, $this->item, json_encode([
        'Value 3',
        'Value 4',
    ], JSON_THROW_ON_ERROR), [
        'Value 3',
        'Value 4',
    ]);
});

it('applies nested object json payloads', function (): void {
    $resource = addFieldsToTestResource(
        Json::make('Data')->fields([
            Text::make('Title'),
            Json::make('Inner data')->fields([
                Text::make('Inner Title'),
                Json::make('Only value')->onlyValue(),
            ])->object(),
        ])->object(),
    );

    assertJsonValue($resource, $this->item, [
        'title' => 'Value',
        'inner_data' => [
            'inner_title' => 'Inner Value',
            'only_value' => [
                ['value' => 'value1'],
                ['value' => 'value2'],
            ],
        ],
    ], [
        'title' => 'Value',
        'inner_data' => [
            'inner_title' => 'Inner Value',
            'only_value' => [
                'value1',
                'value2',
            ],
        ],
    ]);
});

it('applies relation repeater rows after json view split', function (): void {
    $resource = addFieldsToTestResource(
        RelationRepeater::make('Comments', resource: TestCommentResource::class),
    );

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        [
            'comments' => [
                ['id' => '', 'content' => 'Test', 'user_id' => 1],
            ],
        ],
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->comments->first())
        ->content
        ->toBe('Test');

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        [
            'comments' => [
                ['id' => $this->item->comments->first()->getKey(), 'content' => 'Test 2', 'user_id' => 1],
            ],
        ],
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->comments->first())
        ->content
        ->toBe('Test 2');
});

it('applies json as filter', function (): void {
    $field = Json::make('Json')
        ->fields(exampleFields()->toArray())
        ->wrapName('filter');

    $query = Item::query();

    get('/?filter[json][0][title]=test');

    $field
        ->onApply((new JsonModelApply())->apply($field))
        ->apply(
            static fn (Builder $query): Builder => $query,
            $query,
        );

    $sql = strtolower($query->toRawSql());

    expect(str_contains($sql, 'json_contains') || str_contains($sql, 'json_each'))
        ->toBeTrue();
});

it('exports json fields', function (): void {
    jsonExport($this->item);
});

it('imports json fields', function (): void {
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
        'importFields',
    );

    $import = ImportHandler::make('');

    asAdmin()->post(
        $resource->getRoute('handler', query: ['handlerUri' => $import->getUriKey()]),
        [$import->getInputName() => $file],
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->data->toArray())
        ->toBe($data);
});

it('decodes nested json payloads for validation', function (): void {
    $payload = [
        'comments' => [
            [
                'meta' => json_encode([
                    ['key' => 'status', 'value' => 'published'],
                ], JSON_THROW_ON_ERROR),
            ],
        ],
    ];

    $request = new MoonShineFormRequest();

    $method = new ReflectionMethod($request, 'decodeJsonFieldPayload');
    $method->invokeArgs($request, [&$payload, ['comments', '${index0}', 'meta']]);

    expect($payload)
        ->toBe([
            'comments' => [
                [
                    'meta' => [
                        ['key' => 'status', 'value' => 'published'],
                    ],
                ],
            ],
        ]);
});
