<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Decorations;

use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Decorations\Tabs;
use Leeto\MoonShine\Tests\TestCase;

class TabsTest extends TestCase
{
    public function test_tab_get_view(): void
    {
        $decoration = Tabs::make([
            Tab::make('Tab'),
        ]);

        $this->assertEquals('moonshine::decorations.tabs', $decoration->getView());
    }
}
