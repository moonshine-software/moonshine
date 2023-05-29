<?php

use MoonShine\Traits\Fields\ShowWhen;

uses()->group('fields');

beforeEach(function () {
    $this->showWhenTest = new class () {
        use ShowWhen;

        public function name()
        {
            return 'field1';
        }
    };
});

it('default operator', function () {
    $this->showWhenTest->showWhen('field2', 1);

    $condition = $this->showWhenTest->showWhenCondition();
    expect($condition['showField'])->toBe('field1')
        ->and($condition['changeField'])->toBe('field2')
        ->and($condition['operator'])->toBe('=')
        ->and($condition['value'])->toBe(1)
    ;
});

it('operator >', function () {
    $this->showWhenTest->showWhen('field2', '>', 1);

    $condition = $this->showWhenTest->showWhenCondition();
    expect($condition['showField'])->toBe('field1')
        ->and($condition['changeField'])->toBe('field2')
        ->and($condition['operator'])->toBe('>')
        ->and($condition['value'])->toBe(1)
    ;
});

it('operator in', function () {
    $this->showWhenTest->showWhen('field2', 'in', [1,2]);

    $condition = $this->showWhenTest->showWhenCondition();
    expect($condition['showField'])->toBe('field1')
        ->and($condition['changeField'])->toBe('field2')
        ->and($condition['operator'])->toBe('in')
        ->and($condition['value'])->toBe([1,2])
    ;
});

it('error operator', function () {
    $this->showWhenTest->showWhen('field2', '%', 1);

    $condition = $this->showWhenTest->showWhenCondition();
    expect($condition['showField'])->toBe('field1')
        ->and($condition['changeField'])->toBe('field2')
        ->and($condition['operator'])->toBe('=')
        ->and($condition['value'])->toBe('%')
    ;
});

it('error operator in', function () {
    $this->showWhenTest->showWhen('field2', 'in', 1);
})->throws(InvalidArgumentException::class, 'Illegal operator and value combination. Value must be array type');

it('error null value', function () {
    $this->showWhenTest->showWhen('field2', '>', null);
})->throws(InvalidArgumentException::class, 'Illegal operator and value combination.');
