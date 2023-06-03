<?php

use MoonShine\Filters\DateFilter;
use MoonShine\Filters\DateRangeFilter;

uses()->group('filters');

beforeEach(function (): void {
    $this->filter = DateRangeFilter::make('Date');
});

it('date filter is parent', function (): void {
    expect($this->filter)
        ->toBeInstanceOf(DateFilter::class);
});

it('type', function (): void {
    expect($this->filter->type())
        ->toBe('date');
});

it('view', function (): void {
    expect($this->filter->getView())
        ->toBe('moonshine::filters.date-range');
});

it('is group', function (): void {
    expect($this->filter->isGroup())
        ->toBeTrue();
});

it('names', function (): void {
    expect($this->filter)
        ->name()
        ->toBe('filters[date][]')
        ->name('from')
        ->toBe('filters[date][from]')
        ->name('to')
        ->toBe('filters[date][to]');
});
