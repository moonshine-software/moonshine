<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Color;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');


beforeEach(function (): void {
    $this->field = Color::make('Color');
});

it('text is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe("text");
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.color');
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
        ->toBe($data['color'])
    ;
});
