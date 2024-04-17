<?php

declare(strict_types=1);

use MoonShine\Fields\Hidden;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Hidden::make('contractId');
});

it('default', function (): void {
    expect($this->field->label())
        ->toBe('contractId')
        ->and($this->field->column())
        ->toBe('contractId');

    $field = Hidden::make(column: 'contractId');

    expect($field->label())
        ->toBe('')
        ->and($field->column())
        ->toBe('contractId');

    $field = Hidden::make('Contact id', 'contractid');

    expect($field->label())
        ->toBe('Contact id')
        ->and($field->column())
        ->toBe('contractid');

    $field = Hidden::make('Contact id', 'contractid')->setColumn('new');

    expect($field->label())
        ->toBe('Contact id')
        ->and($field->column())
        ->toBe('new');
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('hidden');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.hidden');
});
