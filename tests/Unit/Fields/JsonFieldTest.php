<?php

declare(strict_types=1);

use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeArray;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

uses()->group('fields');
uses()->group('json-field');

function jsonViewData(Json $field): array
{
    $reflection = new ReflectionMethod($field, 'viewData');

    return $reflection->invoke($field);
}

it('has the expected contracts', function (): void {
    expect(Json::make('Json'))
        ->toBeInstanceOf(HasFieldsContract::class)
        ->toBeInstanceOf(HasDefaultValueContract::class)
        ->toBeInstanceOf(CanBeArray::class);
});

it('normalizes filled json rows for fields', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ])
        ->fill('[{"title":"A","value":"111"},{"title":"B","value":"222"}]');

    expect($field->toValue())
        ->toBe([
            ['title' => 'A', 'value' => '111'],
            ['title' => 'B', 'value' => '222'],
        ])
        ->and(jsonViewData($field)['rows'])
        ->toBe([
            ['title' => 'A', 'value' => '111'],
            ['title' => 'B', 'value' => '222'],
        ]);
});

it('builds field schema from fields', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Select::make('Key')
                ->options(['vk' => 'VK', 'email' => 'E-mail']),
            Text::make('Value')->horizontal(),
        ]);

    expect(jsonViewData($field)['fields'])
        ->toMatchArray([
            [
                'column' => 'key',
                'label' => 'Key',
                'type' => 'select',
                'options' => [
                    ['value' => 'vk', 'label' => 'VK'],
                    ['value' => 'email', 'label' => 'E-mail'],
                ],
            ],
            [
                'column' => 'value',
                'label' => 'Value',
                'type' => 'text',
                'wrapperClass' => 'form-group-inline',
            ],
        ]);
});

it('allows removing rows by default', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ]);

    expect($field->isRemovable())
        ->toBeTrue()
        ->and(jsonViewData($field)['removable'])
        ->toBeTrue();
});

it('uses horizontal orientation by default', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ]);

    expect($field->getOrientation())
        ->toBe('horizontal')
        ->and(jsonViewData($field)['orientation'])
        ->toBe('horizontal');
});

it('can use vertical fields orientation', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ], orientation: 'vertical');

    expect($field->getOrientation())
        ->toBe('vertical')
        ->and(jsonViewData($field)['orientation'])
        ->toBe('vertical');
});

it('can use vertical orientation shortcut', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ])
        ->vertical();

    expect($field->getOrientation())
        ->toBe('vertical')
        ->and(jsonViewData($field)['orientation'])
        ->toBe('vertical');
});

it('can disable vertical orientation shortcut', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ], orientation: 'vertical')
        ->vertical(false);

    expect($field->getOrientation())
        ->toBe('horizontal')
        ->and(jsonViewData($field)['orientation'])
        ->toBe('horizontal');
});

it('can disable removing rows', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ])
        ->removable(false);

    expect($field->isRemovable())
        ->toBeFalse()
        ->and(jsonViewData($field)['removable'])
        ->toBeFalse();
});

it('can disable creating rows in filter mode', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ]);

    expect($field->isCreatable())
        ->toBeTrue()
        ->and($field->filterMode())
        ->toBe($field)
        ->and($field->isCreatable())
        ->toBeFalse()
        ->and(jsonViewData($field)['creatable'])
        ->toBeFalse();
});

it('can configure creating rows', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ])
        ->creatable(limit: 6, button: ActionButton::make('New')->primary());

    expect($field->isCreatable())
        ->toBeTrue()
        ->and($field->getCreatableLimit())
        ->toBe(6)
        ->and(jsonViewData($field))
        ->toMatchArray([
            'creatable' => true,
            'creatableLimit' => 6,
        ])
        ->and(jsonViewData($field)['createButton'])
        ->toContain('New')
        ->toContain('add()');
});

it('can hide create button without disabling creating rows', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ])
        ->creatable(limit: 6, hideButton: true);

    expect($field->isCreatable())
        ->toBeTrue()
        ->and($field->isCreateButtonHidden())
        ->toBeTrue()
        ->and(jsonViewData($field))
        ->toMatchArray([
            'creatable' => true,
            'creatableLimit' => 6,
            'hideCreateButton' => true,
        ]);
});

