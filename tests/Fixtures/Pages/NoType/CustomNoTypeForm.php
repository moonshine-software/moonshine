<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\NoType;

use MoonShine\Components\Layout\Box;
use MoonShine\Fields\Preview;
use MoonShine\Pages\Page;

class CustomNoTypeForm extends Page
{
    public function components(): array
    {
        return [
            Box::make([
                Preview::make('CustomNoTypeForm', formatted: fn () => 'CustomNoTypeForm'),
            ]),
        ];
    }
}
