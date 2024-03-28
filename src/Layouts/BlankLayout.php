<?php

declare(strict_types=1);

namespace MoonShine\Layouts;

use MoonShine\Components\Components;
use MoonShine\Components\FlexibleRender;
use MoonShine\Components\Layout\Block;
use MoonShine\Components\Layout\Body;
use MoonShine\Components\Layout\Head;
use MoonShine\Components\Layout\Html;
use MoonShine\Components\Layout\LayoutBuilder;
use MoonShine\Decorations\Heading;
use MoonShine\MoonShineLayout;
use MoonShine\Pages\Page;

final class BlankLayout extends MoonShineLayout
{
    public function build(Page $page): LayoutBuilder
    {
        return LayoutBuilder::make([
            Html::make([
                Head::make(),
                Body::make([
                    Components::make($page->getComponents()),
                ])
            ])->withAlpineJs()->withThemes(),
        ]);
    }
}
