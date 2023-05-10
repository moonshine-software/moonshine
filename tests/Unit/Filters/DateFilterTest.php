<?php

use MoonShine\Filters\DateFilter;
use MoonShine\Filters\TextFilter;

uses()->group('filters');

beforeEach(function () {
    $this->filter = DateFilter::make('Date');
});

it('text filter is parent', function () {
    expect($this->filter)
        ->toBeInstanceOf(TextFilter::class);
});

it('type', function () {
    expect($this->filter->type())
        ->toBe('date');
});

it('view', function () {
    expect($this->filter->getView())
        ->toBe('moonshine::filters.date');
});

it('default format', function () {
    expect($this->filter->getFormat())
        ->toBe('Y-m-d H:i:s');
});

it('change format', function () {
    $this->filter->format('d.m.Y');

    expect($this->filter->getFormat())
        ->toBe('d.m.Y');
});

it('with time', function () {
    $this->filter->withTime();

    expect($this->filter->type())
        ->toBe('datetime-local');
});
