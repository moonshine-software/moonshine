<?php

use MoonShine\Attributes\SearchUsingFullText;

uses()->group('attributes');

it('new instance', function () {
    expect(new SearchUsingFullText('column'))
        ->columns
        ->toBeArray()
        ->toHaveCount(1);
});
