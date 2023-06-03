<?php

use MoonShine\InputExtensions\InputExtension;
use MoonShine\InputExtensions\InputEye;

uses()->group('input-extensions');

beforeEach(function (): void {
    $this->ext = new InputEye('Value');
});

it('methods', function (): void {
    expect($this->ext)
        ->toBeInstanceOf(InputExtension::class)
        ->getValue()
        ->toBe('Value')
        ->getView()
        ->toBe('moonshine::form.input-extensions.eye')
        ->xData()
        ->toBeCollection()
        ->xInit()
        ->toBeCollection();
});
