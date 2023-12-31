<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\Switcher;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Switcher::make('Active');
    $this->item = new class () extends Model {
        public bool $active = true;
    };

    fillFromModel($this->field, $this->item);
});

it('checkbox is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Checkbox::class);
});

it('preview with not auto update', function (): void {
    expect($this->field->preview())
        ->toBe(
            view('moonshine::ui.boolean', [
                'value' => (bool) $this->field->toValue(false),
            ])->render()
        );
});

it('preview with auto update', function (): void {
    expect((string) $this->field->updateOnPreview(url: fn () => '/')->preview())
        ->toBe(
            view('moonshine::fields.switch', [
                'element' => $this->field,
            ])->render()
        );
});

describe('basic methods', function () {
    it('type', function (): void {
        expect($this->field->type())
            ->toBe('checkbox');
    });

    it('view', function (): void {
        expect($this->field->getView())
            ->toBe('moonshine::fields.switch');
    });

    it('preview', function (): void {
        expect($this->field->preview())
            ->toBe(view('moonshine::ui.boolean', [
                'value' => true,
            ])->render());
    });

    it('change preview', function () {
        expect($this->field->changePreview(static fn () => 'changed'))
            ->preview()
            ->toBe('changed');
    });

    it('default value', function () {
        $field = Switcher::make('Switcher')
            ->onValue(1)
            ->offValue(0)
            ->default(1);

        expect($field->toValue())
            ->toBe(1);
    });

    it('applies', function () {
        expect()
            ->applies($this->field);
    });
});
