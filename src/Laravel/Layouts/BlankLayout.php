<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\Core\Pages\Page;
use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\Layout\Body;
use MoonShine\UI\Components\Layout\Head;
use MoonShine\UI\Components\Layout\Html;
use MoonShine\UI\Components\Layout\LayoutBuilder;
use MoonShine\UI\MoonShineLayout;

final class BlankLayout extends MoonShineLayout
{
    public function build(Page $page): LayoutBuilder
    {
        return LayoutBuilder::make([
            Html::make([
                Head::make(),
                Body::make([
                    Components::make($page->getComponents()),
                ]),
            ])->withAlpineJs()->withThemes(),
        ]);
    }
}
