<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\Custom;

use MoonShine\Core\Pages\Page;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Preview;

class CustomPageDetail extends Page
{
    protected ?PageType $pageType = PageType::DETAIL;

    public function components(): array
    {
        return [
            Box::make([
                Preview::make('CustomPageDetail', formatted: fn () => 'CustomPageDetail'),
            ]),
        ];
    }
}
