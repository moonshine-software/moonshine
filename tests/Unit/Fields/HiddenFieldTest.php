<?php

declare(strict_types=1);

use MoonShine\Fields\Hidden;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Hidden::make('contractId');
});

it('default', function (): void {
    expect($this->field->getLabel())
        ->toBe('contractId')
        ->and($this->field->getColumn())
        ->toBe('contractId');

    $field = Hidden::make(column: 'contractId');

    expect($field->getLabel())
        ->toBe('')
        ->and($field->getColumn())
        ->toBe('contractId');

    $field = Hidden::make('Contact id', 'contractid');

    expect($field->getLabel())
        ->toBe('Contact id')
        ->and($field->getColumn())
        ->toBe('contractid');

    $field = Hidden::make('Contact id', 'contractid')->setColumn('new');

    expect($field->getLabel())
        ->toBe('Contact id')
        ->and($field->getColumn())
        ->toBe('new');
});

it('type', function (): void {
    expect($this->field->attributes()->get('type'))
        ->toBe('hidden');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.hidden');
});
