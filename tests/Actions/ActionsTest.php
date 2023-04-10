<?php

declare(strict_types=1);

namespace MoonShine\Tests\Actions;

use MoonShine\Actions\ExportAction;
use MoonShine\Actions\ImportAction;
use MoonShine\Tests\TestCase;

class ActionsTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_only_visible(): void
    {
        $this->assertEquals(2, $this->testResource()->getActions()->count());
        $this->assertEquals(2, $this->testResource()->getActions()->onlyVisible()->count());
    }

    /**
     * @test
     * @return void
     */
    public function it_merge_if_not_exists(): void
    {
        $this->assertEquals(2, $this->testResource()->getActions()->count());
        $this->assertEquals(
            2,
            $this->testResource()
                ->getActions()
                ->mergeIfNotExists(ExportAction::make('Export'))
                ->count()
        );
        $this->assertEquals(
            3,
            $this->testResource()
                ->getActions()
                ->mergeIfNotExists(ImportAction::make('Import'))
                ->count()
        );
    }
}
