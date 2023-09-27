<?php

declare(strict_types=1);

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Fields\DateRangeField;

uses()->group('fields');
uses()->group('date-field');

beforeEach(function (): void {
    $this->field = DateRangeField::make('Range')
        ->fromTo('start', 'end');
});

describe('basic methods', function () {
    it('change preview', function () {
        expect($this->field->changePreview(static fn () => 'changed'))
            ->preview()
            ->toBe('changed');
    });

    it('formatted value', function () {
        $field = DateRangeField::make('Range', formatted: static fn () => ['changed'])
            ->fromTo('start', 'end')
            ->fill([]);

        expect($field->toFormattedValue())
            ->toBe(['changed']);
    });

    it('default value', function () {
        $from = now();
        $to = now()->addMonth();

        $field = DateRangeField::make('Range')
            ->fromTo('start', 'end')
            ->default([$from, $to]);

        expect($field->toValue())
            ->toBe([$from, $to]);

        $fromFilled = now()->addMonth();
        $toFilled = now()->addYear();

        $field = DateRangeField::make('Range')
            ->fromTo('start', 'end')
            ->default([$from, $to])
            ->fill(['start' => $fromFilled, 'end' => $toFilled])
        ;

        expect($field->toValue())
            ->toBe(['start' => $fromFilled, 'end' => $toFilled]);
    });

    it('applies', function () {
        $field = DateRangeField::make('Range')
            ->fromTo('start', 'end')
        ;

        expect($field->onApply(fn ($data) => ['onApply'])->apply(fn ($data) => $data, []))
            ->toBe(['onApply'])
            ->and($field->onBeforeApply(fn ($data) => ['onBeforeApply'])->beforeApply([]))
            ->toBe(['onBeforeApply'])
            ->and($field->onAfterApply(fn ($data) => ['onAfterApply'])->afterApply([]))
            ->toBe(['onAfterApply'])
            ->and($field->onAfterDestroy(fn ($data) => ['onAfterDestroy'])->afterDestroy([]))
            ->toBe(['onAfterDestroy'])
        ;
    });
});

describe('common field methods', function () {
    it('names', function (): void {
        expect($this->field)
            ->name()
            ->toBe('range[]')
            ->name('start')
            ->toBe('range[start]');
    });

    it('correct interfaces', function (): void {
        expect($this->field)
            ->toBeInstanceOf(DefaultCanBeArray::class)
        ;
    });

    it('type', function (): void {
        expect($this->field->type())
            ->toBe('date');
    });

    it('view', function (): void {
        expect($this->field->getView())
            ->toBe('moonshine::fields.range');
    });

    it('preview', function (): void {
        $from = now();
        $to = now()->addMonth();

        expect($this->field->fill(['start' => $from, 'end' => $to])->preview())
            ->toBe($from . ' - ' . $to);
    });

    it('is group', function (): void {
        expect($this->field->isGroup())
            ->toBeTrue();
    });

    it('is nullable', function (): void {
        expect($this->field->preview())
            ->toBeEmpty();
    });
});

describe('unique field methods', function () {
    it('format carbon', function (): void {
        $from = now();
        $to = now()->addMonth();

        $field = $this->field
            ->fill(['start' => $from, 'end' => $to])
            ->format('d.m');

        expect($field->preview())
            ->toBe($from->format('d.m') . ' - ' . $to->format('d.m'))
            ->and($field->value())
                ->toBe([
                    $field->fromField => $from->format('Y-m-d'),
                    $field->toField => $to->format('Y-m-d'),
                ]);
    });

    it('format string', function (): void {
        $from = '2020-01-01';
        $to = '2020-02-01';

        $field = $this->field
            ->fill(['start' => $from, 'end' => $to])
            ->format('d.m');

        expect($field->preview())
            ->toBe('01.01 - 01.02')
            ->and($field->value())
            ->toBe([
                $field->fromField => $from,
                $field->toField => $to,
            ]);
    });

    it('format with time', function (): void {
        $from = '2020-01-01';
        $to = '2020-02-01';

        $field = $this->field
            ->fill(['start' => $from, 'end' => $to])
            ->withTime();

        expect($field->type())
            ->toBe('datetime-local')
            ->and($field->preview())
            ->toBe('2020-01-01 00:00:00 - 2020-02-01 00:00:00')
            ->and($field->value())
            ->toBe([
                $field->fromField => $from . 'T00:00',
                $field->toField => $to . 'T00:00',
            ]);
    });
});
