<?php

use MoonShine\Fields\FormElement;
use MoonShine\Filters\Filter;
use MoonShine\Filters\TextFilter;
use MoonShine\InputExtensions\InputExtension;
use MoonShine\InputExtensions\InputEye;

uses()->group('filters');

beforeEach(function (): void {
    $this->filter = TextFilter::make('Text');
});

it('filter and form element is parent', function (): void {
    expect($this->filter)
        ->toBeInstanceOf(Filter::class)
        ->toBeInstanceOf(FormElement::class);
});

it('type', function (): void {
    expect($this->filter->type())
        ->toBe('text');
});

it('view', function (): void {
    expect($this->filter->getView())
        ->toBe('moonshine::filters.text');
});

it('mask', function (): void {
    expect($this->filter->mask('999'))
        ->getMask()
        ->toBe('999');
});

it('extension', function (): void {
    expect($this->filter->extension(new InputEye()))
        ->getExtensions()
        ->toBeCollection()
        ->toHaveCount(1)
        ->getExtensions()
        ->each->toBeInstanceOf(InputExtension::class);
});
