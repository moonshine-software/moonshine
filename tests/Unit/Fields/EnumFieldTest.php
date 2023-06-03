<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Enum;
use MoonShine\Fields\Select;
use MoonShine\Tests\Fixtures\Enums\TestEnumColor;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Enum::make('Enum')
        ->attach(TestEnumColor::class);

    $this->item = new class () extends Model {
        public TestEnumColor $enum = TestEnumColor::Red;

        protected $casts = [
            'enum' => TestEnumColor::class,
        ];
    };
});

it('select field is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Select::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.select');
});

it('index view value', function (): void {
    expect($this->field->indexViewValue($this->item))
        ->toBe('Red');
});
