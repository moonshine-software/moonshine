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

class BaseFieldTest extends TestCase
{
    public function testBasicTextField()
    {
        $field = Text::make('First name');

        $this->assertEquals('first_name', $field->field());
        $this->assertEquals('first_name', $field->name());
        $this->assertEquals('first_name', $field->id());
        $this->assertNull($field->relation());
        $this->assertEquals('First name', $field->label());
    }

    public function testBasicBelongToField()
    {
        $field = BelongsTo::make('Admin Role');

        $this->assertEquals('admin_role_id', $field->field());
        $this->assertEquals('admin_role_id', $field->name());
        $this->assertEquals('admin_role_id', $field->id());
        $this->assertEquals('adminRole', $field->relation());
        $this->assertEquals('Admin Role', $field->label());
    }

    public function testBasicBelongToManyField()
    {
        $field = BelongsToMany::make('Admin Role');

        $this->assertEquals('adminRole', $field->field());
        $this->assertEquals('adminRole[]', $field->name());
        $this->assertEquals('admin_role', $field->id());
        $this->assertEquals('adminRole', $field->relation());
        $this->assertEquals('Admin Role', $field->label());
    }

    public function testBasicHasManyField()
    {
        $field = HasMany::make('Admin Roles');

        $this->assertEquals('adminRoles', $field->field());
        $this->assertEquals('adminRoles[]', $field->name());
        $this->assertEquals('admin_roles', $field->id());
        $this->assertEquals('adminRoles', $field->relation());
        $this->assertEquals('Admin Roles', $field->label());

        $field = HasMany::make('Admin Roles', 'custom_field');
        $this->assertEquals('custom_field', $field->field());
    }

    public function testBasicHasOneField()
    {
        $field = HasOne::make('Country');

        $this->assertEquals('country', $field->field());
        $this->assertEquals('country', $field->name());
        $this->assertEquals('country', $field->id());
        $this->assertEquals('country', $field->relation());
        $this->assertEquals('Country', $field->label());
    }

    public function testBasicJsonField()
    {
        $field = Json::make('Country')->fields([
            ID::make(),
            Text::make('Value')
        ]);

        $this->assertEquals('country', $field->field());
        $this->assertEquals('country', $field->name());
        $this->assertEquals('country', $field->id());
        $this->assertNull($field->relation());
        $this->assertEquals('Country', $field->label());
    }
}