<?php

namespace Leeto\MoonShine\Tests\Filters;

use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\Tests\TestCase;

class TextFilterTest extends TestCase
{
    public function test_make()
    {
        $filter = TextFilter::make('First name');

        $this->assertEquals('first_name', $filter->field());
        $this->assertEquals('filters[first_name]', $filter->name());
        $this->assertEquals('filters_first_name', $filter->id());
        $this->assertEquals('', $filter->relation());
        $this->assertEquals('First name', $filter->label());
    }
}