<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\Laravel\Traits\WithComponentsPusher;
use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\{Block, Body, Html, LayoutBuilder};

final class LoginLayout extends BaseLayout
{
    use WithComponentsPusher;

    public function build(): LayoutBuilder
    {
        return LayoutBuilder::make([
            Html::make([
                $this->getHeadComponent(),
                Body::make([
                    Block::make([
                        Block::make([
                            $this->getLogoComponent(),
                        ])->class('authentication-logo'),

                        Block::make([
                            Block::make([
                                Heading::make(
                                    __(
                                        'moonshine::ui.login.title',
                                        ['moonshine_title' => moonshineConfig()->getTitle()]
                                    )
                                ),
                                Block::make([
                                    FlexibleRender::make(
                                        __('moonshine::ui.login.description')
                                    ),
                                ])->class('description'),
                            ])->class('authentication-header'),

                            Components::make($this->getPage()->getComponents()),
                        ])->class('authentication-content'),

                        ...$this->getPushedComponents(),
                    ])->class('authentication'),
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
