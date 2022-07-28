<?php
declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Decorations;

use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\TestCase;

class DecorationTest extends TestCase
{
    public function test_decoration_make_with_fields(): void
    {
        $decoration = Decoration::make('Label', [
            Text::make('First name'),
            Text::make('Last name'),
        ]);

        $this->assertEquals('Label', $decoration->label());
        $this->assertTrue($decoration->hasFields());
        $this->assertCount(2, $decoration->fields());
    }

    public function test_decoration_make_without_fields(): void
    {
        $decoration = Decoration::make('Label');

        $this->assertEquals('Label', $decoration->label());
        $this->assertFalse($decoration->hasFields());
        $this->assertCount(0, $decoration->fields());
    }

    public function test_decoration_id(): void
    {
        $decoration = Decoration::make('Label', [
            Text::make('First name'),
            Text::make('Last name'),
        ]);

        $this->assertEquals(str('label')->slug(), $decoration->id());
    }

    public function test_decoration_name(): void
    {
        $decoration = Decoration::make('Label', [
            Text::make('First name'),
            Text::make('Last name'),
        ]);

        $this->assertEquals(str('Label')->slug(), $decoration->name());
    }
}
