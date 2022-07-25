<?php

namespace Leeto\MoonShine\Tests\Utilities;

use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\Utilities\Helpers;

class HasBooleanConditionTraitTest extends TestCase
{
    public function test_execute_boolean_condition()
    {
        $tests = [
            ['args' => [true, false], 'exp' => true],
            ['args' => [false, true], 'exp' => false],
            ['args' => [null, true], 'exp' => true],
            ['args' => [null, false], 'exp' => false],
            ['args' => [fn() => true, false], 'exp' => true],
            ['args' => [fn() => false, true], 'exp' => false],
        ];

        foreach ($tests as $test) {
            $this->assertEquals($test['exp'], Helpers::executeBooleanCondition(...$test['args']));
        }
    }
}
