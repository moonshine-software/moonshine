<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Checkbox;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Checkbox::make('Active');
});

describe('basic methods', function () {
    it('type', function (): void {
        expect($this->field->type())
            ->toBe('checkbox');
    });

    it('view', function (): void {
        expect($this->field->getView())
            ->toBe('moonshine::fields.checkbox');
    });

    it('preview', function (): void {
        $field = Checkbox::make('Active');

        expect($field->fill(1)->preview())
            ->toBe(view('moonshine::ui.boolean', ['value' => true])->render());

        $field = Checkbox::make('Active');

        expect($field->fill(0)->preview())
            ->toBe(view('moonshine::ui.boolean', ['value' => false])->render());
    });

    it('correct is checked value', function (): void {
        $field = Checkbox::make('Active')
            ->fill(true);

        expect($field->isChecked())
            ->toBeTrue();

        $field = Checkbox::make('Active')
            ->fill(1);

        expect($field->isChecked())
            ->toBeTrue();

        $field = Checkbox::make('Active')
            ->fill(0);

        expect($field->isChecked())
            ->toBeFalse();

        $field = Checkbox::make('Active')
            ->onValue('yes')
            ->fill('yes');

        expect($field->isChecked())
            ->toBeTrue();

        $field = Checkbox::make('Active')
            ->onValue('yes')
            ->fill('no');

        expect($field->isChecked())
            ->toBeFalse();
    });

    it('change preview', function () {
        expect($this->field->changePreview(static fn () => 'changed'))
            ->preview()
            ->toBe('changed');
    });

    it('formatted value', function () {
        $field = Checkbox::make('Range', formatted: static fn () => 'yes')
            ->onValue('yes')
            ->fill(false);

        expect($field->toFormattedValue())
            ->toBe('yes');
    });

    it('default value', function () {
        $field = Checkbox::make('Active')
            ->onValue('yes');

        expect($field->isChecked())
            ->toBeFalse();

        $field = Checkbox::make('Active')
            ->onValue('yes')
            ->default('yes');

        expect($field->isChecked())
            ->toBeTrue();

    });

    it('applies', function () {
        expect()
            ->applies($this->field);
    });

    it('apply', function (): void {
        $data = ['active' => 'false'];

        fakeRequest(parameters: $data);

        expect(
            $this->field->apply(
                TestResourceBuilder::new()->onSave($this->field),
                new class () extends Model {
                    protected $fillable = [
                        'active',
                    ];
                }
            )
        )
            ->toBeInstanceOf(Model::class)
            ->active
            ->toBe($data['active']);
    });
});

describe('unique field methods', function () {
    it('on/off values', function (): void {
        expect($this->field->onValue('yes')->offValue('no'))
            ->getOnValue()
            ->toBe('yes')
            ->getOffValue()
            ->toBe('no');
    });
});
