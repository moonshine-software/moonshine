<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\Layout\{Body, Html, Layout};

final class BlankLayout extends BaseLayout
{
    public function build(): Layout
    {
        return Layout::make([
            Html::make([
                $this->getHeadComponent(withAssetsFragment: false),
                Body::make([
                    Components::make($this->getPage()->getComponents()),
                ]),
            ])
                ->customAttributes([
                    'lang' => $this->getHeadLang(),
                ])
                ->withAlpineJs()
                ->when(
                    $this->hasThemes() || $this->isAlwaysDark(),
                    fn (Html $html): Html => $html->withThemes($this->isAlwaysDark())
                ),
        ]);
    }
}
