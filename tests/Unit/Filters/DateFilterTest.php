<?php

use MoonShine\Filters\DateFilter;
use MoonShine\Filters\TextFilter;

uses()->group('filters');

beforeEach(function (): void {
    $this->filter = DateFilter::make('Date');
});

it('text filter is parent', function (): void {
    expect($this->filter)
        ->toBeInstanceOf(TextFilter::class);
});

it('type', function (): void {
    expect($this->filter->type())
        ->toBe('date');
});

it('view', function (): void {
    expect($this->filter->getView())
        ->toBe('moonshine::filters.date');
});

it('default format', function (): void {
    expect($this->filter->getFormat())
        ->toBe('Y-m-d H:i:s');
});

it('change format', function (): void {
    $this->filter->format('d.m.Y');

    expect($this->filter->getFormat())
        ->toBe('d.m.Y');
});

it('with time', function (): void {
    $this->filter->withTime();

    expect($this->filter->type())
        ->toBe('datetime-local');
});
