<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Unit\Helpers;

use Leeto\MoonShine\Helpers\Condition;
use Leeto\MoonShine\Tests\TestCase;

class ConditionTest extends TestCase
{
    /**
     * @dataProvider parametersProvider
     */
    public function test_boolean(array $arguments, bool $expected): void
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
        ];
    }
}
