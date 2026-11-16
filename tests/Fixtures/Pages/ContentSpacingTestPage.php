<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages;

use MoonShine\Laravel\Pages\Dashboard;
use MoonShine\Laravel\Traits\Page\WithContentSpacing;
use MoonShine\UI\Components\Alert;
use MoonShine\UI\Components\FlexibleRender;

class ContentSpacingTestPage extends Dashboard
{
    protected string $title = 'Page title';

    protected string $subtitle = 'Page subtitle';

    protected function components(): iterable
    {
        return [
            FlexibleRender::make('<p>toolbar</p>'),
            Alert::make('information-circle', 'info')->content('Notice'),
        ];
    }
}

final class ContentSpacingTestPageWithTrait extends ContentSpacingTestPage
{
    use WithContentSpacing;
}
