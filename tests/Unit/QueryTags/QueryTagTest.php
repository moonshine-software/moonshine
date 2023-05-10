<?php

use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Models\MoonshineUser;
use MoonShine\QueryTags\QueryTag;

uses()->group('query-tags');

beforeEach(function () {
    $this->tag = QueryTag::make(
        'Tag',
        static fn() => MoonshineUser::query()
    );
});

it('query tag methods', function () {
    expect($this->tag)
        ->label()
        ->toBe('Tag')
        ->uri()
        ->toBe('tag')
        ->builder()
        ->toBeInstanceOf(Builder::class);
});
