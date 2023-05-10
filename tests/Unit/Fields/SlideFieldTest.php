<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Number;
use MoonShine\Fields\SlideField;

uses()->group('fields');

beforeEach(function () {
    $this->field = SlideField::make('Slide')
        ->toField('to')
        ->fromField('from');
    $this->item = new class extends Model {
        public int $from = 10;
        public int $to = 20;
    };
});

it('type', function () {
    expect($this->field->type())
        ->toBe('number');
});

it('number is parent', function () {
    expect($this->field)
        ->toBeInstanceOf(Number::class);
});

it('view', function () {
    expect($this->field->getView())
        ->toBe('moonshine::fields.slide');
});

it('index view value with stars', function () {
    $from = view('moonshine::ui.rating', [
        'value' => $this->item->from,
    ])->render();

    $to = view('moonshine::ui.rating', [
        'value' => $this->item->to,
    ])->render();

    expect($this->field->stars()->indexViewValue($this->item))
        ->toBe("$from - $to");
});

it('index view value', function () {
    expect($this->field->indexViewValue($this->item))
        ->toBe("{$this->item->from} - {$this->item->to}");
});

it('save', function () {
    fakeRequest(parameters: [
        'slide' => [
            'from' => 100,
            'to' => 200
        ]
    ]);

    expect($this->field->save($this->item))
        ->from
        ->toBe(100)
        ->to
        ->toBe(200)
    ;
});

