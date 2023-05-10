<?php

use MoonShine\Filters\Filter;
use MoonShine\Filters\SwitchBooleanFilter;

uses()->group('filters');

beforeEach(function () {
    $this->filter = SwitchBooleanFilter::make('Active');
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
        ->toBe('moonshine::filters.switch');
});

it('on/off values', function () {
    expect($this->filter->onValue('yes')->offValue('no'))
        ->getOnValue()
        ->toBe('yes')
        ->getOffValue()
        ->toBe('no');
});
