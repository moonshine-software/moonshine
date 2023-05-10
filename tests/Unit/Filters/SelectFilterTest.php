<?php

use MoonShine\Filters\Filter;
use MoonShine\Filters\SelectFilter;

uses()->group('filters');

beforeEach(function () {
    $this->selectOptions = [
        0 => 1,
        1 => 2,
        2 => 3,
    ];

    $this->filter = SelectFilter::make('Select')
        ->options($this->selectOptions);
});

it('filter is parent', function () {
    expect($this->filter)
        ->toBeInstanceOf(Filter::class);
});

it('type', function () {
    expect($this->filter->type())
        ->toBeEmpty();
});

it('view', function () {
    expect($this->filter->getView())
        ->toBe('moonshine::filters.select');
});

it('multiple', function () {
    expect($this->filter->isMultiple())
        ->toBeFalse()
        ->and($this->filter->multiple()->isMultiple())
        ->toBeTrue();
});

it('searchable', function () {
    expect($this->filter)
        ->isSearchable()
        ->toBeFalse()
        ->and($this->filter->searchable())
        ->isSearchable()
        ->toBeTrue();
});

it('options', function () {
    expect($this->filter)
        ->values()
        ->toBe($this->selectOptions);
});

it('names single', function () {
    expect($this->filter)
        ->name()
        ->toBe('filters[select]')
        ->name('1')
        ->toBe('filters[select]');
});

it('names multiple', function () {
    expect($this->filter->multiple())
        ->name()
        ->toBe('filters[select][]')
        ->name('1')
        ->toBe('filters[select][1]');
});
