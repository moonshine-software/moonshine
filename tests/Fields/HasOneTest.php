<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Fields;

use Leeto\MoonShine\Fields\HasOne;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\TestCase;

class HasOneTest extends TestCase
{
    public function test_make()
    {
        $field = HasOne::make('Role');

        $this->assertEquals('role', $field->field());
        $this->assertEquals('role[]', $field->name());
        $this->assertEquals('role', $field->id());
        $this->assertEquals('role', $field->relation());
        $this->assertEquals('Role', $field->label());
    }

    public function test_fields()
    {
        $field = HasOne::make('Role')->fields([
            Text::make('Name')
        ]);

        $this->assertTrue($field->hasFields());

        foreach ($field->getFields() as $inner) {
            $this->assertInstanceOf(Text::class, $inner);

            $this->assertEquals('name', $inner->field());
            $this->assertEquals('role[name]', $inner->name());
            $this->assertEquals('role_name', $inner->id());
            $this->assertNull($inner->relation());
            $this->assertEquals('Name', $inner->label());
        }
    }
}
