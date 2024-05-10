<?php

declare(strict_types=1);

namespace MoonShine\Layouts;

use MoonShine\Components\Components;
use MoonShine\Components\FlexibleRender;
use MoonShine\Components\Heading;
use MoonShine\Components\Layout\Block;
use MoonShine\Components\Layout\Body;
use MoonShine\Components\Layout\Head;
use MoonShine\Components\Layout\Html;
use MoonShine\Components\Layout\LayoutBuilder;
use MoonShine\Components\Layout\Logo;
use MoonShine\Components\SocialAuth;
use MoonShine\MoonShineLayout;
use MoonShine\Pages\Page;

final class LoginLayout extends MoonShineLayout
{
    public function build(Page $page): LayoutBuilder
    {
        $logo = asset('vendor/moonshine/logo.svg');

        $title = __('moonshine::ui.login.title', ['moonshine_title' => moonshineConfig()->getTitle()]);
        $description = __('moonshine::ui.login.description');

        return LayoutBuilder::make([
            Html::make([
                Head::make(),
                Body::make([
                    Block::make([
                        Block::make([
                            Logo::make(href: moonshineRouter()->home(), logo: $logo),
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
