<?php

use MoonShine\Dashboard\TextBlock;

uses()->group('dashboard');

it('make instance', function () {
    $block = TextBlock::make('Label', 'Text');

    expect($block)
        ->label()
        ->toBe('Label')
        ->text()
        ->toBe('Text')
        ->and($block->columnSpan(3, 3))
        ->columnSpanValue()
        ->toBe(3)
        ->and($block->adaptiveColumnSpanValue())
        ->toBe(3);
});
