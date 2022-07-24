<?php

namespace Leeto\MoonShine\Tests\Traits;

use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\Traits\Fields\HasBooleanCondition;

class HasBooleanConditionTraitTest extends TestCase
{
    public function test_execute_boolean_condition()
    {
        $testClass = new class {
            use HasBooleanCondition;

            public function test($condition, bool $default): bool
            {
                return $this->executeBooleanCondition($condition, $default);
            }
        };

        $tests = [
            ['args' => [true, false], 'exp' => true],
            ['args' => [false, true], 'exp' => false],
            ['args' => [null, true], 'exp' => true],
            ['args' => [null, false], 'exp' => false],
            ['args' => [fn() => true, false], 'exp' => true],
            ['args' => [fn() => false, true], 'exp' => false],
        ];

        foreach ($tests as $test) {
            $this->assertEquals($test['exp'], $testClass->test(...$test['args']));
        }
    }
}
