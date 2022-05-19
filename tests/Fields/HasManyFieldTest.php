<?php

namespace Leeto\MoonShine\Tests\Fields;

use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Fields\BelongsToMany;
use Leeto\MoonShine\Fields\HasMany;
use Leeto\MoonShine\Fields\HasOne;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\Json;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\TestCase;

class HasManyFieldTest extends TestCase
{
    public function testMakeField()
    {
        $field = HasMany::make('Roles');

        $this->assertEquals('roles', $field->field());
        $this->assertEquals('roles[]', $field->name());
        $this->assertEquals('roles', $field->id());
        $this->assertEquals('roles', $field->relation());
        $this->assertEquals('Roles', $field->label());
    }

    public function testFields()
    {
        $field = HasMany::make('Roles')->fields([
            Text::make('Name')
        ]);

        $this->assertTrue($field->hasFields());

        foreach ($field->getFields() as $inner) {
            $this->assertInstanceOf(Text::class, $inner);

            $this->assertTrue($inner->hasParent());
            $this->assertEquals($field, $inner->parent());

            $this->assertEquals('name', $inner->field());
            $this->assertEquals('roles[${index}][name]', $inner->name());
            $this->assertEquals('roles_name', $inner->id());
            $this->assertNull($inner->relation());
            $this->assertEquals('Name', $inner->label());
        }
    }

    public function testRemovable()
    {
        $field = HasMany::make('Names')->fields([
            Text::make('Name')
        ])->removable();

        $this->assertTrue($field->isRemovable());
    }
}