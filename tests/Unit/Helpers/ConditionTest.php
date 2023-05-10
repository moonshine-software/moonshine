<?php

use MoonShine\Helpers\Condition;

uses()->group('helpers');

it('conditions', function (array $arguments, bool $expected) {
    expect($expected)
        ->toBe(Condition::boolean(...$arguments));
})->with([
    [[true, false], true],
    [[false, true], false],
    [[null, true], true],
    [[null, false], false],
    [[fn() => true, false], true],
    [[fn() => false, true], false],
]);
