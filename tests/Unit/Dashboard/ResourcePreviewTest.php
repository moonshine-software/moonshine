<?php

use MoonShine\Dashboard\ResourcePreview;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('dashboard');

beforeEach(function () {
    $this->resource = TestResourceBuilder::new(MoonshineUser::class);
    $this->block = ResourcePreview::make($this->resource, 'Label');
});

it('make instance', function () {
    expect($this->block)
        ->label()
        ->toBe('Label');
});


it('correct resource', function () {
    expect($this->block)
        ->resource()
        ->toBe($this->resource);
});

it('correct items', function () {
    expect($this->block)
        ->items()
        ->toBeCollection();
});
