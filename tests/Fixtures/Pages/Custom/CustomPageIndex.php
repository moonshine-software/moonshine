<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\Custom;

use MoonShine\Components\ActionButtons\ActionButton;
use MoonShine\Components\Layout\Box;
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
            Box::make([
                Preview::make('CustomPageIndex', formatted: fn () => 'CustomPageIndex'),
                ActionButton::make('To Form', toPage(FormPage::class, $this->getResource())),
                ActionButton::make('To Detail', toPage(DetailPage::class, $this->getResource())),
            ]),
        ];
    }
}
