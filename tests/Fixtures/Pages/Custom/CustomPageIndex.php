<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\Custom;

use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Preview;

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
