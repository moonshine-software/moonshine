<?php

use MoonShine\Fields\FormElement;
use MoonShine\Filters\Filter;
use MoonShine\Filters\TextFilter;
use MoonShine\InputExtensions\InputExtension;
use MoonShine\InputExtensions\InputEye;

uses()->group('filters');

beforeEach(function () {
    $this->filter = TextFilter::make('Text');
});

it('filter and form element is parent', function () {
    expect($this->filter)
        ->toBeInstanceOf(Filter::class)
        ->toBeInstanceOf(FormElement::class);
});

it('type', function () {
    expect($this->filter->type())
        ->toBe('text');
});

it('view', function () {
    expect($this->filter->getView())
        ->toBe('moonshine::filters.text');
});

it('mask', function () {
    expect($this->filter->mask('999'))
        ->getMask()
        ->toBe('999');
});

it('extension', function () {
    expect($this->filter->extension(new InputEye()))
        ->getExtensions()
        ->toBeCollection()
        ->toHaveCount(1)
        ->getExtensions()
        ->each->toBeInstanceOf(InputExtension::class);
});
