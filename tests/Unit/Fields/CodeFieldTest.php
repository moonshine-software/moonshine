<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Code;
use MoonShine\Fields\Textarea;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Code::make('Code');
    $this->item = new class () extends Model {
        public string $code = 'echo 1;';
    };
});

it('textarea is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Textarea::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.code');
});

it('methods', function (): void {
    expect($this->field->language('js')->lineNumbers())
        ->language
        ->toBe('js')
        ->lineNumbers
        ->toBeTrue();
});