it('can pass remove button attributes', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ])
        ->removable(attributes: ['@click.prevent' => 'customAsyncRemove']);

    expect($field->getRemoveButtonAttributes())
        ->toBe(['@click.prevent' => 'customAsyncRemove'])
        ->and(jsonViewData($field)['removeButtonAttributes'])
        ->toBe(['@click.prevent' => 'customAsyncRemove']);
});

it('can override row buttons', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ])
        ->buttons([
            ActionButton::make('')
                ->icon('trash')
                ->onClick(fn(): string => 'remove()', 'prevent')
                ->secondary()
                ->showInLine(),
        ]);

    expect(jsonViewData($field)['buttons'])
        ->toContain('remove(rowIndex)')
        ->toContain('trash');
});

it('can modify remove button', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ])
        ->modifyRemoveButton(
            fn(ActionButton $button): ActionButton => $button->customAttributes([
                'class' => 'btn-secondary',
            ])
        );

    expect(jsonViewData($field)['removeButton'])
        ->toContain('btn-secondary')
        ->toContain('remove(rowIndex)');
});

it('can modify create button', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ])
        ->creatable()
        ->modifyCreateButton(
            fn(ActionButton $button): ActionButton => $button->customAttributes([
                'class' => 'btn-primary',
            ])
        );

    expect(jsonViewData($field)['createButton'])
        ->toContain('btn-primary')
        ->toContain('add()');
});

it('passes nested modified remove button to field schema', function (): void {
    $field = Json::make('Products', 'products')
        ->fields([
            Text::make('Name'),
            Json::make('Links')
                ->fields([
                    Text::make('Label'),
                    Text::make('Url'),
                ])
                ->modifyRemoveButton(
                    fn(ActionButton $button): ActionButton => $button->customAttributes([
                        'class' => 'btn-secondary',
                    ])
                ),
        ]);

    expect(jsonViewData($field)['fields'][1]['removeButton'])
        ->toContain('btn-secondary')
        ->toContain('__moonshine_json_remove__');
});

it('passes nested creatable state to field schema', function (): void {
    $field = Json::make('Products', 'products')
        ->fields([
            Text::make('Name'),
            Json::make('Links')
                ->fields([
                    Text::make('Label'),
                    Text::make('Url'),
                ])
                ->creatable(limit: 2, button: ActionButton::make('New link'))
                ->buttons([
                    ActionButton::make('')
                        ->icon('trash')
                        ->onClick(fn(): string => 'remove()', 'prevent')
                        ->secondary()
                        ->showInLine(),
                ]),
        ]);

    expect(jsonViewData($field)['fields'][1])
        ->toMatchArray([
            'column' => 'links',
            'type' => 'json',
            'creatable' => true,
            'creatableLimit' => 2,
        ]);

    expect(jsonViewData($field)['fields'][1]['createButton'])
        ->toContain('New link')
        ->toContain('__moonshine_json_add__');

    expect(jsonViewData($field)['fields'][1]['buttons'])
        ->toContain('__moonshine_json_remove__')
        ->toContain('trash');
});

it('passes nested hidden create button state to field schema', function (): void {
    $field = Json::make('Products', 'products')
        ->fields([
            Text::make('Name'),
            Json::make('Links')
                ->fields([
                    Text::make('Label'),
                    Text::make('Url'),
                ])
                ->creatable(limit: 2, hideButton: true),
        ]);

    expect(jsonViewData($field)['fields'][1])
        ->toMatchArray([
            'column' => 'links',
            'type' => 'json',
            'creatable' => true,
            'creatableLimit' => 2,
            'hideCreateButton' => true,
        ]);
});

it('does not allow reordering rows by default', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ]);

    expect($field->isReorderable())
        ->toBeFalse()
        ->and(jsonViewData($field)['reorderable'])
        ->toBeFalse();
});

it('can allow reordering rows', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ])
        ->reorderable();

    expect($field->isReorderable())
        ->toBeTrue()
        ->and(jsonViewData($field)['reorderable'])
        ->toBeTrue();
});

