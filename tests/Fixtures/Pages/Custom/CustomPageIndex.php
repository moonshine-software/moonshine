<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\Custom;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Decorations\Block;
use MoonShine\Enums\PageType;
use MoonShine\Fields\Preview;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Page;

class CustomPageIndex extends Page
{
    protected ?PageType $pageType = PageType::INDEX;

    public function components(): array
    {
        return [
            Block::make([
                Preview::make('CustomPageIndex', formatted: fn() => 'CustomPageIndex'),
                ActionButton::make('To Form', to_page(FormPage::class, $this->getResource())),
                ActionButton::make('To Detail', to_page(DetailPage::class, $this->getResource())),
            ]),
        ];
    }
}