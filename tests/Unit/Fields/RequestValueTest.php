<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Text;

uses()->group('request-values');

beforeEach(function () {
    $this->item = new class () extends Model {
        public mixed $value;
    };
});

expect()->extend('requestAndFormViewValues', function (Model $item, mixed $requestValue, mixed $formViewValue) {
    expect($this->value)
        ->requestValue()
        ->toBe($requestValue)
        ->formViewValue($item)
        ->toBe($formViewValue);
});

it('default value level', function () {
    $field = Text::make('Value');

    expect($field)
        ->requestAndFormViewValues($this->item, false, null);

    $field = Text::make('Value')
        ->default('Testing');

    expect($field)
        ->requestAndFormViewValues($this->item, 'Testing', 'Testing');
});

it('request value level', function () {
    $field = Text::make('Value')
        ->default('Testing');

    fakeRequest('/', 'POST', ['value' => 'A']);

    $this->item->value = 'B';

    expect($field)
        ->requestAndFormViewValues($this->item, 'A', 'B');
});

it('item value level', function () {
    $field = Text::make('Value')
        ->default('Testing');

    fakeRequest('/', 'POST', ['value' => 'A']);

    $this->item->value = 'B';

    expect($field)
        ->requestAndFormViewValues($this->item, 'A', 'B');

    $this->item->value = null;

    fakeRequest('/', 'POST', []);

    expect($field)
        ->requestAndFormViewValues($this->item, 'Testing', 'Testing');
});
