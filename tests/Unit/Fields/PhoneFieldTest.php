<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Phone;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Phone::make('Phone');
});

it('text field is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('tel');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});

it('apply', function (): void {
    $data = ['phone' => '79099099988'];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'phone'
                ];
            })
        )
        ->toBeInstanceOf(Model::class)
        ->phone
        ->toBe($data['phone'])
    ;
});