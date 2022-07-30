<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Decorations;

use Leeto\MoonShine\Decorations\Heading;
use Leeto\MoonShine\Tests\TestCase;

class HeadingTest extends TestCase
{
    public function test_heading_get_view(): void
    {
        $decoration = Heading::make('Heading');

        $this->assertEquals('moonshine::decorations.heading', $decoration->getView());
    }
}
