<?php

namespace Leeto\MoonShine\Tests\Helpers;

use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\Helpers\Condition;

class ConditionTest extends TestCase
{
    /**
     * @dataProvider parametersProvider
     */
    public function test_boolean(array $arguments, bool $expected)
    {
        $this->assertEquals($expected, Condition::boolean(...$arguments));
    }

    public function parametersProvider(): array
    {
        return [
            [[true, false], true],
            [[false, true], false],
            [[null, true], true],
            [[null, false], false],
            [[fn() => true, false], true],
            [[fn() => false, true], false],
            [[[], true], false],
            [[0, true], false],
            [[new \stdClass(), false], true],
            [['', true], false],
            [['qwerty', false], true],
            [[2, false], true],
        ];
    }
}