it('can show custom empty message', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Json::make('Links')
                ->fields([
                    Text::make('Label'),
                    Text::make('Url'),
                ])
                ->emptyMessage('No links added'),
        ])
        ->emptyMessage('No options added');

    expect($field->getEmptyMessage())
        ->toBe('No options added')
        ->and(jsonViewData($field)['emptyMessage'])
        ->toBe('No options added')
        ->and(jsonViewData($field)['fields'][1])
        ->toMatchArray([
            'column' => 'links',
            'type' => 'json',
            'emptyMessage' => 'No links added',
        ]);
});

it('normalizes key value object for fields', function (): void {
    $field = Json::make('Contacts', 'contacts')
        ->keyValue(
            keyField: Select::make('Key')
                ->options(['vk' => 'VK', 'email' => 'E-mail']),
            valueField: Text::make('Value'),
        )
        ->fill('{"vk":"aaa","email":"ddd"}');

    expect($field->toValue())
        ->toBe([
            ['key' => 'vk', 'value' => 'aaa'],
            ['key' => 'email', 'value' => 'ddd'],
        ])
        ->and(jsonViewData($field)['keyValue'])
        ->toBeTrue()
        ->and(jsonViewData($field)['rows'])
        ->toBe([
            ['key' => 'vk', 'value' => 'aaa'],
            ['key' => 'email', 'value' => 'ddd'],
        ]);
});

it('can use default key value fields', function (): void {
    $field = Json::make('KV', 'kv')
        ->keyValue()
        ->fill('{"foo":"aaa","bar":"sss"}');

    expect($field->toValue())
        ->toBe([
            ['key' => 'foo', 'value' => 'aaa'],
            ['key' => 'bar', 'value' => 'sss'],
        ])
        ->and(jsonViewData($field)['fields'])
        ->toMatchArray([
            [
                'column' => 'key',
                'label' => 'Key',
                'type' => 'text',
            ],
            [
                'column' => 'value',
                'label' => 'Value',
                'type' => 'text',
            ],
        ])
        ->and($field->prepareOnApply([
            ['key' => 'foo', 'value' => '111'],
            ['key' => 'bar', 'value' => '222'],
        ]))
        ->toBe([
            'foo' => '111',
            'bar' => '222',
        ]);
});

it('normalizes object value for fields', function (): void {
    $field = Json::make('Data', 'data')
        ->fields([
            Text::make('Title'),
            Checkbox::make('Active'),
        ])
        ->object()
        ->fill('{"title":"Title","active":false}');

    expect($field->isObject())
        ->toBeTrue()
        ->and($field->toValue())
        ->toBe([
            ['title' => 'Title', 'active' => false],
        ])
        ->and(jsonViewData($field)['objectMode'])
        ->toBeTrue()
        ->and(jsonViewData($field)['rows'])
        ->toBe([
            ['title' => 'Title', 'active' => false],
        ]);
});

it('does not create empty object rows without value', function (): void {
    $field = Json::make('Data', 'data')
        ->fields([
            Text::make('Title'),
            Checkbox::make('Active'),
        ])
        ->object();

    expect($field->toValue())
        ->toBe([])
        ->and(jsonViewData($field)['rows'])
        ->toBe([]);
});

it('keeps switcher field type for rendering', function (): void {
    $field = Json::make('Data', 'data')
        ->fields([
            Switcher::make('Active'),
        ]);

    expect(jsonViewData($field)['fields'][0])
        ->toMatchArray([
            'column' => 'active',
            'type' => 'switcher',
        ])
        ->and($field->prepareOnApply([
            ['active' => '1'],
        ]))
        ->toBe([
            ['active' => true],
        ]);
});

it('prepares object row as object for apply', function (): void {
    $field = Json::make('Data', 'data')
        ->fields([
            Text::make('Title'),
            Checkbox::make('Active'),
        ])
        ->object();

    expect($field->prepareOnApply([
        ['title' => 'Title', 'active' => true],
    ]))->toBe([
        'title' => 'Title',
        'active' => true,
    ]);
});

it('filters empty object row for apply', function (): void {
    $field = Json::make('Data', 'data')
        ->fields([
            Text::make('Title'),
        ])
        ->object();

    expect($field->prepareOnApply([
        ['title' => ''],
    ]))->toBe([]);
});

