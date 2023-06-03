<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasFullPageMode;
use MoonShine\Contracts\Fields\HasJsonValues;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Fields\Json;
use MoonShine\Fields\Text;

uses()->group('fields');
uses()->group('json-field');

beforeEach(function (): void {
    $this->field = Json::make('Json')
        ->fields(exampleFields()->toArray());
    $this->fieldKeyValue = Json::make('Key value')
        ->keyValue();
    $this->item = new class () extends Model {
        public array $key_value = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        public array $json = [
            ['field1' => 'field1_value', 'field2' => 'field2_value'],
        ];
    };
});

it('names', function (): void {
    expect($this->field)
        ->name()
        ->toBe('json[]')
        ->name('1')
        ->toBe('json[1]');
});

it('correct interfaces', function (): void {
    expect($this->field)
        ->toBeInstanceOf(HasFields::class)
        ->toBeInstanceOf(HasJsonValues::class)
        ->toBeInstanceOf(RemovableContract::class)
        ->toBeInstanceOf(HasFullPageMode::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.json');
});

it('is group', function (): void {
    expect($this->field->isGroup())
        ->toBeTrue();
});

it('removable methods', function (): void {
    expect($this->field)
        ->isRemovable()
        ->toBeFalse()
        ->and($this->field->removable())
        ->isRemovable()
        ->toBeTrue();
});

it('full page mode', function (): void {
    expect($this->field)
        ->isFullPage()
        ->toBeFalse()
        ->and($this->field->fullPage())
        ->isFullPage()
        ->toBeTrue();
});

it('has fields', function (): void {
    expect($this->field->getFields())
        ->hasFields(exampleFields()->toArray())
        ->each(function ($field, $key): void {
            $key++;

            $field->toBeInstanceOf(Text::class)
                ->name()
                ->toBe('json[${index0}][field' . $key . ']')
                ->id()
                ->toBe('json_field' . $key);
        });
});

it('has fields key value', function (): void {
    expect($this->fieldKeyValue->getFields())
        ->hasFields([
            Text::make('Key2'),
            Text::make('Value2'),
        ])
        ->each(function ($field, $key): void {
            $name = $key === 0 ? 'key' : 'value';

            $field->toBeInstanceOf(Text::class)
                ->name()
                ->toBe('key_value[${index0}][' . $name . ']')
                ->id()
                ->toBe('key_value_' . $name);
        });
});

it('json values', function (): void {
    expect($this->field->jsonValues())
        ->toBeArray()
        ->toBe(
            exampleFields()
                ->mapWithKeys(fn ($f) => [$f->field() => ''])
                ->toArray()
        )
        ->and($this->field->jsonValues($this->item))
            ->toBe($this->item->json)
    ;
});

it('json values key value', function (): void {
    expect($this->fieldKeyValue->jsonValues())
        ->toBeArray()
        ->toBe(
            exampleFields()
                ->mapWithKeys(fn ($f, $k) => [$k === 0 ? 'key' : 'value' => ''])
                ->toArray()
        )
        ->and($this->fieldKeyValue->jsonValues($this->item))
        ->toBe([
            ['key' => 'key1', 'value' => 'value1'],
            ['key' => 'key2', 'value' => 'value2'],
        ])
    ;
});
