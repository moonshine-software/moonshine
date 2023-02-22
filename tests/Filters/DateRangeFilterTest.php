<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Filters;

use Leeto\MoonShine\Filters\DateRangeFilter;
use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\Tests\TestCase;

class DateRangeFilterTest extends TestCase
{
    public function test_make()
    {
        $filter = DateRangeFilter::make('Date');

        $this->assertEquals('date', $filter->field());
        $this->assertEquals('filters[date][]', $filter->name());
        $this->assertEquals('filters_date', $filter->id());
        $this->assertEquals('', $filter->relation());
        $this->assertEquals('Date', $filter->label());
    }
}