it('allows removable and reorderable actions in object mode', function (): void {
    $field = Json::make('Data', 'data')
        ->fields([
            Text::make('Title'),
            Checkbox::make('Active'),
        ])
        ->object()
        ->removable()
        ->reorderable();

    expect($field->isRemovable())
        ->toBeTrue()
        ->and($field->isReorderable())
        ->toBeTrue()
        ->and(jsonViewData($field)['removable'])
        ->toBeTrue()
        ->and(jsonViewData($field)['reorderable'])
        ->toBeTrue();
});

it('prepares multiple object rows as rows', function (): void {
    $field = Json::make('Data', 'data')
        ->fields([
            Text::make('Title'),
            Checkbox::make('Active'),
        ])
        ->object();

    expect($field->prepareOnApply([
        ['title' => 'First', 'active' => true],
        ['title' => 'Second', 'active' => false],
    ]))->toBe([
        ['title' => 'First', 'active' => true],
        ['title' => 'Second', 'active' => false],
    ]);
});

it('normalizes nested json object for fields', function (): void {
    $field = Json::make('Products', 'products')
        ->fields([
            Text::make('Name', 'name'),
            Json::make('Prices', 'prices')
                ->fields([
                    Number::make('Wholesale price', 'wholesale_price'),
                    Number::make('Retail price', 'retail_price'),
                ])
                ->object(),
        ])
        ->fill('[{"name":"product 1","prices":{"wholesale_price":1000,"retail_price":1200}}]');

    expect($field->toValue())
        ->toBe([
            [
                'name' => 'product 1',
                'prices' => [
                    [
                        'wholesale_price' => 1000,
                        'retail_price' => 1200,
                    ],
                ],
            ],
        ])
        ->and(jsonViewData($field)['fields'][1])
        ->toMatchArray([
            'column' => 'prices',
            'type' => 'json',
            'objectMode' => true,
            'removable' => true,
            'reorderable' => false,
        ]);
});

it('prepares nested json object for apply', function (): void {
    $field = Json::make('Products', 'products')
        ->fields([
            Text::make('Name', 'name'),
            Json::make('Prices', 'prices')
                ->fields([
                    Number::make('Wholesale price', 'wholesale_price'),
                    Number::make('Retail price', 'retail_price'),
                ])
                ->object(),
        ]);

    expect($field->prepareOnApply([
        [
            'name' => 'product 1',
            'prices' => [
                [
                    'wholesale_price' => '1000',
                    'retail_price' => '1200',
                ],
            ],
        ],
    ]))->toBe([
        [
            'name' => 'product 1',
            'prices' => [
                'wholesale_price' => 1000,
                'retail_price' => 1200,
            ],
        ],
    ]);
});

it('uses first select option for empty nested non nullable select value', function (): void {
    $field = Json::make('Items', 'items')
        ->fields([
            Text::make('One', 'one'),
            Json::make('Example', 'example')
                ->fields([
                    Text::make('Two', 'two'),
                    Select::make('City', 'city_id')
                        ->options([
                            'Italy' => [
                                1 => 'Rome',
                                2 => 'Milan',
                            ],
                            'France' => [
                                3 => 'Paris',
                                4 => 'Marseille',
                            ],
                        ]),
                ])
                ->object(),
        ]);

    expect($field->prepareOnApply([
        [
            'one' => '66',
            'example' => [
                [
                    'two' => '44',
                    'city_id' => '',
                ],
            ],
        ],
    ]))->toBe([
        [
            'one' => '66',
            'example' => [
                'two' => '44',
                'city_id' => '1',
            ],
            ],
        ]);
});

it('renders key value preview as key labels with values', function (): void {
    $preview = Json::make('Contacts', 'contacts')
        ->keyValue(
            keyField: Select::make('Key')
                ->options([
                    'vk' => 'VK',
                    'email' => 'E-mail',
                ]),
            valueField: Text::make('Value'),
        )
        ->previewMode()
        ->fill([
            'vk' => 'Lorem',
            'email' => 'Ipsum',
        ])
        ->preview();

    expect($preview)
        ->toContain('VK')
        ->toContain('Lorem')
        ->toContain('E-mail')
        ->toContain('Ipsum')
        ->not->toContain('>Key<')
        ->not->toContain('>Value<');
});

