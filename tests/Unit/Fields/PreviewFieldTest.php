<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Components\Badge;
use MoonShine\Components\Boolean;
use MoonShine\Components\Url;
use MoonShine\Fields\Preview;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Preview::make('NoInput', 'no_input');

    $this->item = new class () extends Model {
        public string|bool $no_input = 'Hello world';
    };

    fillFromModel($this->field, $this->item);
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.preview');
});

it('default item value', function (): void {
    expect($this->field->preview())
        ->toBe($this->item->no_input);
});

it('reformat item value', function (): void {
    $this->field = Preview::make('NoInput', 'no_input', fn (): string => 'Testing');

    $this->field->resolveFill($this->item->toArray(), $this->item);

    expect($this->field->preview())
        ->toBe('Testing');
});

it('badge value', function (): void {
    expect((string) $this->field->badge('green')->preview())
        ->toBe((string) Badge::make($this->item->no_input, 'green')->render());
});

it('boolean value', function (): void {
    $this->item->no_input = true;

    $this->field->reset()->resolveFill($this->item->toArray(), $this->item);

    expect($this->field->boolean()->preview())
        ->toBe(
            (string) Boolean::make($this->item->no_input)->render()
        )
        ->and($this->field->boolean(hideTrue: true)->preview())
        ->toBeEmpty()
        ->and($this->field->setValue(false)->boolean(hideFalse: true)->preview())
        ->toBeEmpty();
});

it('link value', function (): void {
    expect((string) $this->field->link('/')->preview())
        ->toBe(
            (string) Url::make('/', $this->item->no_input)->render()
        );
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
            $item
        )
    )
        ->toBe($item);
});
