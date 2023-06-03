<?php

use MoonShine\Fields\ID;
use MoonShine\Fields\Text;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = ID::make();
});

it('text is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('hidden');
});
