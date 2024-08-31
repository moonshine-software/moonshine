<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Field;

uses()->group('fields');
uses()->group('date-field');

beforeEach(function (): void {
    $this->field = Date::make('Created at');
});

it('text field is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Field::class);
});

it('type', function (): void {
    expect($this->field->getAttributes()->get('type'))
        ->toBe('date');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});

it('default format', function (): void {
    expect($this->field->getFormat())
        ->toBe('Y-m-d H:i:s');
});

it('change format', function (): void {
    $this->field->format('d.m.Y');

    expect($this->field->getFormat())
        ->toBe('d.m.Y');
});

it('with time', function (): void {
    $this->field->withTime();

    expect($this->field->getAttributes()->get('type'))
        ->toBe('datetime-local');
});

it('preview', function (): void {
    $item = MoonshineUser::factory()->create();

    $this->field->fillData($item);

    expect($this->field->preview())
        ->toBe($item->created_at->format('Y-m-d H:i:s'))
        ->and($this->field->format('d.m'))
        ->preview()
        ->toBe($item->created_at->format('d.m'))
    ;

});

it('value', function (): void {
    $item = MoonshineUser::factory()->create();

    $this->field->fillData($item);

    $itemDateNull = MoonshineUser::factory()->create([
        'created_at' => null,
    ]);

    expect($this->field->getValue())
        ->toBe($item->created_at->format('Y-m-d'))
        ->and($this->field->nullable())
        ->reset()
        ->fillData($itemDateNull)
        ->getValue()
        ->toBeEmpty()
        ->and($this->field->reset())
        ->default('2000-01-12')
        ->getValue()
        ->toBe('2000-01-12')
        ->and($this->field->reset())
        ->fillData($item)
        ->withTime()
        ->getValue()
        ->toBe($item->created_at->format('Y-m-d\TH:i'))
    ;
});

it('apply', function (): void {
    $data = ['created_at' => now()->format('Y-m-d H:i:s')];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->fieldApply($this->field),
            new class () extends Model {}
        )
    )
        ->toBeInstanceOf(Model::class)
        ->created_at
        ->format('Y-m-d H:i:s')
        ->toBe($data['created_at'])
    ;
});
