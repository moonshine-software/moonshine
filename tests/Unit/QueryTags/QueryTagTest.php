<?php

use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Models\MoonshineUser;
use MoonShine\QueryTags\QueryTag;

uses()->group('query-tags');

beforeEach(function (): void {
    $this->tag = QueryTag::make(
        'Tag',
        static fn (Builder $query): Builder => $query
    );
});

it('query tag methods', function (): void {
    expect($this->tag)
        ->label()
        ->toBe('Tag')
        ->uri()
        ->toBe('tag')
        ->builder(MoonshineUser::query())
        ->toBeInstanceOf(Builder::class);
});
