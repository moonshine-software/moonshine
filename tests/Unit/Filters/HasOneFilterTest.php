<?php

use MoonShine\Filters\HasOneFilter;
use MoonShine\Filters\SelectFilter;

uses()->group('filters');
uses()->group('relation-filters');

beforeEach(function () {
    $this->filter = HasOneFilter::make('Has one');
});

it('select filter is parent', function () {
    expect($this->filter)
        ->toBeInstanceOf(SelectFilter::class);
});
