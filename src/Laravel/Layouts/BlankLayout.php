<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\UI\Components\Components;
use MoonShine\UI\MoonShineLayout;
use MoonShine\UI\Components\Layout\{Body, Head, Html, LayoutBuilder};

final class BlankLayout extends MoonShineLayout
{
    public function build(): LayoutBuilder
    {
        return LayoutBuilder::make([
            Html::make([
                Head::make(),
                Body::make([
                    Components::make($this->getPage()->getComponents()),
                ]),
            ])->withAlpineJs()->withThemes(),
        ]);
    }
}
