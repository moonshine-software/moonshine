<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Date;
use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');
uses()->group('date-field');

beforeEach(function (): void {
    $this->field = Date::make('Created at');
});

it('text field is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function (): void {
    expect($this->field->type())
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

    expect($this->field->type())
        ->toBe('datetime-local');
});

it('preview', function (): void {
    $item = MoonshineUser::factory()->create();

    $this->field->resolveFill($item->toArray());

    expect($this->field->preview())
        ->toBe($item->created_at->format('Y-m-d H:i:s'))
        ->and($this->field->format('d.m'))
        ->preview()
        ->toBe($item->created_at->format('d.m'))
    ;

});

it('value', function (): void {
    $item = MoonshineUser::factory()->create();

    $this->field->resolveFill($item->toArray());

    $itemDateNull = MoonshineUser::factory()->create([
        'created_at' => null,
    ]);

    expect($this->field->value())
        ->toBe($item->created_at->format('Y-m-d'))
        ->and($this->field->nullable())
        ->reset()
        ->resolveFill($itemDateNull->toArray())
        ->value()
        ->toBeEmpty()
        ->and($this->field->reset())
        ->default('2000-01-12')
        ->value()
        ->toBe('2000-01-12')
        ->and($this->field->reset())
        ->resolveFill($item->toArray())
        ->withTime()
        ->value()
        ->toBe($item->created_at->format('Y-m-d\TH:i'))
    ;
});

it('apply', function (): void {
    $data = ['created_at' => now()->format('Y-m-d H:i:s')];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {}
        )
    )
        ->toBeInstanceOf(Model::class)
        ->created_at
        ->format('Y-m-d H:i:s')
        ->toBe($data['created_at'])
    ;
});
