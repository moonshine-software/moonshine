<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Enum;
use MoonShine\Fields\Select;
use MoonShine\Tests\Fixtures\Enums\TestEnumColor;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Enum::make('Enum')
        ->attach(TestEnumColor::class);

    $this->field->resolveFill(['enum' => TestEnumColor::Red]);
});

it('select field is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Select::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe("text");
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.select');
});

it('preview', function (): void {
    expect($this->field->preview())
        ->toBe('Red');
});
