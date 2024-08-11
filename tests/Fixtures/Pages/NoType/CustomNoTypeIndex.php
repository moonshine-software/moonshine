<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\NoType;

use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Preview;

class CustomNoTypeIndex extends Page
{
    protected function components(): array
    {
        return [
            Box::make([
                Preview::make('CustomNoTypeIndex', formatted: static fn () => 'CustomNoTypeIndex'),
            ]),
        ];
    }
}
