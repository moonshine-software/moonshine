<?php

use MoonShine\Fields\Textarea;
use MoonShine\Fields\TinyMce;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = TinyMce::make('TinyMce');
});

it('textarea is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Textarea::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.tinymce');
});

it('has assets', function (): void {
    expect($this->field->getAssets())
        ->toBeArray()
        ->not->toBeEmpty();
});
