<?php

use MoonShine\Filters\DateFilter;
use MoonShine\Filters\DateRangeFilter;

uses()->group('filters');

beforeEach(function () {
    $this->filter = DateRangeFilter::make('Date');
});

it('date filter is parent', function () {
    expect($this->filter)
        ->toBeInstanceOf(DateFilter::class);
});

it('type', function () {
    expect($this->filter->type())
        ->toBe('date');
});

it('view', function () {
    expect($this->filter->getView())
        ->toBe('moonshine::filters.date-range');
});

it('is group', function () {
    expect($this->filter->isGroup())
        ->toBeTrue();
});

it('names', function () {
    expect($this->filter)
        ->name()
        ->toBe('filters[date][]')
        ->name('from')
        ->toBe('filters[date][from]')
        ->name('to')
        ->toBe('filters[date][to]');
});
