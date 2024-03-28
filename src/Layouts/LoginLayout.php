<?php

declare(strict_types=1);

namespace MoonShine\Layouts;

use MoonShine\Components\Components;
use MoonShine\Components\FlexibleRender;
use MoonShine\Components\Layout\Body;
use MoonShine\Components\Layout\Head;
use MoonShine\Components\Layout\Html;
use MoonShine\Components\Layout\Block;
use MoonShine\Components\Layout\LayoutBuilder;
use MoonShine\Decorations\Heading;
use MoonShine\MoonShineLayout;
use MoonShine\Pages\Page;

use const _PHPStan_8b6260c21\__;

final class LoginLayout extends MoonShineLayout
{
    public function build(Page $page): LayoutBuilder
    {
        $logo = asset(config('moonshine.logo') ?? 'vendor/moonshine/logo.svg');
        $title = __('moonshine::ui.login.title', ['moonshine_title' => config('moonshine.title')]);
        $description = __('moonshine::ui.login.description');

        return LayoutBuilder::make([
            Html::make([
                Head::make(),
                Body::make([
                    Block::make([
                        Block::make([
                            FlexibleRender::make(view('moonshine::layouts.shared.logo'))
                        ])->class('authentication-logo'),

                        Block::make([
                            Block::make([
                                Heading::make($title),
                                Block::make([
                                    FlexibleRender::make($description)
                                ])->class('description')
                            ])->class('authentication-header'),

                            Components::make($page->getComponents()),
                        ])->class('authentication-content'),


                    ])->class('authentication')
                ])
            ])->withAlpineJs()->withThemes(),
        ]);
    }
}
