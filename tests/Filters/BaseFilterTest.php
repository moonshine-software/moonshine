<?php

namespace Leeto\MoonShine\Tests\Filters;

use Leeto\MoonShine\Filters\BelongsToFilter;
use Leeto\MoonShine\Filters\BelongsToManyFilter;
use Leeto\MoonShine\Filters\TextFilter;
use PHPUnit\Framework\TestCase;

class BaseFilterTest extends TestCase
{
    public function testBasicTextFilter()
    {
        $filter = TextFilter::make('First name');

        $this->assertEquals('first_name', $filter->field());
        $this->assertEquals('filters[first_name]', $filter->name());
        $this->assertEquals('filtersfirst_name', $filter->id());
        $this->assertEquals('', $filter->relation());
        $this->assertEquals('First name', $filter->label());
    }

    public function testBasicBelongToField()
    {
        $field = BelongsToFilter::make('Admin Role');

        $this->assertEquals('admin_role_id', $field->field());
        $this->assertEquals('filters[admin_role_id]', $field->name());
        $this->assertEquals('filtersadmin_role_id', $field->id());
        $this->assertEquals('adminRole', $field->relation());
        $this->assertEquals('Admin Role', $field->label());
    }

    public function testBasicBelongToManyField()
    {
        $field = BelongsToManyFilter::make('Admin Role');

        $this->assertEquals('adminRole', $field->field());
        $this->assertEquals('filters[adminRole][]', $field->name());
        $this->assertEquals('filtersadmin_role', $field->id());
        $this->assertEquals('adminRole', $field->relation());
        $this->assertEquals('Admin Role', $field->label());
    }
}