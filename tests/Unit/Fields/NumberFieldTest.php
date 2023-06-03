<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Number;
use MoonShine\Fields\Text;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Number::make('Rating');
    $this->item = new class () extends Model {
        public int $rating = 3;
    };
});

it('text field is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('number');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});

it('number methods', function (): void {
    expect($this->field)
        ->min(3)
        ->min->toBe(3)
        ->getAttribute('min')
        ->toBe(3)
        ->max(6)
        ->max->toBe(6)
        ->getAttribute('max')
        ->toBe(6)
        ->step(2)
        ->step->toBe(2)
        ->getAttribute('step')
        ->toBe(2)
    ;
});

it('index view value', function (): void {
    expect($this->field->indexViewValue($this->item))
        ->toBe('3');
});

it('index view value with stars', function (): void {
    expect($this->field->stars()->indexViewValue($this->item))
        ->toBe(view('moonshine::ui.rating', [
            'value' => '3',
        ])->render());
});
