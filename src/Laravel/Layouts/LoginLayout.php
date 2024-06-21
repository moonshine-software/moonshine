<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\Laravel\Components\SocialAuth;
use MoonShine\UI\MoonShineLayout;
use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\{Block, Body, Head, Html, LayoutBuilder, Logo};

final class LoginLayout extends MoonShineLayout
{
    public function build(): LayoutBuilder
    {
        $logo = moonshineAssets()->getAsset('vendor/moonshine/logo.svg');

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

                            Components::make($this->getPage()->getComponents()),
                        ])->class('authentication-content'),

                        SocialAuth::make(),
                    ])->class('authentication'),
                ]),
            ])->withAlpineJs()->withThemes(),
        ]);
    }
}
