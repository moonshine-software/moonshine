<?php

declare(strict_types=1);

namespace MoonShine\Tests\Actions;

use MoonShine\Actions\ExportAction;
use MoonShine\Tests\TestCase;

class ExportActionTest extends TestCase
{
    public function test_make()
    {
        $action = ExportAction::make('Export');

        $this->assertEquals('Export', $action->label());
    }
}
