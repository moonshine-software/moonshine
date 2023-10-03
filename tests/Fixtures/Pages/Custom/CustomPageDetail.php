<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\Custom;

use MoonShine\Decorations\Block;
use MoonShine\Enums\PageType;
use MoonShine\Fields\Preview;
use MoonShine\Pages\Page;

class CustomPageDetail extends Page
{
    protected ?PageType $pageType = PageType::DETAIL;

    public function components(): array
    {
        return [
            Block::make([
                Preview::make('CustomPageDetail', formatted: fn() => 'CustomPageDetail')
            ]),
        ];
    }
}