<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Decorations;

use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Tests\TestCase;

class TabTest extends TestCase
{
    public function test_tab_get_view(): void
    {
        $decoration = Tab::make('Tab');

        $this->assertEquals('moonshine::decorations.tab', $decoration->getView());
    }
}
