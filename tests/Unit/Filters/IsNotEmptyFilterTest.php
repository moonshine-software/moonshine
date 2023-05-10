<?php

use MoonShine\Filters\IsNotEmptyFilter;
use MoonShine\Filters\SwitchBooleanFilter;

uses()->group('filters');

beforeEach(function () {
    $this->filter = IsNotEmptyFilter::make('Not Empty');
});

it('switch boolean filter is parent', function () {
    expect($this->filter)
        ->toBeInstanceOf(SwitchBooleanFilter::class);
});

it('correct name', function () {
    expect($this->filter->name())
        ->toBe('filters[is_not_empty_not_empty]');
});

