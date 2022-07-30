<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Actions;

use Leeto\MoonShine\Actions\ExportAction;
use Leeto\MoonShine\Tests\TestCase;

class ExportActionTest extends TestCase
{
    public function test_make()
    {
        $action = ExportAction::make('Export');

        $this->assertEquals('Export', $action->label());
    }
}
