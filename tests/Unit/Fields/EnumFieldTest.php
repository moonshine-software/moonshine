<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Tests\Fixtures\Enums\TestEnumColor;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Select;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Enum::make('Enum')
        ->fill(TestEnumColor::Red)
        ->attach(TestEnumColor::class);
});

it('select field is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Select::class);
});

it('type', function (): void {
    expect($this->field->attributes()->get('type'))
        ->toBeEmpty();
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.select');
});

it('preview', function (): void {
    expect($this->field->preview())
        ->toBe('R');
});

it('apply', function (): void {
    $data = ['enum' => TestEnumColor::Red->value];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'enum',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->enum
        ->toBe($data['enum'])
    ;
});
