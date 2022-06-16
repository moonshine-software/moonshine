<?php

namespace Leeto\MoonShine\Tests\Filters;

use Leeto\MoonShine\Filters\BelongsToFilter;
use Leeto\MoonShine\Filters\BelongsToManyFilter;
use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\Tests\TestCase;

class BelongsToFilterTest extends TestCase
{
    public function test_basic_text()
    {
        $filter = TextFilter::make('First name');

        $this->assertEquals('first_name', $filter->field());
        $this->assertEquals('filters[first_name]', $filter->name());
        $this->assertEquals('filters_first_name', $filter->id());
        $this->assertEquals('', $filter->relation());
        $this->assertEquals('First name', $filter->label());
    }

    public function test_basic_belong_to()
    {
        $field = BelongsToFilter::make('Admin Role');

        $this->assertEquals('admin_role_id', $field->field());
        $this->assertEquals('filters[admin_role_id]', $field->name());
        $this->assertEquals('filters_admin_role_id', $field->id());
        $this->assertEquals('adminRole', $field->relation());
        $this->assertEquals('Admin Role', $field->label());
    }

    public function test_basic_belongs_to_many()
    {
        $field = BelongsToManyFilter::make('Admin Role');

        $this->assertEquals('adminRole', $field->field());
        $this->assertEquals('filters[adminRole][]', $field->name());
        $this->assertEquals('filters_admin_role', $field->id());
        $this->assertEquals('adminRole', $field->relation());
        $this->assertEquals('Admin Role', $field->label());
    }
}