it('renders key value table preview as key columns with values', function (): void {
    $preview = Json::make('Contacts', 'contacts')
        ->keyValue()
        ->table()
        ->fill([
            'title' => '111',
            'status' => '222',
        ])
        ->preview();

    expect($preview)
        ->toContain('<th>')
        ->toContain('Title')
        ->toContain('Status')
        ->toContain('111')
        ->toContain('222')
        ->not->toContain('>title<')
        ->not->toContain('>status<')
        ->not->toContain('>Key<')
        ->not->toContain('>Value<');
});

it('renders key value preview from compatible fields rows', function (): void {
    $preview = Json::make('Contacts preview', 'contacts')
        ->keyValue(
            keyField: Select::make('Key')
                ->options([
                    'vk' => 'VK',
                    'email' => 'E-mail',
                ]),
            valueField: Text::make('Value'),
        )
        ->previewMode()
        ->fill([
            ['key_2' => 'vk', 'value_2' => 'Lorem'],
            ['key_2' => 'email', 'value_2' => 'Ipsum'],
        ])
        ->preview();

    expect($preview)
        ->toContain('VK')
        ->toContain('Lorem')
        ->toContain('E-mail')
        ->toContain('Ipsum')
        ->not->toContain('>Key<')
        ->not->toContain('>Value<');
});

it('renders key value table preview from compatible fields rows', function (): void {
    $preview = Json::make('Contacts preview', 'contacts')
        ->keyValue(
            keyField: Select::make('Key')
                ->options([
                    'vk' => 'VK',
                    'email' => 'E-mail',
                ]),
            valueField: Text::make('Value'),
        )
        ->table()
        ->fill([
            ['key_2' => 'vk', 'value_2' => 'Lorem'],
            ['key_2' => 'email', 'value_2' => 'Ipsum'],
        ])
        ->preview();

    expect($preview)
        ->toContain('<th>')
        ->toContain('VK')
        ->toContain('E-mail')
        ->toContain('Lorem')
        ->toContain('Ipsum')
        ->not->toContain('>Key<')
        ->not->toContain('>Value<');
});

it('renders key value payload with configured field labels in preview', function (): void {
    $preview = Json::make('Settings', 'table_of_contents')
        ->fields([
            Text::make('Title'),
            Text::make('Status'),
        ])
        ->previewMode()
        ->fill([
            ['key' => 'title', 'value' => '111'],
            ['key' => 'status', 'value' => '222'],
        ])
        ->preview();

    expect($preview)
        ->toContain('Title')
        ->toContain('111')
        ->toContain('Status')
        ->toContain('222')
        ->not->toContain('>Key<')
        ->not->toContain('>Value<');
});

it('renders object preview as readonly fields', function (): void {
    $preview = Json::make('Data', 'data')
        ->fields([
            Text::make('Title'),
            Select::make('Status')
                ->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                ]),
            Switcher::make('Active'),
        ])
        ->object()
        ->previewMode()
        ->fill([
            'title' => 'Object title',
            'status' => 'published',
            'active' => true,
        ])
        ->preview();

    expect($preview)
        ->toContain('<label class="form-label">')
        ->toContain('Data')
        ->toContain('grid grid-cols-12 gap-2')
        ->toContain('form-group form-group-inline')
        ->toContain('xl:col-span-2')
        ->toContain('xl:col-span-10')
        ->toContain('Title')
        ->toContain('Object title')
        ->toContain('Status')
        ->toContain('Published')
        ->toContain('flex min-h-5 items-center')
        ->toContain('bg-green-500');
});

it('renders nested preview recursively', function (): void {
    $preview = Json::make('Products', 'data')
        ->fields([
            Text::make('Name'),
            Json::make('Prices')
                ->fields([
                    Number::make('Wholesale price'),
                    Number::make('Retail price'),
                    Switcher::make('Tax included'),
                ])
                ->object(),
            Json::make('Links')
                ->fields([
                    Text::make('Label'),
                    Text::make('Url'),
                ]),
        ])
        ->previewMode()
        ->fill([
            [
                'name' => 'Product 1',
                'prices' => [
                    'wholesale_price' => 1000,
                    'retail_price' => 1200,
                    'tax_included' => true,
                ],
                'links' => [
                    ['label' => 'Docs', 'url' => 'https://moonshine-laravel.com'],
                    ['label' => 'GitHub', 'url' => 'https://github.com/moonshine-software'],
                ],
            ],
        ])
        ->preview();

    expect($preview)
        ->toContain('grid grid-cols-12 gap-2')
        ->toContain('Product 1')
        ->toContain('Wholesale price')
        ->toContain('1000')
        ->toContain('Links')
        ->toContain('Docs')
        ->toContain('GitHub')
        ->toContain('class="divider"')
        ->toContain('bg-green-500');
});

