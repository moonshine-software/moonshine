<?php

namespace Leeto\MoonShine\Tests\Filters;

use Leeto\MoonShine\Filters\BelongsToManyFilter;
use Leeto\MoonShine\Tests\TestCase;

class BelongsToManyFilterTest extends TestCase
{
    public function test_make()
    {
        $field = BelongsToManyFilter::make('Admin Role');

        $this->assertEquals('adminRole', $field->field());
        $this->assertEquals('filters[adminRole][]', $field->name());
        $this->assertEquals('filters_admin_role', $field->id());
        $this->assertEquals('adminRole', $field->relation());
        $this->assertEquals('Admin Role', $field->label());
    }
}