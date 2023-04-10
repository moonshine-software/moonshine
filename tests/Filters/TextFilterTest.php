<?php

declare(strict_types=1);

namespace MoonShine\Tests\Filters;

use MoonShine\Filters\TextFilter;
use MoonShine\Tests\TestCase;

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
