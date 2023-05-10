<?php

use MoonShine\Filters\BelongsToFilter;
use MoonShine\Filters\SelectFilter;

uses()->group('filters');
uses()->group('relation-filters');

beforeEach(function () {
    $this->filter = BelongsToFilter::make('Belongs to');
});

it('select filter is parent', function () {
    expect($this->filter)
        ->toBeInstanceOf(SelectFilter::class);
});
