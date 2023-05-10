<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Select;

uses()->group('fields');

beforeEach(function () {
    $this->selectOptions = [
        0 => 1,
        1 => 2,
        2 => 3,
    ];

    $this->field = Select::make('Select')->options($this->selectOptions);

    $this->fieldMultiple = Select::make('Select multiple')
        ->options($this->selectOptions)
        ->multiple();
    $this->item = new class () extends Model {
        public int $select = 1;
        public array $select_multiple = [1];

        protected $casts = [
            'select_multiple' => 'json',
        ];
    };
});

it('type', function () {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.select');
});

it('index view value', function () {
    expect($this->field->indexViewValue($this->item))
        ->toBe('2')
        ->and($this->fieldMultiple->indexViewValue($this->item))
        ->toBe(view('moonshine::ui.badge', [
            'color' => 'purple',
            'value' => '2',
        ])->render());
});

it('multiple', function () {
    expect($this->field->isMultiple())
        ->toBeFalse()
        ->and($this->fieldMultiple->isMultiple())
        ->toBeTrue();
});

it('searchable', function () {
    expect($this->fieldMultiple)
        ->isSearchable()
        ->toBeFalse()
        ->and($this->fieldMultiple->searchable())
        ->isSearchable()
        ->toBeTrue();
});

it('options', function () {
    expect($this->fieldMultiple)
        ->values()
        ->toBe($this->selectOptions);
});

it('is selected correctly', function () {
    expect($this->fieldMultiple)
        ->isSelected($this->item, '1')
        ->toBeTrue();
});

it('is selected invalid', function () {
    expect($this->fieldMultiple)
        ->isSelected($this->item, '2')
        ->toBeFalse();
});

it('names single', function () {
    expect($this->field)
        ->name()
        ->toBe('select')
        ->name('1')
        ->toBe('select');
});

it('names multiple', function () {
    expect($this->fieldMultiple)
        ->name()
        ->toBe('select_multiple[]')
        ->name('1')
        ->toBe('select_multiple[1]');
});
