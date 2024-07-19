<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\Layout\{Assets, Body, Favicon, Head, Html, LayoutBuilder, Meta};
use MoonShine\UI\Layout;

final class BlankLayout extends Layout
{
    public function build(): LayoutBuilder
    {
        return LayoutBuilder::make([
            Html::make([
                Head::make([
                    Meta::make()->customAttributes([
                        'name' => 'csrf-token',
                        'content' => csrf_token(),
                    ]),
                    Favicon::make(),
                    Assets::make(),
                ]),
                Body::make([
                    Components::make($this->getPage()->getComponents()),
                ]),
            ])
                ->customAttributes([
                    'lang' => str_replace('_', '-', app()->getLocale()),
                ])
                ->withAlpineJs()
                ->withThemes(),
        ]);
    }
}
