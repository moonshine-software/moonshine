<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\NoType;

use MoonShine\Decorations\Box;
use MoonShine\Fields\Preview;
use MoonShine\Pages\Page;

class CustomNoTypeIndex extends Page
{
    public function components(): array
    {
        return [
            Box::make([
                Preview::make('CustomNoTypeIndex', formatted: fn () => 'CustomNoTypeIndex'),
            ]),
        ];
    }
}
