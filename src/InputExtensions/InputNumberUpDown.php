<?php

declare(strict_types=1);

namespace MoonShine\InputExtensions;

final class InputNumberUpDown extends InputExtension
{
    protected string $view = 'moonshine::form.input-extensions.up-down';

    protected array $xInit = [];

    protected array $xData = [
        'value: Number($refs.extensionInput.value)',
        'min: Number($refs.extensionInput.min)',
        'max: Number($refs.extensionInput.max)',
        'step: Number($refs.extensionInput.step ?? 1)',
        'toggleUp() { this.value = this.value + (this.value < this.max ? this.step : 0); $refs.extensionInput.value = this.value; }',
        'toggleDown() { this.value = this.value - (this.value > this.min ? this.step : 0); $refs.extensionInput.value = this.value; }',
    ];
}
