<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\Boolean;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Url;
use MoonShine\UI\Fields\Preview;

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
    $this->field = Preview::make('NoInput', 'no_input', static fn (): string => 'Testing');

    $this->field->fillData($this->item);

    expect($this->field->preview())
        ->toBe('Testing');
});

it('badge value', function (): void {
    expect((string) $this->field->badge('green')->preview())
        ->toBe((string) Badge::make($this->item->no_input, 'green')->render());
});

it('set value', function (): void {
    $field = Preview::make('NoInput', 'no_input')
        ->setValue('set value');

    expect((string) $field->preview())
        ->toContain('set value');
});

it('set value and fill', function (): void {
    $field = Preview::make('NoInput', 'no_input')
        ->setValue('set value')
        ->fill('new value');

    expect((string) $field->preview())
        ->toContain('new value');
});

it('set value and fill by form', function (): void {
    View::share('errors', new ViewErrorBag());

    $form = FormBuilder::make()
        ->fields([
            Preview::make('NoInput', 'no_input')
                ->setValue('set value'),
        ])
        ->fill([]);

    expect((string) $form->render())
        ->toContain('set value');

    $form = FormBuilder::make()
        ->fields([
            Preview::make('NoInput', 'no_input')
                ->setValue('set value'),
        ])
        ->fill(['no_input' => 'new value']);

    expect((string) $form->render())
        ->toContain('new value');
});

it('boolean value', function (): void {
    $this->item->no_input = true;

    $this->field->reset()->fillData($this->item);

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
            TestResourceBuilder::new()->fieldApply($this->field),
            $item
        )
    )
        ->toBe($item);
});