it('can enable table preview mode', function (): void {
    $field = Json::make('Products', 'data')
        ->fields([
            Text::make('Name'),
            Json::make('Links')
                ->fields([
                    Text::make('Label'),
                    Text::make('Url'),
                ])
                ->table(),
        ])
        ->table();

    expect($field->isTable())
        ->toBeTrue()
        ->and($field->isPreviewMode())
        ->toBeTrue()
        ->and(jsonViewData($field)['fields'][1])
        ->toMatchArray([
            'column' => 'links',
            'type' => 'json',
            'tableMode' => true,
        ]);
});

it('renders table mode as readonly preview', function (): void {
    $render = Json::make('Products', 'data')
        ->fields([
            Text::make('Name'),
        ])
        ->table()
        ->fill([
            ['name' => 'Product 1'],
        ])
        ->render();

    expect($render)
        ->toContain('<label class="form-label">')
        ->toContain('Products')
        ->toContain('table table-list')
        ->toContain('Product 1')
        ->not->toContain('json-field__rows')
        ->not->toContain('jsonTreeField');
});

it('renders table preview when enabled', function (): void {
    $preview = Json::make('Products', 'data')
        ->fields([
            Text::make('Name'),
            Json::make('Prices')
                ->fields([
                    Number::make('Wholesale price'),
                    Number::make('Retail price'),
                ])
                ->object(),
            Json::make('Links')
                ->fields([
                    Text::make('Label'),
                    Text::make('Url'),
                ]),
        ])
        ->table()
        ->previewMode()
        ->fill([
            [
                'name' => 'Product 1',
                'prices' => [
                    'wholesale_price' => 1000,
                    'retail_price' => 1200,
                ],
                'links' => [
                    ['label' => 'Docs', 'url' => 'https://moonshine-laravel.com'],
                ],
            ],
        ])
        ->preview();

    expect($preview)
        ->toContain('<label class="form-label">')
        ->toContain('Products')
        ->toContain('table table-list')
        ->toContain('<th>')
        ->toContain('Name')
        ->toContain('Prices')
        ->toContain('Links')
        ->toContain('Product 1')
        ->toContain('Docs');
});

it('can modify table preview', function (): void {
    $preview = Json::make('Products', 'data')
        ->fields([
            Text::make('Name'),
        ])
        ->table()
        ->modifyTable(
            fn(TableBuilder $table, bool $preview): TableBuilder => $table
                ->customAttributes([
                    'style' => 'width: 20%;',
                    'data-preview' => $preview ? 'true' : 'false',
                ])
                ->trAttributes(fn(): array => ['style' => 'background: red'])
                ->tdAttributes(fn(): array => ['style' => 'background: blue'])
                ->simple()
                ->sticky()
        )
        ->previewMode()
        ->fill([
            ['name' => 'Product 1'],
        ])
        ->preview();

    expect($preview)
        ->toContain('class="table"')
        ->toContain('style="width: 20%;"')
        ->toContain('data-preview="true"')
        ->toContain('<tr style="background: red')
        ->toContain('<td style="background: blue')
        ->toContain('table-sticky')
        ->not->toContain('table table-list');
});

it('renders nested json table preview when enabled', function (): void {
    $preview = Json::make('Products', 'data')
        ->fields([
            Text::make('Name'),
            Json::make('Links')
                ->fields([
                    Text::make('Label'),
                    Text::make('Url'),
                ])
                ->table(),
        ])
        ->previewMode()
        ->fill([
            [
                'name' => 'Product 1',
                'links' => [
                    ['label' => 'Docs', 'url' => 'https://moonshine-laravel.com'],
                ],
            ],
        ])
        ->preview();

    expect($preview)
        ->toContain('table table-list')
        ->toContain('Label')
        ->toContain('Url')
        ->toContain('Docs')
        ->toContain('https://moonshine-laravel.com');
});

