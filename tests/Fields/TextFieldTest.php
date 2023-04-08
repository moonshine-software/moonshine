<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Fields;

use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\TestCase;

class TextFieldTest extends TestCase
{
    public function test_make()
    {
        $field = Text::make('First name');

        $this->assertEquals('first_name', $field->field());
        $this->assertEquals('first_name', $field->name());
        $this->assertEquals('first_name', $field->id());
        $this->assertNull($field->relation());
        $this->assertEquals('First name', $field->label());
        $this->assertEquals('text', $field->type());
    }
}
