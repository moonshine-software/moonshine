<?php

declare(strict_types=1);

namespace MoonShine\Tests\Decorations;

use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Tests\TestCase;

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
