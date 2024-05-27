<?php

declare(strict_types=1);

use MoonShine\Core\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Core\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\UI\Fields\Range;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Range::make('Range')->fromTo('start', 'end');
});

describe('basic methods', function () {
    it('change fill', function () {
        $from = 10;
        $to = 20;
        $values = ['start' => $from, 'end' => $to];

        expect($this->field->changeFill(static fn () => $values)->fill([]))
            ->toValue()
            ->toBe($values);

        $from = now();
        $values = ['start' => $from];

        expect($this->field->changeFill(static fn () => $values)->fill([]))
            ->toValue()
            ->toBe(['start' => $from, 'end' => $this->field->getAttribute('max')]);
    });

    it('set value', function () {
        $from = 10;
        $to = 20;
        $values = ['start' => $from, 'end' => $to];

        expect($this->field->setValue($values))
            ->toValue()
            ->toBe($values)
            ->and($this->field->preview())
            ->toContain($from, $to);

        $from = 15;
        $values = ['start' => $from];

        expect($this->field->setValue($values))
            ->toValue()
            ->toBe($values)
            ->and($this->field->setValue($values)->preview())
            ->not->toContain($to)
            ->toContain($from, $this->field->getAttribute('max'));
    });

    it('non value', function () {
        $from = 10;
        $to = 20;
        $values = ['start' => $from, 'end' => $to];

        expect($this->field)
            ->toValue()
            ->toBeNull()
            ->and($this->field->preview())
            ->toBeEmpty()
            ->and($this->field->default($values)->preview())
            ->toBeEmpty()
        ;
    });

    it('change preview', function () {
        expect($this->field->changePreview(static fn () => 'changed'))
            ->preview()
            ->toBe('changed');
    });

    it('formatted value', function () {
        $field = Range::make('Range', formatted: static fn () => ['changed'])
            ->fromTo('start', 'end')
            ->fill([]);

        expect($field->toFormattedValue())
            ->toBe(['changed']);
    });

    it('default value', function () {
        $field = Range::make('Range')
            ->fromTo('start', 'end')
            ->default([0, 100]);

        expect($field->toValue())
            ->toBe([0, 100]);

        $field = Range::make('Range')
            ->fromTo('start', 'end')
            ->default([0, 100])
            ->fill(['start' => 10, 'end' => 90])
        ;

        expect($field->toValue())
            ->toBe(['start' => 10, 'end' => 90]);
    });

    it('applies', function () {
        $field = Range::make('Range')
            ->fromTo('start', 'end')
        ;

        expect()
            ->applies($field);
    });
});

describe('common field methods', function () {
    it('names', function (): void {
        expect($this->field)
            ->getNameAttribute()
            ->toBe('range[]')
            ->getNameAttribute('start')
            ->toBe('range[start]');
    });

    it('correct interfaces', function (): void {
        expect($this->field)
            ->toBeInstanceOf(DefaultCanBeNumeric::class)
            ->toBeInstanceOf(DefaultCanBeArray::class)
        ;
    });

    it('type', function (): void {
        expect($this->field->attributes()->get('type'))
            ->toBe('number');
    });

    it('view', function (): void {
        expect($this->field->getView())
            ->toBe('moonshine::fields.range');
    });

    it('preview', function (): void {
        expect($this->field->fill(['start' => 0, 'end' => 100])->preview())
            ->toBe('0 - 100');
    });

    it('is group', function (): void {
        expect($this->field->isGroup())
            ->toBeTrue();
    });
});
