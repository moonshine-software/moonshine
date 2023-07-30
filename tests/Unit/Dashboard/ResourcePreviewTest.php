<?php

use MoonShine\Components\ResourcePreview;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('dashboard');

beforeEach(function (): void {
    $this->resource = TestResourceBuilder::new(MoonshineUser::class);
    $this->block = ResourcePreview::make($this->resource, 'Label');
});

it('make instance', function (): void {
    expect($this->block)
        ->label()
        ->toBe('Label');
});


it('correct resource', function (): void {
    expect($this->block)
        ->resource()
        ->toBe($this->resource);
});

it('correct items', function (): void {
    expect($this->block)
        ->items()
        ->toBeCollection();
});
