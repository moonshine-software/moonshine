<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\Custom;

use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Preview;

class CustomPageForm extends Page
{
    protected ?PageType $pageType = PageType::FORM;

    protected function components(): iterable
    {
        return [
            Box::make([
                Preview::make('CustomPageForm', formatted: static fn () => 'CustomPageForm'),
            ]),
        ];
    }
}
