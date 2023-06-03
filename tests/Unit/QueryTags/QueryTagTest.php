<?php

use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Models\MoonshineUser;
use MoonShine\QueryTags\QueryTag;

uses()->group('query-tags');

beforeEach(function (): void {
    $this->tag = QueryTag::make(
        'Tag',
        static fn (): \Illuminate\Database\Eloquent\Builder => MoonshineUser::query()
    );
});

it('query tag methods', function (): void {
    expect($this->tag)
        ->label()
        ->toBe('Tag')
        ->uri()
        ->toBe('tag')
        ->builder()
        ->toBeInstanceOf(Builder::class);
});