it('can modify nested table preview', function (): void {
    $preview = Json::make('Products', 'data')
        ->fields([
            Text::make('Name'),
            Json::make('Links')
                ->fields([
                    Text::make('Label'),
                ])
                ->table()
                ->modifyTable(
                    fn(TableBuilder $table): TableBuilder => $table
                        ->customAttributes([
                            'data-json-nested' => 'links',
                        ])
                        ->trAttributes(fn(): array => ['style' => 'background: red'])
                        ->tdAttributes(fn(): array => ['style' => 'background: blue'])
                        ->simple()
                ),
        ])
        ->previewMode()
        ->fill([
            [
                'name' => 'Product 1',
                'links' => [
                    ['label' => 'Docs'],
                ],
            ],
        ])
        ->preview();

    expect($preview)
        ->toContain('data-json-nested="links"')
        ->toContain('<tr style="background: red')
        ->toContain('<td style="background: blue')
        ->not->toContain('table table-list');
});

it('prepares key value rows as object for apply', function (): void {
    $field = Json::make('Contacts', 'contacts')
        ->keyValue(
            keyField: Select::make('Key')
                ->options(['vk' => 'VK', 'email' => 'E-mail']),
            valueField: Text::make('Value'),
        );

    expect($field->prepareOnApply([
        ['key' => 'vk', 'value' => '111'],
        ['key' => 'email', 'value' => '222'],
    ]))->toBe([
        'vk' => '111',
        'email' => '222',
    ]);
});

it('keeps positional key value fields support', function (): void {
    $field = Json::make('Contacts', 'contacts')
        ->keyValue(
            Select::make('Key')->options(['vk' => 'VK']),
            Text::make('Value'),
        );

    expect(jsonViewData($field)['fields'])
        ->toMatchArray([
            [
                'column' => 'key',
                'type' => 'select',
            ],
            [
                'column' => 'value',
                'type' => 'text',
            ],
        ]);
});

it('normalizes only value array for fields', function (): void {
    $field = Json::make('Tags', 'tags')
        ->onlyValue()
        ->fill('["aaa","bbb"]');

    expect($field->toValue())
        ->toBe([
            ['value' => 'aaa'],
            ['value' => 'bbb'],
        ])
        ->and(jsonViewData($field)['onlyValue'])
        ->toBeTrue()
        ->and(jsonViewData($field)['rows'])
        ->toBe([
            ['value' => 'aaa'],
            ['value' => 'bbb'],
        ]);
});

it('prepares only value rows as array for apply', function (): void {
    $field = Json::make('Tags', 'tags')
        ->onlyValue();

    expect($field->prepareOnApply([
        ['value' => '111'],
        ['value' => '222'],
    ]))->toBe([
        '111',
        '222',
    ]);
});

it('can use custom only value field', function (): void {
    $field = Json::make('Tags', 'tags')
        ->onlyValue(
            value: 'Type',
            valueField: Select::make('Type')
                ->options(['email' => 'E-mail', 'vk' => 'VK']),
        );

    expect(jsonViewData($field)['fields'])
        ->toMatchArray([
            [
                'column' => 'type',
                'label' => 'Type',
                'type' => 'select',
                'options' => [
                    ['value' => 'email', 'label' => 'E-mail'],
                    ['value' => 'vk', 'label' => 'VK'],
                ],
            ],
        ]);
});

it('prepares rows for apply', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ]);

    expect($field->prepareOnApply([
        ['title' => 'A', 'value' => '111'],
        ['title' => 'B', 'value' => '222'],
    ]))->toBe([
        ['title' => 'A', 'value' => '111'],
        ['title' => 'B', 'value' => '222'],
    ]);
});

it('keeps empty rows payload empty', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ]);

    expect($field->prepareOnApply([]))
        ->toBe([])
        ->and($field->fill('[]')->toValue())
        ->toBe([]);
});

it('prepares a single associative row for apply', function (): void {
    $field = Json::make('Product Options', 'column_name')
        ->fields([
            Text::make('Title'),
            Text::make('Value'),
        ]);

    expect($field->prepareOnApply(['title' => 'A', 'value' => 'B']))
        ->toBe([
            ['title' => 'A', 'value' => 'B'],
        ]);
});
