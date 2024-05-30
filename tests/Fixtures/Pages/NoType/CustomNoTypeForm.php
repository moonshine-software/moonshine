<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\NoType;

use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Preview;

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
