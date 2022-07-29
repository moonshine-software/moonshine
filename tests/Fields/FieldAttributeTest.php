<?php

namespace Leeto\MoonShine\Tests\Fields;

use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\TestCase;

class FieldAttributeTest extends TestCase
{
    public function test_empty()
    {
        $field = Text::make('Name');

        $this->assertEmpty($field->attributes()->get('class'));
        $this->assertNull($field->attributes()->get('autocomplete'));
    }

    public function test_class()
    {
        $field = Text::make('Name')
            ->customClasses(['one', 'two']);

        $this->assertEquals('one two', $field->attributes()->get('class'));
    }

    public function test_custom_attributes()
    {
        $field = Text::make('Name')
            ->customAttributes([
                'accept' => 'accept',
                'data-value' => 'test'
            ])
            ->customClasses(['one', 'two']);

        $this->assertEquals('one two', $field->attributes()->get('class'));
        $this->assertEquals('test', $field->attributes()->get('data-value'));
        $this->assertEquals('accept', $field->attributes()->get('accept'));
    }
}
