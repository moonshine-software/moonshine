<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Number;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Number::make('Rating');
    $this->item = new class () extends Model {
        public int $rating = 3;
    };

    fillFromModel($this->field, $this->item);
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

it('preview value', function (): void {
    expect($this->field->preview())
        ->toBe('3');
});

it('preview with stars', function (): void {
    expect($this->field->stars()->preview())
        ->toBe(view('moonshine::ui.rating', [
            'value' => '3',
        ])->render());
});

it('apply', function (): void {
    $data = ['rating' => 5];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'rating',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->rating
        ->toBe($data['rating'])
    ;
});
