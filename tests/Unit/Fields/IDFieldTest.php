<?php

use MoonShine\Fields\ID;
use MoonShine\Fields\Text;

uses()->group('fields');

beforeEach(function () {
    $this->field = ID::make();
});

it('text is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function () {
    expect($this->field->type())
        ->toBe('hidden');
});
