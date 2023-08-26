<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\SwitchBoolean;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = SwitchBoolean::make('Active');
    $this->item = new class () extends Model {
        public bool $active = true;
    };

    fillFromModel($this->field, $this->item);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('checkbox');
});

it('checkbox is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Checkbox::class);
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.switch');
});

it('preview with not auto update', function (): void {

    expect($this->field->autoUpdate(null, false)->preview())
        ->toBe(
            view('moonshine::fields.switch', [
                'element' => $this->field,
            ])->render()
        );
});

it('preview with auto update', function (): void {
    expect($this->field->preview())
        ->toBe(
            view('moonshine::fields.switch', [
                'element' => $this->field,
                'autoUpdate' => true,
                'item' => $this->item,
            ])->render()
        );
});
