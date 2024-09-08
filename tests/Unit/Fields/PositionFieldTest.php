<?php

declare(strict_types=1);

use MoonShine\UI\Fields\Position;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Position::make('Position')->fill(index: 3);
});

describe('basic methods', function () {
    it('view', function (): void {
        expect($this->field->getView())
            ->toBe('moonshine::fields.preview');
    });

    it('preview and render', function (): void {
        $field = Position::make('Position');

        expect((string) $field->fill(1, index: 3)->preview())
            ->toBe('4');

        $field = Position::make('Position');

        expect((string) $field->fill(0, index: 4)->render())
            ->toContain('5', 'data-increment-position', 'Position');
    });

    it('values', function (): void {
        $field = Position::make('Position')->fill(1, index: 3);

        expect($field->toFormattedValue())
            ->toBe(4)
            ->and($field->toValue())
            ->toBe(1)
            ->and($field->getValue())
            ->toBe('4')
            ->and($field->toRawValue())
            ->toBe('4');
    });

    it('visual states', function () {
        $field = Position::make('Position')->fill(1, index: 4);

        expect((string) $field->render())
            ->toContain('5', 'data-increment-position', 'Position')
            ->and((string) $field->flushRenderCache()->previewMode()->render())
            ->toBe('5')
            ->and((string) $field->flushRenderCache()->rawMode()->render())
            ->toBe('5')
            ->and((string) $field->flushRenderCache()->defaultMode()->rawMode()->previewMode()->render())
            ->toContain('5', 'data-increment-position')
        ;
    });

    it('change preview', function () {
        expect($this->field->changePreview(static fn () => 'changed'))
            ->preview()
            ->toBe('changed');
    });
});
