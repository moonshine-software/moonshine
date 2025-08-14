<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Fieldset;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Text;

uses()->group('fields');
uses()->group('fieldset');

beforeEach(function () {
    $this->field = Fieldset::make('Fieldset', [
        Text::make('Text'),
        Json::make('Json')->fields([
            Text::make('Text'),
            Json::make('Object')->fields([
                Fieldset::make('test', [
                    Text::make('Text'),
                ]),
                Json::make('OV')->onlyValue(),
            ])->object(),
        ]),
    ]);

    $this->data = [
        'text' => 'Text',
        'json' => [
            [
                'text' => 'JsonText',
                'object' => [
                    'text' => 'ObjectText',
                    'ov' => [
                        'OV_value1',
                        'OV_value2',
                    ],
                ],
            ],
        ],
    ];

    $this->form = FormBuilder::make()
        ->fields([$this->field])
        ->fill($this->data);
});

it('fill', function () {
    /** @var Fieldset $field */
    $field = $this->form->getPreparedFields()->first();

    expect($field->getValue()->toArray())
        ->toBe($this->data)
        ->and($field->getPreparedFields()->findByColumn('text')->getValue())
        ->toBe('Text')
        ->and($field->getPreparedFields()->findByColumn('json')->getValue())
        ->toBe($this->data['json'])
    ;
});

it('render', function () {
    expect((string) $this->field->fill($this->data)->render())
        ->toContain('<input', 'Fieldset', 'Text', 'JsonText', 'ObjectText', 'OV_value1', 'OV_value2');
});

it('preview', function () {
    expect((string) $this->field->fill($this->data)->previewMode()->render())
        ->toContain('Text', 'JsonText', 'ObjectText', 'OV_value1', 'OV_value2')
        ->not->toContain('Fieldset', '<input')
    ;
});

it('unwrap', function () {
    expect($this->form->getFields()->onlyFields(false, true)->first())
        ->toBeInstanceOf(Fieldset::class);
});

it('raw', function () {
    /** @var Fieldset $field */
    $field = $this->form->getPreparedFields()->first();

    expect($field->toRawValue())
        ->toBe($this->data)
        ->and($field->getPreparedFields()->findByColumn('text')->toRawValue())
        ->toBe('Text')
        ->and($field->getPreparedFields()->findByColumn('json')->toRawValue())
        ->toBe(json_encode($this->data['json']))
    ;
});

it('extract labels', function () {
    expect($this->form->getPreparedFields()->onlyFields()->extractLabels())
    ->toBe([
        "text" => "Text",
        "json" => "Json",
    ]);
});

it('apply', function () {
    $data = [
        'text' => 'Text',
        'json' => [
            [
                'text' => 'JsonText',
                'object' => [
                    'text' => 'ObjectText',
                    'ov' => [
                        ['value' => 'OV_value1'],
                        ['value' => 'OV_value2'],
                    ],
                ],
            ],
        ],
    ];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->fieldApply($this->field),
            new class () extends Model {}
        )
    )
        ->toBeInstanceOf(Model::class)
        ->getAttributes()
        ->toBe($this->data)
    ;
});

it('custom apply', function () {
    $data = [
        'text' => 'Text',
        'json' => [
            [
                'text' => 'JsonText',
                'object' => [
                    'text' => 'ObjectText',
                    'ov' => [
                        ['value' => 'OV_value1'],
                        ['value' => 'OV_value2'],
                    ],
                ],
            ],
        ],
    ];

    fakeRequest(parameters: $data);

    expect(
        $this->field->onApply(fn (Model $model) => $model->setAttribute('data', 'test'))->apply(
            TestResourceBuilder::new()->fieldApply($this->field),
            new class () extends Model {}
        )
    )
        ->toBeInstanceOf(Model::class)
        ->getAttributes()
        ->toBe(['data' => 'test'])
    ;
});
