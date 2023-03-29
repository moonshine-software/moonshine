<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Filters;

use Leeto\MoonShine\Actions\ExportAction;
use Leeto\MoonShine\Actions\ImportAction;
use Leeto\MoonShine\Filters\BelongsToFilter;
use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\Tests\TestCase;

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
