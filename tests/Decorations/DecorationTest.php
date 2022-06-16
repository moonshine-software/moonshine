<?php

namespace Leeto\MoonShine\Tests\Decorations;

use Leeto\MoonShine\Decorations\Heading;
use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\TestCase;

class DecorationTest extends TestCase
{
    public function test_tab()
    {
        $decoration = Tab::make('Tab', [
            Text::make('First name'),
            Text::make('Last name'),
        ]);

        $this->assertEquals('Tab', $decoration->label());
        $this->assertTrue($decoration->hasFields());
        $this->assertCount(2, $decoration->fields());
    }

    public function test_heading()
    {
        $decoration = Heading::make('h1');

        $this->assertEquals('h1', $decoration->label());
    }
}