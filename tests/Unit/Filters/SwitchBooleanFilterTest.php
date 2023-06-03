<?php

use MoonShine\Filters\Filter;
use MoonShine\Filters\SwitchBooleanFilter;

uses()->group('filters');

beforeEach(function (): void {
    $this->filter = SwitchBooleanFilter::make('Active');
});

it('filter is parent', function (): void {
    expect($this->filter)
        ->toBeInstanceOf(Filter::class);
});

it('type', function (): void {
    expect($this->filter->type())
        ->toBe('checkbox');
});

it('view', function (): void {
    expect($this->filter->getView())
        ->toBe('moonshine::filters.switch');
});

it('on/off values', function (): void {
    expect($this->filter->onValue('yes')->offValue('no'))
        ->getOnValue()
        ->toBe('yes')
        ->getOffValue()
        ->toBe('no');
});
