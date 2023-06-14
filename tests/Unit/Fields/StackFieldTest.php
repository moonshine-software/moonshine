<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\StackFields;
use MoonShine\Fields\Text;

uses()->group('fields');
uses()->group('stack-fields');

beforeEach(function (): void {
    $this->field = StackFields::make('Email')->fields([
        Text::make('text_1'),
        Text::make('text_2'),
    ]);

    $this->item = new class () extends Model {
        public string $text_1 = 'Text 1';
        public string $text_2 = 'Text 2';
    };
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.stack');
});

it('has fields', function (): void {
    expect($this->field->getFields())
        ->each->toBeInstanceOf(Text::class);
});

it('index view value', function (): void {
    expect($this->field->indexViewValue($this->item))
        ->toContain('Text 1', 'Text 2');
});

it('save', function (): void {
    fakeRequest(parameters: [
        'text_1' => 'New value 1',
        'text_2' => 'New value 2',
    ]);

    expect($this->field->save($this->item))
        ->text_1
        ->toBe('New value 1')
        ->text_2
        ->toBe('New value 2');
});
