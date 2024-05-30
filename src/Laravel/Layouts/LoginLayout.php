<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\Core\Contracts\PageContract;
use MoonShine\Laravel\Components\SocialAuth;
use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Block;
use MoonShine\UI\Components\Layout\Body;
use MoonShine\UI\Components\Layout\Head;
use MoonShine\UI\Components\Layout\Html;
use MoonShine\UI\Components\Layout\LayoutBuilder;
use MoonShine\UI\Components\Layout\Logo;
use MoonShine\UI\MoonShineLayout;

final class LoginLayout extends MoonShineLayout
{
    public function build(PageContract $page): LayoutBuilder
    {
        $logo = moonshineAssets()->asset('vendor/moonshine/logo.svg');

        $title = __('moonshine::ui.login.title', ['moonshine_title' => moonshineConfig()->getTitle()]);
        $description = __('moonshine::ui.login.description');

        return LayoutBuilder::make([
            Html::make([
                Head::make(),
                Body::make([
                    Block::make([
                        Block::make([
                            Logo::make(href: moonshineRouter()->getEndpoints()->home(), logo: $logo),
                        ])->class('authentication-logo'),

                        Block::make([
                            Block::make([
                                Heading::make($title),
                                Block::make([
                                    FlexibleRender::make($description),
                                ])->class('description'),
                            ])->class('authentication-header'),

                            Components::make($page->getComponents()),
                        ])->class('authentication-content'),

                        SocialAuth::make(),
                    ])->class('authentication'),
                ]),
            ])->withAlpineJs()->withThemes(),
        ]);
    }
}
