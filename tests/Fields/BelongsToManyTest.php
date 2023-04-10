<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fields;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MoonShine\Fields\BelongsToMany;
use MoonShine\Fields\Text;
use MoonShine\Tests\TestCase;

class BelongsToManyTest extends TestCase
{
    use RefreshDatabase;

    public function test_make()
    {
        $field = BelongsToMany::make('Role', 'roles');

        $this->assertEquals('roles', $field->field());
        $this->assertEquals('roles[]', $field->name());
        $this->assertEquals('roles', $field->id());
        $this->assertEquals('roles', $field->relation());
        $this->assertEquals('Role', $field->label());
    }

    public function test_fields()
    {
        $field = BelongsToMany::make('Role', 'roles')->fields([
            Text::make('Pivot'),
        ]);

        $this->assertTrue($field->hasFields());

        foreach ($field->getFields() as $inner) {
            $this->assertInstanceOf(Text::class, $inner);

            $this->assertEquals('pivot', $inner->field());
            $this->assertEquals('roles_pivot[]', $inner->name());
            $this->assertEquals('roles_pivot', $inner->id());
            $this->assertNull($inner->relation());
            $this->assertEquals('Pivot', $inner->label());
        }
    }
}
