<?php

declare(strict_types=1);

namespace MoonShine\Tests\Decorations;

use MoonShine\Decorations\Heading;
use MoonShine\Tests\TestCase;

class HeadingTest extends TestCase
{
    public function test_heading_get_view(): void
    {
        $decoration = Heading::make('Heading');

        $this->assertEquals('moonshine::decorations.heading', $decoration->getView());
    }
}
