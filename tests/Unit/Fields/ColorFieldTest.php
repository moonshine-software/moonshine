<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\Field;

uses()->group('fields');


beforeEach(function (): void {
    $this->field = Color::make('Color');
});

it('text is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Field::class);
});

it('type', function (): void {
    expect($this->field->attributes()->get('type'))
        ->toBe('color');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.color');
});

it('resolve preview value', function (): void {
    expect($this->field->setValue('#DDD')->rawMode()->preview())
        ->toBe('#DDD');
});

it('apply', function (): void {
    $data = ['color' => '#FFF'];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'color',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->color
        ->toBe($data['color']);
});
