<?php

namespace Leeto\MoonShine\Tests\Helpers;

use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\Helpers\Conditions;

class ConditionsTest extends TestCase
{
    public function test_boolean()
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
            $this->assertEquals($test['exp'], Conditions::boolean(...$test['args']));
        }
    }
}
