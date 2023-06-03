<?php

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Filters\TextFilter;
use MoonShine\Models\MoonshineUser;

uses()->group('filters');

beforeEach(function (): void {
    $this->filter = TextFilter::make('Text');
});

it('correct link', function (): void {
    $link = fake()->url();

    expect($this->filter->addLink('Link', $link, true))
        ->getLinkName()
        ->toBe('Link')
        ->getLinkValue()
        ->toBe($link)
        ->isLinkBlank()
        ->toBeTrue();
});

it('correct name', function (): void {
    expect($this->filter->name())
        ->toBe('filters[text]');
});

it('form view name', function (): void {
    fakeRequest(parameters: [
        'filters' => ['text' => 'Testing'],
    ]);

    expect($this->filter->formViewValue(new class () extends Model {}))
        ->toBe('Testing');
});

it('get query', function (): void {
    expect($this->filter->getQuery(MoonshineUser::query()))
        ->toBeInstanceOf(Builder::class);
});
