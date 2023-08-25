<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Checkbox;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Checkbox::make('Active');
    $this->item = new class () extends Model {
        public bool $active = true;
    };
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('checkbox');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.checkbox');
});

it('preview', function (): void {
    expect((string) $this->field)
        ->toBe(view('moonshine::fields.checkbox', ['element' => $this->field])->render());
});

it('correct is checked value', function (): void {

    $this->field->resolveFill(['active' => true]);

    expect($this->field->isChecked())
        ->toBeTrue();
});

it('on/off values', function (): void {
    expect($this->field->onValue('yes')->offValue('no'))
        ->getOnValue()
        ->toBe('yes')
        ->getOffValue()
        ->toBe('no');
});
