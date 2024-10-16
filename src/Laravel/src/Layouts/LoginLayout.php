<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Layouts;

use MoonShine\Laravel\Traits\WithComponentsPusher;
use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\{Block, Body, Html, Layout};

final class LoginLayout extends BaseLayout
{
    use WithComponentsPusher;

    protected ?string $title = null;

    protected ?string $description = null;

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title ?? __(
            'moonshine::ui.login.title',
            ['moonshine_title' => moonshineConfig()->getTitle()],
        );
    }

    public function getDescription(): string
    {
        return $this->description ?? __('moonshine::ui.login.description');
    }

    public function build(): Layout
    {
        return Layout::make([
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
                                    $this->getTitle(),
                                ),
                                Block::make([
                                    FlexibleRender::make(
                                        $this->getDescription(),
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
