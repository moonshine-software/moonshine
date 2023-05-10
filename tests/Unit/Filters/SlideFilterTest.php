<?php

use MoonShine\Filters\Filter;
use MoonShine\Filters\SlideFilter;

uses()->group('filters');

beforeEach(function () {
    $this->filter = SlideFilter::make('Slide')
        ->toField('to')
        ->fromField('from');
});

it('filter is parent', function () {
    expect($this->filter)
        ->toBeInstanceOf(Filter::class);
});

it('type', function () {
    expect($this->filter->type())
        ->toBe('number');
});

it('view', function () {
    expect($this->filter->getView())
        ->toBe('moonshine::filters.slide');
});

it('number methods', function () {
    expect($this->filter)
        ->min(3)
        ->min->toBe(3)
        ->getAttribute('min')
        ->toBe(3)
        ->max(6)
        ->max->toBe(6)
        ->getAttribute('max')
        ->toBe(6)
        ->step(2)
        ->step->toBe(2)
        ->getAttribute('step')
        ->toBe(2)
    ;
});
