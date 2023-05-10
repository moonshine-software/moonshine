<?php

use MoonShine\Fields\Textarea;
use MoonShine\Fields\TinyMce;

uses()->group('fields');

beforeEach(function () {
    $this->field = TinyMce::make('TinyMce');
});

it('textarea is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(Textarea::class);
});

it('type', function () {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.tinymce');
});

it('has assets', function () {
    expect($this->field->getAssets())
        ->toBeArray()
        ->not->toBeEmpty();
});



