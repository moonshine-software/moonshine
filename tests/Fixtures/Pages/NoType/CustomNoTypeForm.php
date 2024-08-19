<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\NoType;

use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Preview;

class CustomNoTypeForm extends Page
{
    protected function components(): iterable
    {
        return [
            Box::make([
                Preview::make('CustomNoTypeForm', formatted: static fn () => 'CustomNoTypeForm'),
            ]),
        ];
    }
}
