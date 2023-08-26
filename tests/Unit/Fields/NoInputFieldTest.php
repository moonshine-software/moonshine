<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\NoInput;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = NoInput::make('NoInput', 'no_input');

    $this->item = new class () extends Model {
        public string|bool $no_input = 'Hello world';
    };

    fillFromModel($this->field, $this->item);
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.no-input');
});

it('default item value', function (): void {
    expect($this->field->preview())
        ->toBe($this->item->no_input);
});

it('reformat item value', function (): void {
    $this->field = NoInput::make('NoInput', 'no_input', fn (): string => 'Testing');

    $this->field->resolveFill($this->item->toArray(), $this->item);

    expect($this->field->preview())
        ->toBe('Testing');
});

it('badge value', function (): void {
    expect($this->field->badge('green')->preview())
        ->toBe(view('moonshine::ui.badge', [
            'color' => 'green',
            'value' => $this->item->no_input,
        ])->render());
});

it('boolean value', function (): void {
    $this->item->no_input = true;

    $this->field->reset()->resolveFill($this->item->toArray(), $this->item);


    expect($this->field->boolean()->preview())
        ->toBe(view('moonshine::ui.boolean', [
            'value' => $this->item->no_input,
        ])->render())
    ->and($this->field->reset()->boolean(hideTrue: true)->preview())
        ->toBeEmpty();

    $this->item->no_input = false;

    expect($this->field->boolean(hideFalse: true)->preview())
        ->toBeEmpty();
});

it('link value', function (): void {
    expect($this->field->link('/', true)->preview())
        ->toBe(view('moonshine::ui.url', [
            'value' => $this->item->no_input,
            'href' => '/',
        ])->render());
});

it('apply', function (): void {
    $data = ['data' => 'data'];

    fakeRequest(parameters: $data);

    $item = new class () extends Model {
        protected $fillable = [
            'name',
            'body',
        ];
    };

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            $item)
        )
        ->toBe($item)
    ;

});

