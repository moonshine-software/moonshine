<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\Layout\{Body, Html, LayoutBuilder};

final class BlankLayout extends BaseLayout
{
    public function build(): LayoutBuilder
    {
        return LayoutBuilder::make([
            Html::make([
                $this->getHeadComponent(),
                Body::make([
                    Components::make($this->getPage()->getComponents()),
                ]),
            ])
                ->customAttributes([
                    'lang' => $this->getHeadLang(),
                ])
                ->withAlpineJs()
                ->withThemes(),
        ]);
    }
}
