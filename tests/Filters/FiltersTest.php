<?php

declare(strict_types=1);

namespace MoonShine\Tests\Filters;

use MoonShine\Filters\BelongsToFilter;
use MoonShine\Filters\TextFilter;
use MoonShine\Tests\TestCase;

class FiltersTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_find_filter_by_column(): void
    {
        $this->assertInstanceOf(
            TextFilter::class,
            $this->testResource()->getFilters()->findFilterByColumn('name')
        );

        $this->assertInstanceOf(
            BelongsToFilter::class,
            $this->testResource()->getFilters()->findFilterByColumn('undefined', BelongsToFilter::make('default'))
        );
    }
}
