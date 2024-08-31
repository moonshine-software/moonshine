<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Text;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Phone::make('Phone');
});

it('text field is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function (): void {
    expect($this->field->getAttributes()->get('type'))
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
            TestResourceBuilder::new()->fieldApply($this->field),
            new class () extends Model {
                protected $fillable = [
                    'phone',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->phone
        ->toBe($data['phone'])
    ;
});
