<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Fields\Json;
use MoonShine\Fields\Text;

uses()->group('fields');
uses()->group('json-field');

uses()->group('test');

beforeEach(function (): void {
    $this->field = Json::make('Json')
        ->fields(exampleFields()->toArray());

    $this->fieldKeyValue = Json::make('Key value')
        ->keyValue();

    $this->fieldOnlyValue = Json::make('Only value')
        ->onlyValue();

    $this->item = new class () extends Model {
        public array $key_value = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        public array $only_value = [
            'value1',
            'value2',
        ];

        public array $json = [
            ['field1' => 'field1_value', 'field2' => 'field2_value'],
        ];
    };

    $this->field->resolveFill(['json' =>[
        'field1' => 'field1_value',
        'field2' => 'field2_value',
    ]], $this->item);

    $this->fieldKeyValue->resolveFill(['key_value' =>[
        'key1' => 'value1',
        'key2' => 'value2',
    ]], $this->item);

    $this->fieldOnlyValue->resolveFill(['only_value' =>[
        'value1',
        'value2',
    ]], $this->item);
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
        ->toBeInstanceOf(RemovableContract::class)
        ->toBeInstanceOf(HasDefaultValue::class)
        ->toBeInstanceOf(DefaultCanBeArray::class)
    ;
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

it('has fields', function (): void {
    expect($this->field->getFields())
        ->hasFields(exampleFields()->toArray())
        ->each(function ($field, $key): void {
            $key++;

            $field->toBeInstanceOf(Text::class)
                ->name()
                ->toBe('field'.$key)
                ->id()
                ->toBe('field'.$key);
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
                ->toBe($name)
                ->id()
                ->toBe($name);
        });
});

// TODO Json values tests
