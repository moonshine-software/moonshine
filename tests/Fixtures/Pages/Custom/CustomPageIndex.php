<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\Custom;

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Preview;

class CustomPageIndex extends Page
{
    protected ?PageType $pageType = PageType::INDEX;

    protected function components(): array
    {
        return [
            Box::make([
                Preview::make('CustomPageIndex', formatted: static fn () => 'CustomPageIndex'),
                ActionButton::make('To Form', $this->getRouter()->getEndpoints()->toPage(FormPage::class, $this->getResource())),
                ActionButton::make('To Detail', $this->getRouter()->getEndpoints()->toPage(DetailPage::class, $this->getResource())),
            ]),
        ];
    }
}
