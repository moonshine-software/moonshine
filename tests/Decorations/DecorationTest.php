<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Decorations;

use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DecorationTest extends TestCase
{
    public function test_decoration_make_with_fields(): void
    {
        /** @var Decoration $decoration */
        $decoration = $this->createDecorationMockObject('Label', [
            Text::make('First name'),
            Text::make('Last name'),
        ]);
        $this->assertTrue($decoration->hasFields());
        $this->assertCount(2, $decoration->fields());
    }

    public function test_decoration_make_without_fields(): void
    {
        /** @var Decoration $decoration */
        $decoration = $this->createDecorationMockObject('Label');

        $this->assertFalse($decoration->hasFields());
        $this->assertCount(0, $decoration->fields());
    }

    public function test_decoration_id(): void
    {
        /** @var Decoration $decoration */
        $decoration = $this->createDecorationMockObject('Label', [
            Text::make('First name'),
            Text::make('Last name'),
        ]);

        $this->assertEquals(str('Label')->slug(), $decoration->id());
    }

    public function test_decoration_name(): void
    {
        /** @var Decoration $decoration */
        $decoration = $this->createDecorationMockObject('Label', [
            Text::make('First name'),
            Text::make('Last name'),
        ]);

        $this->assertEquals(str('Label')->slug(), $decoration->name());
    }

    public function test_decoration_label(): void
    {
        /** @var Decoration $decoration */
        $decoration = $this->createDecorationMockObject('Label', [
            Text::make('First name'),
            Text::make('Last name'),
        ]);

        $this->assertEquals('Label', $decoration->label());
    }

    private function createDecorationMockObject(string $label, array $fields = []): MockObject
    {
        return $this->getMockForAbstractClass(
            Decoration::class,
            [
                $label,
                $fields,
            ],
        );
    }
}
