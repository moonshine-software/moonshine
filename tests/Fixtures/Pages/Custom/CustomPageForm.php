<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\Custom;

use MoonShine\Decorations\Block;
use MoonShine\Enums\PageType;
use MoonShine\Fields\Preview;
use MoonShine\Pages\Page;

class CustomPageForm extends Page
{
    protected ?PageType $pageType = PageType::FORM;

    public function components(): array
    {
        return [
            Block::make([
                Preview::make('CustomPageForm', formatted: fn () => 'CustomPageForm'),
            ]),
        ];
    }
}
