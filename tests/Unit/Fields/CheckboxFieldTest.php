<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Checkbox;

uses()->group('fields');

beforeEach(function () {
    $this->field = Checkbox::make('Active');
    $this->item = new class extends Model {
        public bool $active = true;
    };
});

it('type', function () {
    expect($this->field->type())
        ->toBe('checkbox');
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.checkbox');
});

it('index view value', function () {
    expect($this->field->indexViewValue($this->item))
        ->toBe(
            view('moonshine::ui.boolean', ['value' => true])->render()
        );
});

it('correct is checked value', function () {
    expect($this->field->isChecked($this->item, true))
        ->toBeTrue();
});

it('on/off values', function () {
    expect($this->field->onValue('yes')->offValue('no'))
        ->getOnValue()
        ->toBe('yes')
        ->getOffValue()
        ->toBe('no');
});

