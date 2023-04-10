<?php

declare(strict_types=1);

namespace MoonShine\Tests\Decorations;

use MoonShine\Decorations\Decoration;
use MoonShine\Fields\Text;
use MoonShine\Tests\TestCase;
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
        $this->assertCount(2, $decoration->getFields());
    }

    public function test_decoration_make_without_fields(): void
    {
        /** @var Decoration $decoration */
        $decoration = $this->createDecorationMockObject('Label');

        $this->assertFalse($decoration->hasFields());
        $this->assertCount(0, $decoration->getFields());
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
