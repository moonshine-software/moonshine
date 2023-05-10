<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Code;
use MoonShine\Fields\Textarea;

uses()->group('fields');

beforeEach(function () {
    $this->field = Code::make('Code');
    $this->item = new class () extends Model {
        public string $code = 'echo 1;';
    };
});

it('textarea is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(Textarea::class);
});

it('type', function () {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.code');
});

it('methods', function () {
    expect($this->field->language('js')->lineNumbers())
        ->language
        ->toBe('js')
        ->lineNumbers
        ->toBeTrue();
});
