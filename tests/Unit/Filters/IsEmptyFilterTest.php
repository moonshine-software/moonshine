<?php

use MoonShine\Filters\IsEmptyFilter;
use MoonShine\Filters\SwitchBooleanFilter;

uses()->group('filters');

beforeEach(function (): void {
    $this->filter = IsEmptyFilter::make('Empty');
});

it('switch boolean filter is parent', function (): void {
    expect($this->filter)
        ->toBeInstanceOf(SwitchBooleanFilter::class);
});

it('correct name', function (): void {
    expect($this->filter->name())
        ->toBe('filters[is_empty_empty]');
});
