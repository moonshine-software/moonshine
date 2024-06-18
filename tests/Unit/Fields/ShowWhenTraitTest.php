<?php

declare(strict_types=1);

use MoonShine\UI\Traits\Fields\ShowWhen;

uses()->group('fields');
uses()->group('show-when');

beforeEach(function (): void {
    $this->showWhenTest = new class () {
        use ShowWhen;

        public function getNameAttribute(): string
        {
            return 'field1';
        }

        public function getColumn(): string
        {
            return 'field1';
        }

        protected function getDotNestedToName(string $value): string
        {
            return $value;
        }
    };
});

it('default operator', function (): void {
    $this->showWhenTest->showWhen('field2', 1);

    $condition = $this->showWhenTest->getShowWhenCondition()[0];
    expect($condition['showField'])->toBe('field1')
        ->and($condition['changeField'])->toBe('field2')
        ->and($condition['operator'])->toBe('=')
        ->and($condition['value'])->toBe(1)
    ;
});

it('operator >', function (): void {
    $this->showWhenTest->showWhen('field2', '>', 1);

    $condition = $this->showWhenTest->getShowWhenCondition()[0];
    expect($condition['showField'])->toBe('field1')
        ->and($condition['changeField'])->toBe('field2')
        ->and($condition['operator'])->toBe('>')
        ->and($condition['value'])->toBe(1)
    ;
});

it('operator in', function (): void {
    $this->showWhenTest->showWhen('field2', 'in', [1,2]);

    $condition = $this->showWhenTest->getShowWhenCondition()[0];
    expect($condition['showField'])->toBe('field1')
        ->and($condition['changeField'])->toBe('field2')
        ->and($condition['operator'])->toBe('in')
        ->and($condition['value'])->toBe([1,2])
    ;
});

it('error operator', function (): void {
    $this->showWhenTest->showWhen('field2', '%', 1);

    $condition = $this->showWhenTest->getShowWhenCondition()[0];
    expect($condition['showField'])->toBe('field1')
        ->and($condition['changeField'])->toBe('field2')
        ->and($condition['operator'])->toBe('=')
        ->and($condition['value'])->toBe('%')
    ;
});

it('error operator in', function (): void {
    $this->showWhenTest->showWhen('field2', 'in', 1);
})->throws(InvalidArgumentException::class, 'Illegal operator and value combination. Value must be array type');

it('error null value', function (): void {
    $this->showWhenTest->showWhen('field2', '>', null);
})->throws(InvalidArgumentException::class, 'Illegal operator and value combination.');
