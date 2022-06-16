<?php

namespace Leeto\MoonShine\Tests\Fields;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Leeto\MoonShine\Fields\BelongsToMany;
use Leeto\MoonShine\Tests\TestCase;

class BelongsToManyTest extends TestCase
{
    use RefreshDatabase;

    public function test_make()
    {
        $field = BelongsToMany::make('Role', 'admin_role_id');

        $this->assertEquals('admin_role_id', $field->field());
        $this->assertEquals('admin_role_id[]', $field->name());
        $this->assertEquals('admin_role_id', $field->id());
        $this->assertEquals('adminRole', $field->relation());
        $this->assertEquals('Role', $field->label());
    }
}