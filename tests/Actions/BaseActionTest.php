<?php

namespace Leeto\MoonShine\Tests\Actions;

use Leeto\MoonShine\Actions\ExportAction;
use Leeto\MoonShine\Tests\TestCase;

class BaseActionTest extends TestCase
{
    public function testExportAction()
    {
        $action = ExportAction::make('Export');

        $this->assertEquals('Export', $action->label());
    }
}