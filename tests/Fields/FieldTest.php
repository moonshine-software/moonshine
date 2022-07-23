<?php

namespace Leeto\MoonShine\Tests\Fields;

use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\TestCase;

class FieldTest extends TestCase
{
    public function test_execute_boolean_condition()
    {
        $field = Text::make('Test');
        $reflection = new \ReflectionClass(get_class($field));
        $method = $reflection->getMethod('executeBooleanCondition');
        $method->setAccessible(true);

        $tests = [
            ['args' => [true, false], 'exp' => true],
            ['args' => [false, true], 'exp' => false],
            ['args' => ['true', false], 'exp' => true],
            ['args' => ['false', true], 'exp' => false],
            ['args' => ['1', false], 'exp' => true],
            ['args' => ['0', true], 'exp' => false],
            ['args' => [null, true], 'exp' => true],
            ['args' => [null, false], 'exp' => false],
            ['args' => [fn() => true, false], 'exp' => true],
            ['args' => [fn() => false, true], 'exp' => false],
        ];

        foreach ($tests as $test) {
            $this->assertEquals($test['exp'], $method->invokeArgs($field, $test['args']));
        }
    }
}
