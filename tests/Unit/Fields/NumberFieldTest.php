<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Components\Rating;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\InputExtensions\InputNumberUpDown;

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
        ->toBeInstanceOf(Field::class);
});

it('type', function (): void {
    expect($this->field->getAttributes()->get('type'))
        ->toBe('number');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});

it('number methods', function (): void {
    expect($this->field)
        ->min(3)
        ->getAttribute('min')
        ->toBe('3')
        ->max(6)
        ->getAttribute('max')
        ->toBe('6')
        ->step(2)
        ->getAttribute('step')
        ->toBe('2')
    ;
});

it('preview value', function (): void {
    expect($this->field->preview())
        ->toBe('3');
});

it('buttons is up-down extension', function (): void {
    expect($this->field->buttons()->getExtensions()->first())
        ->toBeInstanceOf(InputNumberUpDown::class);
});

it('preview with stars', function (): void {
    expect($this->field->stars()->preview())
        ->toBe(
            (string) Rating::make(
                3
            )->render()
        );
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
